<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\VehicleType;
use Modules\Taxido\Models\RentalVehicle;
use Illuminate\Database\Eloquent\Builder;
use Modules\Taxido\Enums\ServiceCategoryEnum;
use Modules\Taxido\Enums\RequestEnum;
use Modules\Taxido\Models\ServiceCategory;
use Modules\Taxido\Models\VehicleTypeZone;
use Modules\Taxido\Http\Traits\BiddingTrait;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Modules\Taxido\Http\Resources\Riders\VehicleTypeResource;

class VehicleTypeRepository extends BaseRepository
{
    use BiddingTrait;

    protected $fieldSearchable = [
        'name' => 'like',
    ];

    function model()
    {
        return VehicleType::class;
    }

    public function boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));

        } catch (ExceptionHandler $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        try {

            return $this->model->findOrFail($id);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function getVehicleTypeByLocations($request)
    {
        try {
            $zoneIds = [];
            $locations = $request->locations ?? [];
            $serviceId = $request->service_id;
            $serviceCategoryId = $request->service_category_id;

            if (!is_array($locations) || count($locations) < 1) {
                throw new Exception('At least one valid pickup location is required.', 422);
            }

            foreach ($locations as $location) {
                if (!is_array($location)) {
                    throw new Exception('A route location has invalid coordinates.', 422);
                }
                $lat = $location['lat'] ?? null;
                $lng = $location['lng'] ?? null;
                if (
                    !is_numeric($lat) ||
                    !is_numeric($lng) ||
                    !is_finite((float) $lat) ||
                    !is_finite((float) $lng) ||
                    abs((float) $lat) > 90 ||
                    abs((float) $lng) > 180
                ) {
                    throw new Exception('A route location has invalid coordinates.', 422);
                }

                $zones = getZoneByPoint($location['lat'], $location['lng']);
                if (!$zones->isEmpty()) {
                    foreach ($zones as $zone) {
                        $zoneIds[] = $zone?->id;
                    }
                }
            }

            if (empty($zoneIds)) {
                throw new Exception(__('taxido::static.vehicle_types.not_found_vehicles_by_points'), 400);
            }

            $originLat = $locations[0]['lat'];
            $originLng = $locations[0]['lng'];
            $zone = getZoneByPoint($originLat, $originLng)?->first();
            if (!$zone) {
                throw new Exception(__('taxido::static.vehicle_types.not_found_vehicles_by_points'), 400);
            }

            $packageDiscovery = false;
            $rentalDiscovery = false;
            if ($serviceCategoryId) {
                $serviceCategory = ServiceCategory::query()
                    ->where('id', $serviceCategoryId)
                    ->where('status', true)
                    ->where('service_id', $serviceId)
                    ->whereHas('service', function (Builder $service) {
                        $service->where('status', true);
                    })
                    ->first();

                if (!$serviceCategory) {
                    throw new Exception('The selected service category is inactive or does not match its service.', 422);
                }

                $packageDiscovery = $serviceCategory->type === ServiceCategoryEnum::PACKAGE;
                $rentalDiscovery = $serviceCategory->type === ServiceCategoryEnum::RENTAL;
            }

            $pickupDiscovery = $packageDiscovery || $rentalDiscovery;
            if ($pickupDiscovery && count($locations) !== 1) {
                throw new Exception('Package and rental discovery require one pickup location.', 422);
            }

            $vehicleTypes = $this->model->where('status', true)->where(function ($query) use ($zoneIds) {
                $query->where('is_all_zones', true)
                    ->orWhere(function ($q) use ($zoneIds) {
                        $q->where('is_all_zones', false)
                            ->where('status', true)
                            ->whereHas('zones', function (Builder $zones) use ($zoneIds) {
                                $zones->select('zones.id')?->whereIn('zones.id', $zoneIds);
                            });
                    });
            });

            if ($serviceId) {
                $vehicleTypes = $vehicleTypes->whereHas('services', function (Builder $service) use ($serviceId) {
                    $service->where('services.id', $serviceId)
                        ->where('services.status', true);
                });
            }

            if ($serviceCategoryId) {
                $vehicleTypes = $vehicleTypes->whereHas('service_categories', function (Builder $category) use ($serviceCategoryId, $serviceId) {
                    $category->where('service_categories.id', $serviceCategoryId)
                        ->where('service_categories.status', true)
                        ->where('service_categories.service_id', $serviceId);
                });
            }

            if ($request->service_category) {
                $service_category = $request->service_category;
                $vehicleTypes = $vehicleTypes->whereHas('service_categories', function (Builder $query) use ($service_category) {
                    $query->where('slug', '=' , $service_category);
                });
            }

            if ($packageDiscovery) {
                $vehicleTypes = $vehicleTypes->whereHas('hourly_packages', function (Builder $package) {
                    $package->where('status', true);
                });
            } elseif (!$rentalDiscovery) {
                $rideDistance = calculateRideDistance($locations, $zone?->distance_type);
            }

            $vehicleTypes = $vehicleTypes?->get() ?? [];
            if ($rentalDiscovery && $vehicleTypes) {
                $eligibleVehicleTypeIds = RentalVehicle::query()
                    ->where('zone_id', $zone->id)
                    ->where('status', true)
                    ->where('verified_status', RequestEnum::APPROVED)
                    ->where('vehicle_per_day_price', '>', 0)
                    ->whereIn('vehicle_type_id', $vehicleTypes->pluck('id'))
                    ->pluck('vehicle_type_id')
                    ->unique()
                    ->all();

                $vehicleTypes = $vehicleTypes->whereIn('id', $eligibleVehicleTypeIds);
            }

            if($zone && $vehicleTypes) {
                $vehicleTypeIds = $vehicleTypes->pluck('id')?->toArray() ?? [];
                if(!empty($vehicleTypeIds)) {
                    $vehicleTypeZones = VehicleTypeZone::whereIn('vehicle_type_id', $vehicleTypeIds ?? [])
                        ->where('zone_id', $zone->id)
                        ->get()
                        ->keyBy('vehicle_type_id');

                    foreach($vehicleTypes as $vehicleType) {
                        $vehicleType->currency_symbol = $zone->currency?->symbol;
                        $vehicleType->currency_code = $zone->currency?->code;

                        // Use pre-fetched data
                        $vehicleTypeZone = $vehicleTypeZones->get($vehicleType->id);
                        if($vehicleTypeZone) {
                            $vehicleType->vehicle_type_zone = $vehicleTypeZone;
                            $vehicleTypeZone->weight = $request?->weight;
                            if (!$pickupDiscovery) {
                                $charge = $this->calVehicleTypeZonePrice($rideDistance, $vehicleTypeZone, $request);
                                $vehicleType->charges = $charge;
                            }
                        }
                    }
                }
            }

            if($vehicleTypes && $zone && !$pickupDiscovery) {
                if($request->distance && $request->duration) {
                    foreach($vehicleTypes as $vehicleType) {
                        $distance = str_replace(' KM','',$request->distance);
                        $zone->distance_unit = null;
                        $zone->distance = $distance;
                        $vehicleType->currency_symbol = $zone->currency?->symbol;
                        $vehicleType->currency_code = $zone->currency?->code;
                    }
                }

                $vehicleTypes = $vehicleTypes->filter(function ($vehicleType, $key) {
                    return isset($vehicleType?->charges['total']) ? $vehicleType?->charges['total'] > 0 : false;
                });
            } elseif ($packageDiscovery) {
                $vehicleTypes = $vehicleTypes->filter(function ($vehicleType) {
                    return $vehicleType?->vehicle_type_zone !== null;
                });
            }

            return VehicleTypeResource::collection($vehicleTypes ?? []);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
