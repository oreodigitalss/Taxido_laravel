<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\TaxidoSetting;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Prettus\Repository\Eloquent\BaseRepository;

class SettingRepository extends BaseRepository
{

    public function model()
    {
        return TaxidoSetting::class;
    }

    public function index()
    {
        $settings = getTaxidoSettings();

        $settings['setting']['splash_screen'] = getMedia($settings['setting']['splash_screen_id'] ?? null);
        $settings['setting']['driver_splash_screen'] = getMedia($settings['setting']['splash_driver_screen_id'] ?? null);

        return view('taxido::admin.taxido-setting.index', [
            'taxidosettings' => $settings,
            'id' => $this->model->pluck('id')->first(),
        ]);
    }

    public function updateSettings(Request $request, $id)
    {
        DB::beginTransaction();
        try {

            $taxidoSettings = $this->model->findOrFail($id);

            $fields = [
                'general' => ['ambulance_image', 'ambulance_map_icon'],
            ];

            foreach ($fields as $section => $imageFields) {
                $this->processFields($request, $taxidoSettings, $section, $imageFields);
            }

            // Keep only submitted form values here; uploaded files are handled
            // separately so Symfony UploadedFile instances never enter JSON.
            $values = $request->request->all();
            $values = array_diff_key($values, array_flip(['_token', '_method']));
            if (isset($values['referral'])) {
                $this->validateReferralSettings($values['referral']);
            }

            if (isset($values['location']['google_map_api_key'])) {
                $values['location']['google_map_api_key'] = decryptKey($values['location']['google_map_api_key']);
            }

            $previousGoogleMapKey = data_get($taxidoSettings->cabbooking_values, 'location.google_map_api_key');
            $googleMapKeyChanged = data_get($values, 'location.google_map_api_key') !== $previousGoogleMapKey;

            $taxidoSettings->update([
                'cabbooking_values' => $values,
            ]);

            DB::commit();
            $this->env($values, $googleMapKeyChanged);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('static.settings.update_successfully'),
                ]);
            }

            return to_route('admin.taxido-setting.index')->with('success', __('static.settings.update_successfully'));

        } catch (Exception $e) {
            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    private function validateReferralSettings(array $referralSettings): void
    {
        if (isset($referralSettings['minimum_ride_amount'])) {
            $minRideAmount = (float) $referralSettings['minimum_ride_amount'];
            if ($minRideAmount < 0) {
                throw new Exception(__('taxido::static.validation.minimum_ride_amount_positive'));
            }
        }

        if (isset($referralSettings['referrer_bonus_percentage'])) {
            $referrerPercentage = (float) $referralSettings['referrer_bonus_percentage'];
            if ($referrerPercentage < 0 || $referrerPercentage > 100) {
                throw new Exception(__('taxido::static.validation.referrer_bonus_percentage_range'));
            }
        }

        if (isset($referralSettings['referred_bonus_percentage'])) {
            $referredPercentage = (float) $referralSettings['referred_bonus_percentage'];
            if ($referredPercentage < 0 || $referredPercentage > 100) {
                throw new Exception(__('taxido::static.validation.referred_bonus_percentage_range'));
            }
        }
    }

    public function env($cabbooking_values, bool $googleMapKeyChanged = true)
    {
        try {
            if ($googleMapKeyChanged && isset($cabbooking_values['location']['google_map_api_key'])){
                $google_map_api_key = $cabbooking_values['location']['google_map_api_key'];

                // Avoid touching .env on image-only/settings saves. Laravel's
                // built-in server restarts when .env changes, interrupting the
                // upload request and briefly refusing new connections.
                $currentKey = DotenvEditor::keyExists('GOOGLE_MAP_API_KEY')
                    ? DotenvEditor::getValue('GOOGLE_MAP_API_KEY')
                    : null;

                if ($currentKey === $google_map_api_key) {
                    return;
                }

                DotenvEditor::setKeys([
                    'GOOGLE_MAP_API_KEY' => $google_map_api_key,
                ]);

                DotenvEditor::save();
            }
        } catch (Throwable $e) {
            // The settings have already been committed at this point. A
            // dotenv write failure must not turn a successful save into a
            // blank/500 response; keep the failure visible in the log.
            Log::error('Unable to update Google Maps API key in .env: '.$e->getMessage());
        }
    }

    private function processFields(Request $request, $taxidoSettings, $section, array $fields)
    {
        foreach ($fields as $field) {
            $requestValue = $request->file($section.'.'.$field);

            if ($request->hasFile($section.'.'.$field)) {
                $media = $this->storeImage($requestValue);
                $request->merge([$section => array_merge(
                    $request->input($section, []),
                    [$field => $media->asset_url]
                )]);
            }

            // The request's raw input array is persisted below. Preserve the
            // existing URL when no replacement file was uploaded.
            if (!$request->hasFile($section.'.'.$field)) {
                $request->merge([$section => array_merge(
                    $request->input($section, []),
                    [$field => $taxidoSettings->cabbooking_values[$section][$field] ?? null]
                )]);
            }
        }
    }

    public function storeImage($request)
    {
        $attachments = createAttachment();
        $media = addMedia($attachments, $request);
        return $media;
    }

}
