<?php
    $settings = getTaxidoSettings();
?>
<div class="col-12">
    <div class="row g-xl-4 g-3">
        <div class="col-xl-12">
            <div class="left-part">
                <div class="contentbox">
                    <div class="inside">
                        <div class="contentbox-title">
                            <h3>
                                <?php echo e(isset($airport) ? __('taxido::static.airports.edit') : __('taxido::static.airports.add')); ?>

                                (<?php echo e(request('locale', app()->getLocale())); ?>)
                            </h3>
                        </div>
                        <?php if(isset($airport)): ?>
                            <div class="form-group row">
                                <label class="col-md-2" for="name"><?php echo e(__('taxido::static.language.languages')); ?></label>
                                <div class="col-md-10">
                                    <ul class="language-list">
                                        <?php $__empty_1 = true; $__currentLoopData = getLanguages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <li>
                                                <a href="<?php echo e(route('admin.airport.edit', ['airport' => $airport->id, 'locale' => $lang->locale])); ?>"
                                                    class="language-switcher <?php echo e(request('locale') === $lang->locale ? 'active' : ''); ?>"
                                                    target="_blank">
                                                    <img src="<?php echo e(@$lang?->flag ?? asset('admin/images/No-image-found.jpg')); ?>"
                                                        alt="">
                                                    <?php echo e(@$lang?->name); ?> (<?php echo e(@$lang?->locale); ?>)
                                                    <i class="ri-arrow-right-up-line"></i>
                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <li>
                                                <a href="<?php echo e(route('admin.airport.edit', ['airport' => $airport->id, 'locale' => Session::get('locale', 'en')])); ?>"
                                                    class="language-switcher active" target="blank">
                                                    <img src="<?php echo e(asset('admin/images/flags/LR.png')); ?>" alt="">
                                                    English
                                                    <i class="ri-arrow-right-up-line"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="locale" value="<?php echo e(request('locale')); ?>">
                        <div class="form-group row">
                            <label class="col-md-2" for="name"><?php echo e(__('taxido::static.airports.name')); ?><span>*</span></label>
                            <div class="col-md-10">
                                    <input class="form-control" type="text" id="name" name="name"
                                        placeholder="<?php echo e(__('taxido::static.airports.enter_name')); ?> (<?php echo e(request('locale', app()->getLocale())); ?>)"
                                        value="<?php echo e(isset($airport->name) ? $airport->getTranslation('name', request('locale', app()->getLocale())) : old('name')); ?>">
                                    <i class="ri-file-copy-line copy-icon" data-target="#name"></i>
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
                                for="place_points"><?php echo e(__('taxido::static.airports.place_points')); ?><span>
                                    *</span></label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" id="place_points" name="place_points"
                                    placeholder="<?php echo e(__('taxido::static.airports.select_place_points')); ?>"
                                    value="<?php echo e(isset($airport->locations) ? json_encode($airport->locations, true) : old('place_points')); ?>"
                                    readonly>
                                <?php $__errorArgs = ['place_points'];
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
                                for="search-box"><?php echo e(__('taxido::static.airports.search_location')); ?></label>
                            <div class="col-md-10">
                                <input id="search-box" class="form-control" type="text"
                                    placeholder="<?php echo e(__('taxido::static.airports.search_locations')); ?>">
                                <ul id="suggestions-list" class="map-location-list custom-scrollbar"></ul>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2" for="map"><?php echo e(__('taxido::static.airports.map')); ?></label>
                            <div class="col-md-10">
                                <div class="map-warper dark-support rounded overflow-hidden">
                                    <div class="map-container" id="map-container"></div>
                                </div>
                                <div id="coords"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2" for="status"><?php echo e(__('taxido::static.status')); ?></label>
                            <div class="col-md-10">
                                <div class="editor-space">
                                    <label class="switch">
                                        <?php if(isset($airport)): ?>
                                            <input class="form-control" type="hidden" name="status"
                                                value="0">
                                            <input class="form-check-input" type="checkbox" name="status"
                                                id="" value="1" <?php echo e($airport->status ? 'checked' : ''); ?>>
                                        <?php else: ?>
                                            <input class="form-control" type="hidden" name="status"
                                                value="0">
                                            <input class="form-check-input" type="checkbox" name="status"
                                                id="" value="1" checked>
                                        <?php endif; ?>
                                        <span class="switch-state"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <div class="submit-btn">
                                    <button type="button" id="saveBtn" name="save" class="btn btn-primary spinner-btn">
                                        <i class="ri-save-line text-white lh-1"></i> <?php echo e(__('taxido::static.save')); ?>

                                    </button>
                                    <button type="button" id="saveExitBtn" name="save_and_exit" class="btn btn-primary spinner-btn">
                                        <i class="ri-expand-left-line text-white lh-1"></i><?php echo e(__('taxido::static.save_and_exit')); ?>

                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($settings['location']['map_provider'] == 'google_map'): ?>
    <?php if ($__env->exists('taxido::admin.airport.google')) echo $__env->make('taxido::admin.airport.google', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php elseif($settings['location']['map_provider'] == 'osm'): ?>
    <?php if ($__env->exists('taxido::admin.airport.osm')) echo $__env->make('taxido::admin.airport.osm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function($) {
            "use strict";
            $('#airportForm').validate({
                rules: {
                    "name": "required",
                    "currency_id": "required",
                    "amount": "required",
                    "distance_type": "required",
                    "place_points": "required",
                }
            });
        })(jQuery);

        function toggleInputFields(type) {
            if (type === 'fixed') {
                $('#currencyIcon').show();
                $('#percentageIcon').hide();
                $('#amountField').show();
            } else if (type === 'percentage') { 
                $('#currencyIcon').hide();
                $('#percentageIcon').show();
                $('#amountField').show();
            } else {
                $('#amountField').hide();
            }
        }
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/aakshay/Taxido_laravel/Modules/Taxido/resources/views/admin/airport/fields.blade.php ENDPATH**/ ?>