<div class="row g-xl-4 g-3">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="left-part">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3><?php echo e(isset($hourlyPackage) ? __('taxido::static.hourly_package.edit') : __('taxido::static.hourly_package.add')); ?>

                        </h3>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2" for="hour"><?php echo e(__('taxido::static.hourly_package.hour')); ?><span>
                                *</span></label>
                        <div class="col-md-10">
                            <input class="form-control" type="number" name="hour"
                                value="<?php echo e(isset($hourlyPackage->hour) ? $hourlyPackage->hour : old('hour')); ?>"
                                placeholder="<?php echo e(__('taxido::static.hourly_package.enter_hour')); ?>">
                            <?php $__errorArgs = ['hour'];
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
                            <span class="text-gray mt-1">
                                <?php echo e(__('taxido::static.hourly_package.hour_span')); ?>

                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2" for="distance"><?php echo e(__('taxido::static.hourly_package.distance')); ?><span>
                                *</span></label>
                        <div class="col-md-10">
                            <input class="form-control" type="number" name="distance"
                                value="<?php echo e(isset($hourlyPackage->distance) ? $hourlyPackage->distance : old('distance')); ?>"
                                placeholder="<?php echo e(__('taxido::static.hourly_package.enter_distance')); ?>">
                            <?php $__errorArgs = ['distance'];
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
                            <span class="text-gray mt-1">
                                <?php echo e(__('taxido::static.hourly_package.distance_span')); ?>

                            </span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2"
                            for="distance_type"><?php echo e(__('taxido::static.hourly_package.distance_type')); ?><span>
                                *</span></label>
                        <div class="col-md-10 select-label-error">
                            <span class="text-gray mt-1">
                                <?php echo e(__('taxido::static.hourly_package.distance_type_span')); ?>

                            </span>
                            <select class="select-2 form-control" id="distance_type" name="distance_type"
                                data-placeholder="<?php echo e(__('taxido::static.hourly_package.select_distance_type')); ?>">
                                <option class="select-placeholder" value=""></option>
                                <?php $__currentLoopData = ['km' => 'KM', 'mile' => 'Mile']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option class="option" value="<?php echo e($key); ?>"
                                        <?php if(old('distance_type', $hourlyPackage->distance_type ?? '') == $key): ?> selected <?php endif; ?>><?php echo e($option); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['distance_type'];
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
                    <div class="form-group row" id="vehicle-type-selection">
                        <label class="col-md-2"
                            for="vehicle_type"><?php echo e(__('taxido::static.hourly_package.select_vehicle_type')); ?></label>
                        <div class="col-md-10 select-label-error">

                            <span class="text-gray mt-1">
                                <?php echo e(__('taxido::static.hourly_package.vehicle_type_span')); ?>

                            </span>
                            <select class="form-control select-2" name="vehicle_types[]"
                                data-placeholder="<?php echo e(__('taxido::static.hourly_package.select_vehicle_type')); ?>"
                                multiple>
                                <?php $__currentLoopData = $vehicleTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $vehicleType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($vehicleType->id); ?>"
                                        <?php if(@$hourlyPackage?->vehicle_types): ?> <?php if(in_array($vehicleType->id, $hourlyPackage->vehicle_types->pluck('id')->toArray())): ?>
                                                        selected <?php endif; ?>
                                    <?php elseif(old('vehicle_types.' . $index) == $vehicleType->id): ?> selected <?php endif; ?>>
                                        <?php echo e($vehicleType->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['vehicle_types'];
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
                        <label class="col-md-2" for="role"><?php echo e(__('taxido::static.hourly_package.status')); ?></label>
                        <div class="col-md-10">
                            <div class="editor-space">
                                <label class="switch">
                                    <input class="form-control" type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" name="status" id=""
                                        value="1" <?php if(@$hourlyPackage?->status ?? true): echo 'checked'; endif; ?>>
                                    <span class="switch-state"></span>
                                </label>
                            </div>
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
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="submit-btn">
                                <button type="submit" name="save" class="btn btn-primary spinner-btn">
                                    <i class="ri-save-line text-white lh-1"></i> <?php echo e(__('taxido::static.save')); ?>

                                </button>
                                <button type="submit" name="save_and_exit" class="btn btn-primary spinner-btn">
                                    <i
                                        class="ri-expand-left-line text-white lh-1"></i><?php echo e(__('taxido::static.save_and_exit')); ?>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
    <script>
        (function($) {
            "use strict";
            $('#hourlyPackageForm').validate({
                rules: {
                    "distance": "required",
                    "hour": "required",
                    "distance_type": "required",
                }
            });
        })(jQuery);
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/aakshay/Taxido_laravel/Modules/Taxido/resources/views/admin/hourly-package/fields.blade.php ENDPATH**/ ?>