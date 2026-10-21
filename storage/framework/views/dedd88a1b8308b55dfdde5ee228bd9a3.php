<?php $__env->startSection('title', __('taxido::static.airports.add_airport')); ?>
<?php $__env->startSection('content'); ?>
<?php
    $settings = getTaxidoSettings();
?>
<div class="row">
    <div class="m-auto col-12-8">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-xl-7 order-xl-1 order-last">
                    <form id="airportForm" action="<?php echo e(route('admin.airport.store')); ?>" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo $__env->make('taxido::admin.airport.fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </form>
                </div>
                <div class="col-xl-5 order-xl-2 order-1">
                    <div class="map-instruction">
                        <h4><?php echo e(__('taxido::static.zones.map_instruction_heading')); ?></h4>
                        <p><?php echo e(__('taxido::static.zones.map_instruction_title')); ?></p>
                        <div class="map-detail">
                            <i class="ri-drag-move-fill"></i>
                            <?php echo e(__('taxido::static.zones.map_instruction_paragraph_1')); ?>

                        </div>
                        <div class="map-detail">
                            <i class="ri-map-pin-line"></i>
                            <?php echo e(__('taxido::static.zones.map_instruction_paragraph_2')); ?>

                        </div>

                        <?php if($settings['location']['map_provider'] == 'google_map'): ?>
                            <img src="<?php echo e(Module::asset('taxido:images/taxido-map.gif')); ?>" class="notify-img">
                        <?php elseif($settings['location']['map_provider'] == 'osm'): ?>
                            <img src="<?php echo e(Module::asset('taxido:images/taxido-osm.gif')); ?>" class="notify-img">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aakshay/Taxido_laravel/Modules/Taxido/resources/views/admin/airport/create.blade.php ENDPATH**/ ?>