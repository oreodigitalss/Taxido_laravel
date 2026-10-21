<?php $__env->startSection('title', __('taxido::static.hourly_package.add')); ?>
<?php $__env->startSection('content'); ?>
    <div class="banner-create">
        <form id="hourlyPackageForm" action="<?php echo e(route('admin.hourly-package.store')); ?>" method="POST"
            enctype="multipart/form-data">
            
            <?php echo method_field('POST'); ?>
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('taxido::admin.hourly-package.fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aakshay/Taxido_laravel/Modules/Taxido/resources/views/admin/hourly-package/create.blade.php ENDPATH**/ ?>