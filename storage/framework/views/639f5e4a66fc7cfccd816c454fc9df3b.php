<?php $__env->startSection('title', __('static.roles.roles')); ?>
<?php $__env->startSection('content'); ?>
    <div class="contentbox">
        <div class="inside">
            <div class="contentbox-title">
                <div class="contentbox-subtitle">
                    <h3><?php echo e(__('static.roles.roles')); ?></h3>
                    <div class="subtitle-button-group">
                        <button class="add-spinner btn btn-outline" data-url="<?php echo e(route('admin.role.create')); ?>">
                            <i class="ri-add-line"></i> <?php echo e(__('static.roles.add_new')); ?>

                        </button>
                    </div>
                </div>
            </div>
            <div class="role-table">
                <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table.index','data' => ['columns' => $tableConfig['columns'],'data' => $tableConfig['data'],'filters' => $tableConfig['filters'],'actions' => $tableConfig['actions'],'total' => $tableConfig['total'],'bulkactions' => $tableConfig['bulkactions'],'search' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableConfig['columns']),'data' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableConfig['data']),'filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableConfig['filters']),'actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableConfig['actions']),'total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableConfig['total']),'bulkactions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableConfig['bulkactions']),'search' => true]); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $attributes = $__attributesOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $component = $__componentOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__componentOriginal163c8ba6efb795223894d5ffef5034f5); ?>
<?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

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

        $("#role-form").validate({
            ignore: [],
            rules: {
                "name": {
                    required: true
                },
            }
        });

        $('#submitBtn').on('click', function(e) {
            $("#role-form").valid();
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    </script>

<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aakshay/Taxido_laravel/resources/views/admin/role/index.blade.php ENDPATH**/ ?>