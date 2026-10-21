<?php $__env->startSection('title', __('static.notify_templates.email')); ?>

<?php $__env->startSection('content'); ?>
    <?php $__empty_1 = true; $__currentLoopData = $emailTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $emailTemplate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="contentbox">
            <div class="inside">
                <div class="contentbox-title">
                    <div class="contentbox-subtitle">
                        <h3><?php echo e($emailTemplate['name']); ?></h3>
                    </div>
                </div>
                <div class="table-main template-table notify-template-table m-0">
                    <div class="table-responsive custom-scrollbar m-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('static.notify_templates.name')); ?></th>
                                    <th><?php echo e(__('static.notify_templates.description')); ?></th>
                                    <th><?php echo e(__('static.status')); ?></th>
                                    <th><?php echo e(__('static.notify_templates.action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_2 = true; $__currentLoopData = $emailTemplate['templates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <?php
                                        $status = $statuses[$template['slug']] ?? true;
                                    ?>
                                    <tr>
                                        <td><?php echo e($template['name']); ?></td>
                                        <td><?php echo e($template['description']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($status ? 'success' : 'danger'); ?>">
                                                <?php echo e($status ? __('static.active') : __('static.deactive')); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('admin.email-template.edit', ['slug' => $template['slug']])); ?>"
                                                class="btn btn-link" title="Edit">
                                                <svg>
                                                    <use xlink:href="<?php echo e(asset('images/svg/edit.svg#edit')); ?>"></use>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aakshay/Taxido_laravel/resources/views/admin/email-template/index.blade.php ENDPATH**/ ?>