
<!-- jquery JS -->
<script src="<?php echo e(asset('front/js/jquery.js')); ?>"></script>

<!-- Bootstrap JS -->
<script src="<?php echo e(asset('front/js/bootstrap.js')); ?>"></script>

<!-- JQuery Validation js -->
<script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>

<!-- Toaster Js -->
<script src="<?php echo e(asset('js/toastr.min.js')); ?>"></script>

<!-- Swiper JS -->
<script src="<?php echo e(asset('front/js/swiper.js')); ?>"></script>
<script src="<?php echo e(asset('front/js/custom-swiper.js')); ?>"></script>

<!-- Custom Script JS -->
<script src="<?php echo e(asset('front/js/script.js')); ?>"></script>
<script src="<?php echo e(asset('front/js/dark-mode.js')); ?>"></script>

<!-- Select2 -->
<script src="<?php echo e(asset('js/select2.full.min.js')); ?>"></script>


<?php if(Request::is('/')): ?>
    <?php
        $tawkToPropertyId = trim((string) ($content['analytics']['tawk_to_property_id'] ?? ''));
        $tawkToWidgetId = trim((string) ($content['analytics']['tawk_to_widget_id'] ?? ''));
        // Tawk.to embed URLs require the property's generated ID (24 hex chars),
        // not the property name or account email.
        $isValidTawkPropertyId = preg_match('/^[a-f0-9]{24}$/i', $tawkToPropertyId) === 1;
        $isValidTawkWidgetId = preg_match('/^[a-z0-9]{8}$/i', $tawkToWidgetId) === 1;
    ?>

    <?php if($isValidTawkPropertyId && $isValidTawkWidgetId): ?>
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
            var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
            (function() {
                var s1 = document.createElement("script"),
                    s0 = document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src = 'https://embed.tawk.to/<?php echo e($tawkToPropertyId); ?>/<?php echo e($tawkToWidgetId); ?>';
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s0.parentNode.insertBefore(s1, s0);
            })();
        </script>
    <?php endif; ?>
<?php endif; ?>

<?php echo $__env->yieldPushContent('js'); ?>
<?php /**PATH D:\oreodigitalss-project\Taxido_laravel\resources\views/front/layouts/script.blade.php ENDPATH**/ ?>