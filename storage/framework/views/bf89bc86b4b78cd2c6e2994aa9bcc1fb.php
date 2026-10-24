
<?php $__env->startSection('title', __('static.roles.add_role')); ?>
<?php $__env->startSection('content'); ?>
<div>
    <form id="roleForm" action="<?php echo e(route('admin.role.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo $__env->make('admin.role.fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\oreodigitalss-project\Taxido_laravel\resources\views/admin/role/create.blade.php ENDPATH**/ ?>