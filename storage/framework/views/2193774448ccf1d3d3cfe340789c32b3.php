<div class="row">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="contentbox">
            <div class="inside">
                <div class="contentbox-title">
                    <h3><?php echo e(isset($role) ? __('static.roles.edit_role') : __('static.roles.add_role')); ?></h3>
                </div>
                <div class="roles">
                    <div class="roles-body mb-sm-4 mb-3">
                        <div class="form-group row">
                            <label class="col-md-2" for="name"><?php echo e(__('static.roles.name')); ?><span> *</span></label>
                            <div class="col-md-10">
                                <div class="position-relative">
                                    <input class="form-control" value="<?php echo e(@$role->name ?? old('name')); ?>" type="text"
                                        name="name" placeholder="<?php echo e(__('static.roles.enter_role_name')); ?>" required>
                                    <div class="invalid-feedback"><?php echo e(__('static.roles.name_is_required')); ?></div>
                                </div>
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
                    </div>
                    <div class="permission mt-sm-4 mt-3">
                        <h5><?php echo e(__('static.roles.permissions')); ?><span> *</span></h5>
                        <?php $__errorArgs = ['permission[]'];
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
                        <div class="permission-section">
                            <ul class="custom-scrollbar">
                                <li>
                                    <label for="all_permissions">
                                        <h5><?php echo e(__('static.roles.select_all_permissions')); ?> &nbsp;<input type="checkbox"
                                                id="all_permissions" class="checkbox_animated"></h5>
                                    </label>
                                </li>
                                <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="h-custom-scrollbar">
                                        <h5 class="text-truncate"><?php echo e(ucwords(str_replace('_', ' ', $module->name))); ?>:
                                        </h5>
                                        <div class="form-group m-checkbox-inline mb-0 d-flex">
                                            <?php
                                                $permissions =
                                                    @$role?->getAllPermissions()->pluck('name')->toArray() ?? [];
                                                $isAllSelected =
                                                    count(array_diff(array_values($module->actions), $permissions)) ===
                                                    0;
                                            ?>
                                            <label class="d-block" for="all-<?php echo e($module->name); ?>">
                                                <input type="checkbox"
                                                    class="checkbox_animated select-all-permission select-all-for-<?php echo e($module->name); ?>"
                                                    id="all-<?php echo e($module->name); ?>" value="<?php echo e($module->name); ?>"
                                                    <?php echo e($isAllSelected ? 'checked' : ''); ?>><?php echo e(__('static.roles.all')); ?>

                                            </label>
                                            <?php $__currentLoopData = $module->actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action => $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <label class="d-block" for="<?php echo e($permission); ?>"
                                                    data-action="<?php echo e($action); ?>"
                                                    data-module="<?php echo e($module->name); ?>">
                                                    <input type="checkbox" name="permissions[]"
                                                        class="checkbox_animated module_<?php echo e($module->name); ?> module_<?php echo e($module->name); ?>_<?php echo e($action); ?>"
                                                        value="<?php echo e($permission); ?>" id="<?php echo e($permission); ?>"
                                                        <?php echo e(in_array($permission, $permissions) ? 'checked' : ''); ?>

                                                        required><?php echo e($action); ?>

                                                </label>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row mt-sm-4 mt-3">
                        <div class="col-12">
                            <div class="submit-btn">
                                <button type="submit" name="save" class="btn btn-primary spinner-btn">
                                    <i class="ri-save-line text-white lh-1"></i> <?php echo e(__('static.save')); ?>

                                </button>
                                <button type="submit" name="save_and_exit" class="btn btn-primary spinner-btn">
                                    <i class="ri-expand-left-line text-white lh-1"></i><?php echo e(__('static.save_and_exit')); ?>

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
        $(document).ready(function() {
            'use strict';
            $(document).on('click', '.select-all-permission', function() {
                let value = $(this).prop('value');
                $('.module_' + value).prop('checked', $(this).prop('checked'));
                updateGlobalSelectAll();
            });
            $('.checkbox_animated').not('.select-all-permission').on('change', function() {
                let $this = $(this);
                let $label = $this.closest('label');
                let module = $label.data('module');
                let action = $label.data('action');
                let total_permissions = $('.module_' + module).length;
                let $selectAllCheckBox = $this.closest('.' + module + '-permission-list').find(
                    '.select-all-permission');
                let total_checked = $('.module_' + module).filter(':checked').length;
                let isAllChecked = total_checked === total_permissions;
                if ($this.prop('checked')) {
                    $('.module_' + module + '_index').prop('checked', true);

                } else {
                    let moduleCheckboxes = $(`input[type="checkbox"][data-module="${module}"]:checked`);
                    if (moduleCheckboxes.length === 0) {
                        if (action === 'index') {
                            $('.module_' + module).prop('checked', false);
                        }
                        $(`.module_${module}_${action}`).prop('checked', false);
                        $('.select-all-for-' + module).prop('checked', false);
                    }
                }

                $('.select-all-for-' + module).prop('checked', isAllChecked);
                updateGlobalSelectAll();
            });

            $('#roleForm').validate({});
        });

        $('#all_permissions').on('change', function() {
                $('.checkbox_animated').prop('checked', $(this).prop('checked'));
        });

        function updateGlobalSelectAll() {
                let allChecked = true;
                $('.select-all-permission').each(function() {
                    if (!$(this).prop('checked')) {
                        allChecked = false;
                    }
                });
                $('#all_permissions').prop('checked', allChecked);
            }

        $("#roleForm").validate({
            ignore: [],
            rules: {
                "name": {
                    required: true
                },
            }
        });

        $('#submitBtn').on('click', function(e) {
            $("#roleForm").valid();
        });

            $('[data-bs-toggle="tooltip"]').tooltip();

    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\oreodigitalss-project\Taxido_laravel\resources\views/admin/role/fields.blade.php ENDPATH**/ ?>