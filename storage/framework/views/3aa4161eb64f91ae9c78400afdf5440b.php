<div class="row g-xl-4 g-3">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="left-part">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3><?php echo e(isset($rider) ? __('taxido::static.riders.edit') : __('taxido::static.riders.add')); ?>

                        </h3>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2" for="profile_image_id"><?php echo e(__('taxido::static.drivers.profile_image')); ?><span>*</span></label>
                        <div class="col-md-10">
                            <div class="form-group">
                                <?php if (isset($component)) { $__componentOriginal22d447e3f5aafc93b8447b54b36ee789 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.image','data' => ['text' => __('static.svg_not_supported'),'unallowedTypes' => ['svg'],'name' => 'profile_image_id','data' => isset($rider->profile_image)? $rider?->profile_image : old('profile_image_id'),'multiple' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('static.svg_not_supported')),'unallowed_types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['svg']),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('profile_image_id'),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($rider->profile_image)? $rider?->profile_image : old('profile_image_id')),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $attributes = $__attributesOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__attributesOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789)): ?>
<?php $component = $__componentOriginal22d447e3f5aafc93b8447b54b36ee789; ?>
<?php unset($__componentOriginal22d447e3f5aafc93b8447b54b36ee789); ?>
<?php endif; ?>
                                <?php $__errorArgs = ['profile_image_id'];
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
                        <label class="col-md-2" for="name"><?php echo e(__('taxido::static.riders.full_name')); ?><span>
                                *</span></label>
                        <div class="col-md-10">
                            <input class="form-control" value="<?php echo e(isset($rider->name) ? $rider->name : old('name')); ?>"
                                type="text" name="name"
                                placeholder="<?php echo e(__('taxido::static.riders.enter_full_name')); ?>">
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
                        <label class="col-md-2" for="email"><?php echo e(__('taxido::static.riders.email')); ?><span>*</span></label>
                        <div class="col-md-10">
                            <?php if(isset($rider) && isDemoModeEnabled()): ?>
                            <input class="form-control" value="<?php echo e(__('static.demo_mode')); ?>" type="text" readonly>
                            <?php else: ?>
                            <input class="form-control" value="<?php echo e(isset($rider->email) ? $rider->email : old('email')); ?>" type="email" name="email" id="email" placeholder="<?php echo e(__('taxido::static.riders.enter_email')); ?>" required style="text-transform: lowercase;"
                                oninput="this.value = this.value.toLowerCase();">
                            <?php endif; ?>
                            <?php $__errorArgs = ['email'];
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
                        <label class="col-md-2" for="phone"><?php echo e(__('taxido::static.riders.phone')); ?><span>*</span></label>
                        <div class="col-md-10">
                            <?php if(isset($rider) && isDemoModeEnabled()): ?>
                            <input class="form-control" value="<?php echo e(__('static.demo_mode')); ?>" type="text" readonly>
                            <?php else: ?>
                            <div class="input-group mb-3 phone-detail">
                                <div class="col-sm-1">
                                    <select class="select-2 form-control" id="select-country-code" name="country_code">
                                        <?php $__currentLoopData = getCountryCodes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option class="option" value="<?php echo e($option->calling_code); ?>"
                                            data-image="<?php echo e(asset('images/flags/' . $option->flag)); ?>"
                                            <?php if($option->calling_code == old('country_code', $rider->country_code ?? '1')): echo 'selected'; endif; ?>>
                                            <?php echo e($option->calling_code); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-sm-11">
                                    <input class="form-control" type="number" name="phone"
                                        value="<?php echo e(old('phone', $rider->phone ?? '')); ?>"
                                        placeholder="<?php echo e(__('taxido::static.riders.enter_phone')); ?>">
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php $__errorArgs = ['phone'];
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
                        <label class="col-md-2" for="referral_code"><?php echo e(__('taxido::static.referrals.referral_code')); ?></label>
                        <div class="col-md-10">
                            <input class="form-control" value="<?php echo e(old('referral_code', $rider->referral_code ?? '')); ?>"
                                type="text" name="referral_code" id="referral_code"
                                placeholder="<?php echo e(__('taxido::static.referrals.referral_code')); ?>">
                            <?php $__errorArgs = ['referral_code'];
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
                    <?php if(request()->routeIs('admin.rider.create')): ?>
                    <div class="form-group row">
                        <label class="col-md-2" for="password"><?php echo e(__('taxido::static.riders.new_password')); ?><span>
                                *</span></label>
                        <div class="col-md-10">
                            <div class="position-relative">
                                <input class="form-control" type="password" id="password" name="password"
                                    placeholder="<?php echo e(__('taxido::static.riders.enter_password')); ?>">
                                <i class="ri-eye-line toggle-password"></i>
                            </div>
                            <?php $__errorArgs = ['password'];
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
                            for="confirm_password"><?php echo e(__('taxido::static.riders.confirm_password')); ?><span>
                                *</span></label>
                        <div class="col-md-10">
                            <div class="position-relative">
                                <input class="form-control" type="password" name="confirm_password"
                                    placeholder="<?php echo e(__('taxido::static.riders.enter_confirm_password')); ?>"
                                    required>
                                <i class="ri-eye-line toggle-password"></i>
                            </div>
                            <?php $__errorArgs = ['confirm_password'];
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
                        <label class="col-md-2 mb-0"
                            for="notify"><?php echo e(__('taxido::static.riders.notification')); ?></label>
                        <div class="col-md-10">
                            <div class="form-check p-0 w-auto">
                                <input type="checkbox" name="notify" id="notify" value="1"
                                    class="form-check-input me-2">
                                <label for="notify"><?php echo e(__('taxido::static.riders.sentence')); ?></label>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group row">
                        <label class="col-md-2" for="role"><?php echo e(__('taxido::static.status')); ?></label>
                        <div class="col-md-10">
                            <div class="editor-space">
                                <label class="switch">
                                    <input class="form-control" type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" name="status" id=""
                                        value="1" <?php if(@$rider?->status ?? true): echo 'checked'; endif; ?>>
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
        $('#riderForm').validate({
            rules: {
                "name": "required",
                "email": "required",
                "role_id": "required",
                "phone": {
                    "required": true,
                    "minlength": 6,
                    "maxlength": 15
                },
                "password": {
                    "required": true,
                    "minlength": 8
                },
                "confirm_password": {
                    "required": true,
                    "equalTo": "#password"
                }
            },
        });

        $('#userForm').on('submit', function() {
            let emailInput = $('#email');
            emailInput.val(emailInput.val().toLowerCase());
        });

    })(jQuery);
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\oreodigitalss-project\Taxido_laravel\Modules/Taxido\resources/views/admin/rider/fields.blade.php ENDPATH**/ ?>