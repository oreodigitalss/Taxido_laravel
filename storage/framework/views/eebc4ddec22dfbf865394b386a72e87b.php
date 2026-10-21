  <!-- Link Swiper's CSS -->
  <link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/vendors/swiper-slider.css')); ?>">
  <?php use \App\Models\Tax; ?>
  <?php use \Modules\Taxido\Enums\RoleEnum; ?>
  <?php use \Modules\Taxido\Models\Zone; ?>
  <?php use \App\Enums\RoleEnum as BaseRoleEnum; ?>
  <?php
  $taxes = Tax::where('status', true)->get(['id', 'name']);
  $zones = Zone::where('status', true)?->get(['id', 'name']);
  $drivers = getAllVerifiedDrivers();
  ?>
  <div class="col-xl-9">
      <div class="left-part">
          <div class="contentbox">
              <div class="inside">
                  <div class="contentbox-title">
                      <h3><?php echo e(isset($rentalVehicle) ? __('taxido::static.rental_vehicle.edit') : __('taxido::static.rental_vehicle.add')); ?>(<?php echo e(request('locale', app()->getLocale())); ?>)
                      </h3>
                  </div>
                  <?php if(isset($rentalVehicle)): ?>
                  <div class="form-group row">
                      <label class="col-md-2" for="name"><?php echo e(__('taxido::static.language.languages')); ?></label>
                      <div class="col-md-10">
                          <ul class="language-list">
                              <?php $__empty_1 = true; $__currentLoopData = getLanguages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                              <li>
                                  <a href="<?php echo e(route('admin.rental-vehicle.edit', ['rental_vehicle' => $rentalVehicle->id, 'locale' => $lang->locale])); ?>"
                                      class="language-switcher <?php echo e(request('locale') === $lang->locale ? 'active' : ''); ?>"
                                      target="_blank"><img src="<?php echo e(@$lang?->flag ?? asset('admin/images/No-image-found.jpg')); ?>" alt=""> <?php echo e(@$lang?->name); ?> (<?php echo e(@$lang?->locale); ?>)
                                      <i class="ri-arrow-right-up-line"></i>
                                    </a>
                              </li>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <li>
                                  <a href="<?php echo e(route('admin.rental-vehicle.edit', ['rental_vehicle' => $rentalVehicle->id, 'locale' => Session::get('locale', 'en')])); ?>"
                                      class="language-switcher active" target="blank"><img
                                          src="<?php echo e(asset('admin/images/flags/LR.png')); ?>" alt="">English<i
                                          class="ri-arrow-right-up-line"></i></a>
                              </li>
                              <?php endif; ?>
                          </ul>
                      </div>
                  </div>
                  <?php endif; ?>
                  <input type="hidden" name="locale" value="<?php echo e(request('locale')); ?>">
                  <?php if(getCurrentRoleName() !== RoleEnum::DRIVER): ?>
                  <div class="form-group row">
                      <label class="col-md-2" for="driver"><?php echo e(__('taxido::static.reports.driver')); ?></label>
                      <div class="col-md-10 select-label-error">
                          <span class="text-gray mt-1">
                              <?php echo e(__('taxido::static.driver_documents.add_driver_message')); ?>

                              <a href="<?php echo e(route('admin.driver.index')); ?>" class="text-primary">
                                  <b><?php echo e(__('taxido::static.here')); ?></b>
                              </a>
                          </span>
                          <select class="select-2 form-control filter-dropdown disable-all" id="driver_id" name="driver_id" data-placeholder="<?php echo e(__('taxido::static.reports.select_driver')); ?>">
                              <option value="" selected></option>
                              <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($driver->id); ?>" <?php if(old('driver_id', $rentalVehicle->driver_id ?? '') == $driver->id): echo 'selected'; endif; ?>>
                                  <?php echo e($driver?->name); ?>

                              </option>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                      </div>
                  </div>
                  <?php endif; ?>

                  <div class="form-group row">
                      <label class="col-md-2" for="name"><?php echo e(__('taxido::static.rental_vehicle.name')); ?><span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="name" name="name"
                              value="<?php echo e(isset($rentalVehicle->name) ? $rentalVehicle->name : old('name')); ?>"
                              placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_name')); ?>">
                          <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2"
                          for="description"><?php echo e(__('taxido::static.rental_vehicle.description')); ?></label>
                      <div class="col-md-10">
                          <textarea class="form-control" id="description" name="description" placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_description')); ?>"><?php echo e(isset($rentalVehicle->description) ? $rentalVehicle->description : old('description')); ?></textarea>
                          <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-2" for="vehicle_type_id"><?php echo e(__('taxido::static.rental_vehicle.vehicle_type')); ?><span>*</span></label>
                    <div class="col-md-10 select-label-error">
                        <select class="form-control select-2" data-placeholder="<?php echo e(__('taxido::static.rental_vehicle.select_vehicle_type')); ?>"
                            id="vehicle_type_id" name="vehicle_type_id">
                            <option value=""><?php echo e(__('taxido::static.rental_vehicle.select_vehicle_type')); ?></option>
                            <?php $__currentLoopData = $vehicleTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicleType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vehicleType->id); ?>" <?php if(old('vehicle_type_id', $rentalVehicle->vehicle_type_id ?? '') == $vehicleType->id): echo 'selected'; endif; ?>>
                                    <?php echo e($vehicleType->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['vehicle_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback d-block" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2" for="zone_id"><?php echo e(__('taxido::static.rental_vehicle.zone')); ?><span>*</span></label>
                    <div class="col-md-10 select-label-error">
                        <span class="text-gray mt-1">
                            <?php echo e(__('taxido::static.vehicle_types.no_zones_message')); ?>

                            <a href="<?php echo e(route('admin.zone.index')); ?>" class="text-primary">
                                <b><?php echo e(__('taxido::static.here')); ?></b>
                            </a>
                        </span>
                        <select class="form-control select-2" data-placeholder="<?php echo e(__('taxido::static.rental_vehicle.select_zones')); ?>"
                            id="zone_id" name="zone_id">
                            <option value=""><?php echo e(__('taxido::static.rental_vehicle.select_zones')); ?></option>
                            <?php $__currentLoopData = $zones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($zone->id); ?>"
                                    data-currency-symbol="<?php echo e($zone->currency?->symbol ?? getDefaultCurrency()?->symbol); ?>"
                                    data-currency-code="<?php echo e($zone->currency?->code ?? getDefaultCurrency()?->code); ?>"
                                    <?php if(old('zone_id', $rentalVehicle->zone_id ?? '') == $zone->id): echo 'selected'; endif; ?>>
                                    <?php echo e($zone->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['zone_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback d-block" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2" for="vehicle_per_day_price"><?php echo e(__('taxido::static.rental_vehicle.vehicle_per_day_price')); ?><span>*</span></label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text vehicle-currency-symbol"><?php echo e($rentalVehicle?->currency_symbol ?? ($rentalVehicle->zone?->currency?->symbol ?? getDefaultCurrency()?->symbol)); ?></span>
                            <input class="form-control" type="number" id="vehicle_per_day_price"
                                   name="vehicle_per_day_price" step="0.01"
                                   value="<?php echo e(isset($rentalVehicle->vehicle_per_day_price) ? $rentalVehicle->vehicle_per_day_price : old('vehicle_per_day_price')); ?>"
                                   placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_vehicle_per_day_price')); ?>">
                        </div>
                        <?php $__errorArgs = ['vehicle_per_day_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback d-block" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2" for="allow_with_driver"><?php echo e(__('taxido::static.rental_vehicle.with_driver')); ?></label>
                    <div class="col-md-10">
                        <label class="switch">
                            <input class="form-control" type="hidden" name="allow_with_driver" value="0">
                            <input class="form-check-input" type="checkbox" id="allow_with_driver" name="allow_with_driver" value="1"
                                <?php if(old('allow_with_driver', $rentalVehicle->allow_with_driver ?? true)): echo 'checked'; endif; ?>>
                            <span class="switch-state"></span>
                        </label>
                        <?php $__errorArgs = ['allow_with_driver'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback d-block" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="form-group row driver-charge-field"
                        style="display: <?php echo e(old('allow_with_driver', $rentalVehicle->allow_with_driver ?? true) ? 'flex' : 'none'); ?>">
                    <label class="col-md-2" for="driver_per_day_charge"><?php echo e(__('taxido::static.rental_vehicle.driver_per_day_charge')); ?><span>*</span></label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text driver-currency-symbol"><?php echo e($rentalVehicle?->currency_symbol ?? ($rentalVehicle->zone?->currency?->symbol ?? getDefaultCurrency()?->symbol)); ?></span>
                            <input class="form-control" type="number" id="driver_per_day_charge"
                                    name="driver_per_day_charge" step="0.01"
                                    value="<?php echo e(isset($rentalVehicle->driver_per_day_charge) ? $rentalVehicle->driver_per_day_charge : old('driver_per_day_charge')); ?>"
                                    placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_driver_per_day_charge')); ?>">
                        </div>
                        <?php $__errorArgs = ['driver_per_day_charge'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="invalid-feedback d-block" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                    <div class="form-group row">
                      <label class="col-md-2" for="vehicle_subtype">
                          <?php echo e(__('taxido::static.rental_vehicle.vehicle_subtype')); ?><span>*</span>
                      </label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="vehicle_subtype" name="vehicle_subtype"
                              value="<?php echo e(isset($rentalVehicle->vehicle_subtype) ? $rentalVehicle->vehicle_subtype : old('vehicle_subtype')); ?>"
                              placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_vehicle_subtype')); ?>">
                          <?php $__errorArgs = ['vehicle_subtype'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-md-2" for="fuel_type">
                          <?php echo e(__('taxido::static.rental_vehicle.fuel_type')); ?><span>*</span>
                      </label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="fuel_type" name="fuel_type" value="<?php echo e(isset($rentalVehicle->fuel_type) ? $rentalVehicle->fuel_type : old('fuel_type')); ?>"
                              placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_fuel_type')); ?>">
                          <?php $__errorArgs = ['fuel_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-md-2" for="gear_type">
                          <?php echo e(__('taxido::static.rental_vehicle.gear_type')); ?><span>
                              *</span>
                      </label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="gear_type" name="gear_type"
                              value="<?php echo e(isset($rentalVehicle->gear_type) ? $rentalVehicle->gear_type : old('gear_type')); ?>"
                              placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_gear_type')); ?>">
                          <?php $__errorArgs = ['gear_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-md-2"
                          for="vehicle_speed"><?php echo e(__('taxido::static.rental_vehicle.vehicle_speed')); ?><span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="vehicle_speed" name="vehicle_speed"
                              value="<?php echo e(isset($rentalVehicle->vehicle_speed) ? $rentalVehicle->vehicle_speed : old('vehicle_speed')); ?>"
                              placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_vehicle_speed')); ?>">
                          <?php $__errorArgs = ['vehicle_speed'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                    </div>

                  <div class="form-group row">
                      <label class="col-md-2" for="mileage"><?php echo e(__('taxido::static.rental_vehicle.mileage')); ?><span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="mileage" name="mileage"
                              value="<?php echo e(isset($rentalVehicle->mileage) ? $rentalVehicle->mileage : old('mileage')); ?>"
                              placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_mileage')); ?>">
                          <?php $__errorArgs = ['mileage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2"
                          for="registration_no"><?php echo e(__('taxido::static.rental_vehicle.registration_no')); ?><span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="registration_no" name="registration_no"
                              value="<?php echo e(isset($rentalVehicle->registration_no) ? $rentalVehicle->registration_no : old('registration_no')); ?>"
                              placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_registration_no')); ?>">
                          <?php $__errorArgs = ['registration_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                  </div>
                  <?php if(isset($rentalVehicle)): ?>
                  <?php if(getCurrentRoleName() == BaseRoleEnum::ADMIN): ?>
                  <div class="form-group row">
                      <label for="status" class="col-md-2">
                          <?php echo e(__('taxido::static.driver_documents.status')); ?><span>*</span>
                      </label>
                      <div class="col-md-10 select-label-error">
                          <select class="select-2 form-control" id="status" name="verified_status"
                              data-placeholder="<?php echo e(__('taxido::static.driver_documents.select_status')); ?>">
                              <option class="option" value="" selected></option>
                              <option value="pending" <?php if(old('status', @$rentalVehicle?->verified_status) == 'pending'): echo 'selected'; endif; ?>>
                                  <?php echo e(__('taxido::static.driver_documents.pending')); ?>

                              </option>
                              <option value="approved" <?php if(old('status', @$rentalVehicle?->verified_status) == 'approved'): echo 'selected'; endif; ?>>
                                  <?php echo e(__('taxido::static.driver_documents.approved')); ?>

                              </option>
                              <option value="rejected" <?php if(old('status', @$rentalVehicle?->verified_status) == 'rejected'): echo 'selected'; endif; ?>>
                                  <?php echo e(__('taxido::static.driver_documents.rejected')); ?>

                              </option>
                          </select>
                          <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                  </div>
                  <?php elseif(getCurrentRoleName() == RoleEnum::DRIVER): ?>
                  <input type="hidden" name="status" value="pending">
                  <?php endif; ?>
                  <?php endif; ?>
                  <div class="form-group row">
                      <label class="col-md-2" for="status"><?php echo e(__('taxido::static.rental_vehicle.status')); ?></label>
                      <div class="col-md-10">
                          <label class="switch">
                              <input class="form-control" type="hidden" name="status" value="0">
                              <input class="form-check-input" type="checkbox" id="status" name="status"
                                  value="1" <?php if(old('status', $rentalVehicle->status ?? true)): echo 'checked'; endif; ?>>
                              <span class="switch-state"></span>
                          </label>
                          <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <span class="invalid-feedback d-block" role="alert">
                              <strong><?php echo e($message); ?></strong>
                          </span>
                          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                  </div>

                  <div id="interior-group">
                      <?php if(!empty(old('interior', $rentalVehicle->interior ?? []))): ?>
                      <?php $__currentLoopData = old('interior', $rentalVehicle->interior ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $interiorDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="form-group row">
                          <label class="col-md-2" for="interior">
                              <?php echo e(__('taxido::static.rental_vehicle.interior')); ?><span> *</span>
                          </label>
                          <div class="col-md-10">
                              <div class="interior-fields">
                                  <input class="form-control" type="text" name="interior[]"
                                      placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_interior_detail')); ?>"
                                      value="<?php echo e($interiorDetail); ?>">
                                  <button type="button" class="btn btn-danger remove-interior">
                                      <i class="ri-delete-bin-line"></i>
                                  </button>
                              </div>
                          </div>
                      </div>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php else: ?>
                      <div class="form-group row">
                          <label class="col-md-2" for="interior">
                              <?php echo e(__('taxido::static.rental_vehicle.interior')); ?><span> *</span>
                          </label>
                          <div class="col-md-10">
                              <div class="interior-fields">
                                  <input class="form-control" type="text" name="interior[]"
                                      placeholder="<?php echo e(__('taxido::static.rental_vehicle.enter_interior_detail')); ?>">
                                  <button type="button" class="btn btn-danger remove-interior">
                                      <i class="ri-delete-bin-line"></i>
                                  </button>
                              </div>
                          </div>
                      </div>
                      <?php endif; ?>
                  </div>

                  <button type="button" id="add-interior" class="btn btn-primary mt-2">
                      <?php echo e(__('taxido::static.rental_vehicle.add_interior')); ?>

                  </button>
              </div>
          </div>
      </div>
  </div>
  <div class="col-xl-3">
      <div class="left-part">
          <div class="contentbox">
              <div class="inside">
                  <div class="contentbox-title ">
                      <h3><?php echo e(__('static.blogs.publish')); ?></h3>
                  </div>
                  <div class="form-group row">
                      <div class="col-12">
                          <div class="row g-3">
                              <div class="col-12">
                                  <div class="d-flex align-items-center gap-2 icon-position">
                                      <button type="submit" name="save" class="btn btn-primary">
                                          <i class="ri-save-line text-white lh-1"></i> <?php echo e(__('static.save')); ?>

                                      </button>
                                      <button type="submit" name="save_and_exit"
                                          class="btn btn-primary spinner-btn">
                                          <i
                                              class="ri-expand-left-line text-white lh-1"></i><?php echo e(__('static.save_and_exit')); ?>

                                      </button>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="contentbox">
              <div class="inside">
                  <div class="contentbox-title">
                      <h3><?php echo e(__('taxido::static.rental_vehicle.images')); ?></h3>
                      <button type="button" class="btn btn-calculate" data-bs-toggle="modal" data-bs-target="#imageHelpModal">
                          <i class="ri-information-line"></i> <?php echo e(__('taxido::static.rental_vehicle.view_image_guide')); ?>

                      </button>

                  </div>
                  <div class="form-group row">
                      <label class="col-md-5" for="normal_image_id">
                          <?php echo e(__('taxido::static.rental_vehicle.normal_image')); ?><span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <?php if (isset($component)) { $__componentOriginal22d447e3f5aafc93b8447b54b36ee789 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image','data' => ['unallowedTypes' => ['svg'],'name' => 'normal_image_id','data' => isset($rentalVehicle->normal_image)
                                  ? $rentalVehicle->normal_image
                                  : old('normal_image_id'),'text' => __('taxido::static.rental_vehicle.normal_image'),'multiple' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['unallowed_types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['svg']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('normal_image_id'),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($rentalVehicle->normal_image)
                                  ? $rentalVehicle->normal_image
                                  : old('normal_image_id')),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('taxido::static.rental_vehicle.normal_image')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $attributes = $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $component = $__componentOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
                              <?php $__errorArgs = ['normal_image_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong><?php echo e($message); ?></strong>
                              </span>
                              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="front_view_id">
                          <?php echo e(__('taxido::static.rental_vehicle.front_view')); ?><span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <?php if (isset($component)) { $__componentOriginal22d447e3f5aafc93b8447b54b36ee789 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image','data' => ['unallowedTypes' => ['svg'],'name' => 'front_view_id','data' => isset($rentalVehicle->front_view)
                                  ? $rentalVehicle->front_view
                                  : old('front_view_id'),'text' => __('taxido::static.rental_vehicle.front_view'),'multiple' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['unallowed_types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['svg']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('front_view_id'),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($rentalVehicle->front_view)
                                  ? $rentalVehicle->front_view
                                  : old('front_view_id')),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('taxido::static.rental_vehicle.front_view')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $attributes = $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $component = $__componentOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
                              <?php $__errorArgs = ['front_view_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong><?php echo e($message); ?></strong>
                              </span>
                              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="side_view_id">
                          <?php echo e(__('taxido::static.rental_vehicle.side_view')); ?><span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <?php if (isset($component)) { $__componentOriginal22d447e3f5aafc93b8447b54b36ee789 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image','data' => ['unallowedTypes' => ['svg'],'name' => 'side_view_id','data' => isset($rentalVehicle->side_view)
                                  ? $rentalVehicle->side_view
                                  : old('side_view_id'),'text' => __('taxido::static.rental_vehicle.side_view'),'multiple' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['unallowed_types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['svg']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('side_view_id'),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($rentalVehicle->side_view)
                                  ? $rentalVehicle->side_view
                                  : old('side_view_id')),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('taxido::static.rental_vehicle.side_view')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $attributes = $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $component = $__componentOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
                              <?php $__errorArgs = ['side_view_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong><?php echo e($message); ?></strong>
                              </span>
                              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="boot_view_id">
                          <?php echo e(__('taxido::static.rental_vehicle.boot_view')); ?><span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <?php if (isset($component)) { $__componentOriginal22d447e3f5aafc93b8447b54b36ee789 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image','data' => ['unallowedTypes' => ['svg'],'name' => 'boot_view_id','data' => isset($rentalVehicle->boot_view)
                                  ? $rentalVehicle->boot_view
                                  : old('boot_view_id'),'text' => __('taxido::static.rental_vehicle.boot_view'),'multiple' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['unallowed_types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['svg']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('boot_view_id'),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($rentalVehicle->boot_view)
                                  ? $rentalVehicle->boot_view
                                  : old('boot_view_id')),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('taxido::static.rental_vehicle.boot_view')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $attributes = $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $component = $__componentOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
                              <?php $__errorArgs = ['boot_view_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong><?php echo e($message); ?></strong>
                              </span>
                              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="interior_image_id">
                          <?php echo e(__('taxido::static.rental_vehicle.interior')); ?><span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <?php if (isset($component)) { $__componentOriginal22d447e3f5aafc93b8447b54b36ee789 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image','data' => ['unallowedTypes' => ['svg'],'name' => 'interior_image_id','data' => isset($rentalVehicle->interior_image)
                                  ? $rentalVehicle->interior_image
                                  : old('interior_image_id'),'text' => __('taxido::static.rental_vehicle.interior'),'multiple' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['unallowed_types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['svg']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('interior_image_id'),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($rentalVehicle->interior_image)
                                  ? $rentalVehicle->interior_image
                                  : old('interior_image_id')),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('taxido::static.rental_vehicle.interior')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $attributes = $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $component = $__componentOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
                              <?php $__errorArgs = ['interior_image_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong><?php echo e($message); ?></strong>
                              </span>
                              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="registration_image_id">
                          <?php echo e(__('taxido::static.rental_vehicle.registration_image')); ?><span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <?php if (isset($component)) { $__componentOriginal22d447e3f5aafc93b8447b54b36ee789 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image','data' => ['unallowedTypes' => ['svg'],'name' => 'registration_image_id','data' => isset($rentalVehicle->registration_image)
                                  ? $rentalVehicle->registration_image
                                  : old('registration_image_id'),'text' => __('taxido::static.rental_vehicle.registration_image'),'multiple' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['unallowed_types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['svg']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('registration_image_id'),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($rentalVehicle->registration_image)
                                  ? $rentalVehicle->registration_image
                                  : old('registration_image_id')),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('taxido::static.rental_vehicle.registration_image')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $attributes = $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $component = $__componentOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
                              <?php $__errorArgs = ['registration_image_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong><?php echo e($message); ?></strong>
                              </span>
                              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <div class="modal fade rental-rides-modal" id="imageHelpModal">
      <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-body">
                  <button type="button" class="btn-close" data-bs-dismiss="modal">
                      <i class="ri-close-line"></i>
                  </button>
                  <div class="swiper rental-images-slider">
                      <div class="swiper-wrapper">
                          <div class="swiper-slide">
                              <img src="<?php echo e(asset('modules/taxido/images/defaults/normal.jpg')); ?>"
                                  class="d-block w-100" alt="Normal Image">
                          </div>
                          <div class="swiper-slide">
                              <img src="<?php echo e(asset('modules/taxido/images/defaults/front.jpg')); ?>"
                                  class="d-block w-100" alt="Front View">
                          </div>
                          <div class="swiper-slide">
                              <img src="<?php echo e(asset('modules/taxido/images/defaults/side-view.jpg')); ?>"
                                  class="d-block w-100" alt="Side View">
                          </div>
                          <div class="swiper-slide">
                              <img src="<?php echo e(asset('modules/taxido/images/defaults/boot-view.jpg')); ?>"
                                  class="d-block w-100" alt="Boot View">
                          </div>
                          <div class="swiper-slide">
                              <img src="<?php echo e(asset('modules/taxido/images/defaults/interior.jpg')); ?>"
                                  class="d-block w-100" alt="Interior View">
                          </div>
                      </div>
                  </div>
                  <div class="swiper rental-content-slider theme-pagination">
                      <div class="swiper-wrapper">
                          <div class="swiper-slide">
                              <div class="rental-content-box">
                                  <h5><?php echo e(__('taxido::static.rental_vehicle.normal_image')); ?></h5>
                                  <p><?php echo e(__('taxido::static.rental_vehicle.normal_image_span')); ?></p>
                              </div>
                          </div>
                          <div class="swiper-slide">
                              <div class="rental-content-box">
                                  <h5><?php echo e(__('taxido::static.rental_vehicle.front_view')); ?></h5>
                                  <p><?php echo e(__('taxido::static.rental_vehicle.front_view_span')); ?></p>
                              </div>
                          </div>
                          <div class="swiper-slide">
                              <div class="rental-content-box">
                                  <h5><?php echo e(__('taxido::static.rental_vehicle.side_view')); ?></h5>
                                  <p><?php echo e(__('taxido::static.rental_vehicle.side_view_span')); ?></p>
                              </div>
                          </div>
                          <div class="swiper-slide">
                              <div class="rental-content-box">
                                  <h5><?php echo e(__('taxido::static.rental_vehicle.boot_view')); ?></h5>
                                  <p><?php echo e(__('taxido::static.rental_vehicle.boot_view_span')); ?></p>
                              </div>
                          </div>
                          <div class="swiper-slide">
                              <div class="rental-content-box">
                                  <h5><?php echo e(__('taxido::static.rental_vehicle.interior')); ?></h5>
                                  <p><?php echo e(__('taxido::static.rental_vehicle.interior_span')); ?></p>
                              </div>
                          </div>
                      </div>
                      <div class="swiper-pagination pagination-swiper"></div>
                  </div>
              </div>
          </div>
      </div>
  </div>
<?php $__env->startPush('scripts'); ?>
  <script src="<?php echo e(asset('js/swiper-slider/swiper.js')); ?>"></script>
  <script src="<?php echo e(asset('js/swiper-slider/custom-slider.js')); ?>"></script>
  <script>
      (function($) {
          "use strict";
          $('#rentalVehicleForm').validate({
              rules: {
                  "name": "required",
                  "vehicle_type_id": "required",
                  "vehicle_subtype": "required",
                  "normal_image_id": "required",
                  "front_view_id": "required",
                  "side_view_id": "required",
                  "boot_view_id": "required",
                  "interior_image_id": "required",
                  "vehicle_per_day_price": {
                      required: true,
                      number: true,
                      min: 0
                  },
                  "allow_with_driver": "required",
                  "driver_per_day_charge": {
                      required: function() {
                          return $('#allow_with_driver').is(':checked');
                      },
                      number: true,
                      min: 0
                  },
                  "fuel_type": "required",
                  "gear_type": "required",
                  "vehicle_speed": "required",
                  "mileage": "required",
                  "status": "required",
                  "zone_id": "required",
                  "interior[]": "required",
                  "registration_no": "required",
                  "commission_type": "required",
                  "commission_rate": {
                      required: true,
                      number: true,
                      min: 0
                  },
              },
          });

          $(document).ready(function() {
              const $allowWithDriver = $('#allow_with_driver');
              const $driverChargeField = $('.driver-charge-field');
              const $commissionType = $('#commission_type');
              const $currencyIcon = $('#currencyIcon');
              const $percentageIcon = $('#percentageIcon');
              const $zoneSelect = $('#zone_id');
              const $vehicleCurrencySymbol = $('.vehicle-currency-symbol');
              const $driverCurrencySymbol = $('.driver-currency-symbol');

              function updateCurrencySymbols() {
                  const selectedOption = $zoneSelect.find('option:selected');
                  const currencySymbol = selectedOption.data('currency-symbol') || '<?php echo e(getDefaultCurrency()?->symbol); ?>';

                  // Update both currency symbols
                  $vehicleCurrencySymbol.text(currencySymbol);
                  $driverCurrencySymbol.text(currencySymbol);

                  if ($commissionType.val() === 'fixed') {
                      $currencyIcon.text(currencySymbol).show();
                      $percentageIcon.hide();
                  } else {
                      $currencyIcon.hide();
                      $percentageIcon.show();
                  }
              }

              updateCurrencySymbols();

              $zoneSelect.on('change', function() {
                  updateCurrencySymbols();
              });

              $allowWithDriver.on('change', function() {
                  $driverChargeField.toggle($(this).is(':checked'));
              });

              $commissionType.on('change', function() {
                  const commissionType = $(this).val();
                  if (commissionType === 'percentage') {
                      $currencyIcon.hide();
                      $percentageIcon.show();
                  } else {
                      $currencyIcon.show();
                      $percentageIcon.hide();
                  }
                  updateCurrencySymbols();
              });

              const MAX_INTERIORS = 5;

              function toggleRemoveButtons() {
                  const $interiorGroups = $('#interior-group .form-group');
                  if ($interiorGroups.length === 1) {
                      $interiorGroups.find('.remove-interior').hide();
                  } else {
                      $interiorGroups.find('.remove-interior').show();
                  }
              }

              $('#add-interior').on('click', function () {
                const interiorInputs = $('#interior-group input[name="interior[]"]');
                const interiorCount = interiorInputs.length;

                if (interiorCount >= MAX_INTERIORS) {
                    toastr.warning("<?php echo e(__('taxido::static.rental_vehicle.message')); ?>");
                    return;
                }

                let allFilled = true;
                interiorInputs.each(function () {
                    if ($(this).val().trim() === '') {
                        allFilled = false;
                        return false;
                    }
                });

                if (!allFilled) {
                    toastr.warning("<?php echo e(__('taxido::static.rental_vehicle.fill_all_before_add')); ?>");
                    return;
                }

                const newInteriorField = $('#interior-group .form-group:first').clone();
                newInteriorField.find('input').val('');
                $('#interior-group').append(newInteriorField);
                toggleRemoveButtons();
            });

              $(document).on('click', '.remove-interior', function() {
                  $(this).closest('.form-group').remove();
                  toggleRemoveButtons();
              });

              toggleRemoveButtons();
          });
      })(jQuery);
  </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/aakshay/Taxido_laravel/Modules/Taxido/resources/views/admin/rental-vehicle/fields.blade.php ENDPATH**/ ?>