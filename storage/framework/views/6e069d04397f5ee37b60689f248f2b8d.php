<?php $__env->startSection('title',  __('taxido::static.rental_vehicle.add')); ?>
<?php $__env->startSection('content'); ?>
<div class="banner-create">
    <form id="rentalVehicleForm" action="<?php echo e(route('admin.rental-vehicle.store')); ?>" method="POST" enctype="multipart/form-data">
        <div class="row g-xl-4 g-3">
            <?php echo method_field('POST'); ?>
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('taxido::admin.rental-vehicle.fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aakshay/Taxido_laravel/Modules/Taxido/resources/views/admin/rental-vehicle/create.blade.php ENDPATH**/ ?>