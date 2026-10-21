<?php $__env->startSection('title', __('taxido::static.vehicle_types.add')); ?>
<?php $__env->startSection('content'); ?>
    <div class="vehicle-create">
        <form id="vehicleTypeForm" action="<?php echo e(route('admin.vehicle-type.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo method_field('POST'); ?>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="req_service" value="<?php echo e(request()->service); ?>" />
            <?php echo $__env->make('taxido::admin.vehicle-type.fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
        <?php echo $__env->make('taxido::admin.vehicle-type.zone-price', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aakshay/Taxido_laravel/Modules/Taxido/resources/views/admin/vehicle-type/create.blade.php ENDPATH**/ ?>