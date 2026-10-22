

<?php $__env->startSection('title', __('taxido::front.login')); ?>

<?php $__env->startSection('content'); ?>
    <section class="authentication-section section-b-space">
        <div class="container">
            <?php
                $cabSettings = getSettings();
            ?>

            <div class="auth-form-box">
                <div class="row align-items-center">
                    <div class="col-xl-7 col-lg-6 d-lg-block d-none">
                        <img src="<?php echo e(asset('images/authentication-img.png')); ?>" class="img-fluid auth-image" alt="Authentication">
                    </div>

                    <div class="col-xl-5 col-lg-6">
                        <div class="auth-right-box">
                            <h3><?php echo e(__('taxido::front.welcome_to', ['app_name' => env('APP_NAME')])); ?></h3>

                            <h6><?php echo e(__('taxido::front.account_information')); ?></h6>

                            <form method="POST" id="loginForm">
                                <?php echo csrf_field(); ?>

                                <div class="form-box">
                                    <div class="number-mail-box">
                                        <div class="country-code-section" style="display: block;">
                                            <select class="select-2 form-control"
                                                id="select-country-code"
                                                name="country_code">

                                                <?php $__currentLoopData = getCountryCodes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option
                                                        value="<?php echo e($option->calling_code); ?>"
                                                        data-image="<?php echo e(asset('images/flags/' . $option->flag)); ?>"
                                                        <?php if(
                                                            ltrim((string) $option->calling_code, '+') ===
                                                            ltrim((string) old('country_code', '91'), '+')
                                                        ): echo 'selected'; endif; ?>
                                                    >
                                                        <?php echo e($option->calling_code); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            </select>
                                        </div>

                                        <input
                                            type="text"
                                            class="form-control"
                                            name="email_or_phone"
                                            id="email_or_phone"
                                            placeholder="<?php echo e(__('taxido::front.enter_phone_email')); ?>"
                                            value="<?php echo e(old('email_or_phone')); ?>"
                                            autocomplete="username"
                                        >

                                        <?php $__errorArgs = ['email_or_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-danger"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="btn gradient-bg-color otp-btn"
                                    id="get-otp-btn">
                                    <?php echo e(__('taxido::front.get_otp')); ?>

                                </button>

                                
                                <div id="recaptcha-container" class="mt-3"></div>

                                
                                <div id="firebase-otp-section" style="display: none;">
                                    <div class="form-box mt-3">
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="firebase-otp"
                                            placeholder="Enter 6-digit OTP"
                                            inputmode="numeric"
                                            maxlength="6"
                                            autocomplete="one-time-code"
                                        >
                                    </div>

                                    <button
                                        type="button"
                                        class="btn gradient-bg-color otp-btn mt-3"
                                        id="verify-firebase-otp-btn">
                                        Verify OTP
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-outline mt-2"
                                        id="resend-firebase-otp-btn">
                                        Resend OTP
                                    </button>
                                </div>
                            </form>

                            <div class="or-box">
                                <span><?php echo e(__('taxido::front.login_with')); ?></span>
                            </div>

                            <?php if(isset($cabSettings['activation']['default_credentials'])): ?>
                                <?php if((int) $cabSettings['activation']['default_credentials']): ?>
                                <div class="demo-credential">
                                    <button
                                        type="button"
                                        class="btn btn-outline default-credentials guest-btn">
                                        <span>
                                            <i class="ri-google-fill"></i>
                                        </span>
                                        Login with Google
                                    </button>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <h6 class="new-account">
                                <?php echo e(__('taxido::front.don’t_have_account')); ?>

                                <a href="<?php echo e(route('front.cab.register.index')); ?>">
                                    <?php echo e(__('taxido::front.sign_up')); ?>

                                </a>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

    
    <script src="https://www.gstatic.com/firebasejs/10.14.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.14.1/firebase-auth-compat.js"></script>

    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {

                const firebaseConfig = {
                    apiKey: <?php echo json_encode(config('services.firebase.api_key'), 15, 512) ?>,
                    authDomain: <?php echo json_encode(config('services.firebase.auth_domain'), 15, 512) ?>,
                    projectId: <?php echo json_encode(config('services.firebase.project_id'), 15, 512) ?>,
                    storageBucket: <?php echo json_encode(config('services.firebase.storage_bucket'), 15, 512) ?>,
                    messagingSenderId: <?php echo json_encode(config('services.firebase.messaging_sender_id'), 15, 512) ?>,
                    appId: <?php echo json_encode(config('services.firebase.app_id'), 15, 512) ?>,
                    measurementId: <?php echo json_encode(config('services.firebase.measurement_id'), 15, 512) ?>
                };

                if (!firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                }

                const auth = firebase.auth();

                const csrfToken = <?php echo json_encode(csrf_token(), 15, 512) ?>;
                const loginUrl = <?php echo json_encode(route('front.cab.login_with_credential'), 15, 512) ?>;
                const verifyUrl = <?php echo json_encode(route('front.cab.firebase.verify-otp'), 15, 512) ?>;
                const emailVerifyUrl = <?php echo json_encode(route('front.cab.verify_otp'), 15, 512) ?>;
                const otpButtonText = <?php echo json_encode(__('taxido::front.get_otp'), 15, 512) ?>;
                const failedOtpMessage = <?php echo json_encode(__('taxido::front.failed_resent_otp'), 15, 512) ?>;

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                let confirmationResult = null;
                let recaptchaVerifier = null;
                let isNumb = false;

                function showError(message) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                }

                function showSuccess(message) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(message);
                    } else {
                        alert(message);
                    }
                }

                function getErrorMessage(xhr, fallback) {
                    if (xhr.responseJSON?.message) {
                        return xhr.responseJSON.message;
                    }

                    if (xhr.responseJSON?.errors) {
                        return Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join('\n');
                    }

                    return fallback;
                }

                function setButtonLoading($button, loading, text) {
                    $button.prop('disabled', loading);

                    if (loading) {
                        $button.html(
                            text +
                            ' <span class="spinner-border spinner-border-sm ms-2 spinner_loader"></span>'
                        );
                    } else {
                        $button.text(text);
                    }
                }

                function firebaseErrorMessage(error) {
                    const messages = {
                        'auth/operation-not-allowed': 'Phone sign-in is not enabled for the Firebase project used by this site.',
                        'auth/region-not-allowed': 'Firebase is not allowing SMS delivery to the selected country. Check the SMS region policy for the project used by this site.',
                        'auth/invalid-phone-number': 'Please enter a valid phone number with the selected country code.',
                        'auth/captcha-check-failed': 'reCAPTCHA verification failed. Please try again.',
                        'auth/too-many-requests': 'Too many attempts. Please wait before trying again.',
                        'auth/quota-exceeded': 'SMS sending is temporarily unavailable for this Firebase project.',
                        'auth/network-request-failed': 'Network error. Check your connection and try again.'
                    };

                    return messages[error.code] || 'Unable to complete phone verification. Please try again.';
                }

                function getPhoneNumber() {
                    const countryCode = String(
                        $('#select-country-code').val() || ''
                    ).replace(/\D/g, '');

                    const rawPhone = String($('#email_or_phone').val() || '').trim();
                    let phone = rawPhone.replace(/\D/g, '');

                    if (!countryCode || !phone) {
                        throw new Error('Please enter your country code and phone number.');
                    }

                    // If a user pastes an international number, do not prepend the
                    // selected dial code a second time.
                    if (rawPhone.startsWith('+')) {
                        if (!phone.startsWith(countryCode)) {
                            throw new Error('The phone number country code does not match the selected country.');
                        }
                        phone = phone.slice(countryCode.length);
                    }

                    const fullPhoneNumber = '+' + countryCode + phone;

                    if (!/^\+[1-9]\d{7,14}$/.test(fullPhoneNumber)) {
                        throw new Error('Please enter a valid international phone number.');
                    }

                    return fullPhoneNumber;
                }

                function setupRecaptcha() {
                    if (recaptchaVerifier) {
                        return Promise.resolve();
                    }

                    recaptchaVerifier = new firebase.auth.RecaptchaVerifier(
                        'recaptcha-container',
                        {
                            size: 'normal',
                            callback: function () {},
                            'expired-callback': function () {
                                showError('reCAPTCHA expired. Please verify again.');
                                confirmationResult = null;
                                resetRecaptcha();
                            }
                        }
                    );

                    return recaptchaVerifier.render();
                }

                function resetRecaptcha() {
                    if (recaptchaVerifier) {
                        recaptchaVerifier.clear();
                    }

                    recaptchaVerifier = null;
                    $('#recaptcha-container').empty();
                }

                // Detect email or phone without changing the original UI structure.
                $('#email_or_phone').on('input', function () {
                    const value = $(this).val().trim();
                    const hasLetters = /[a-zA-Z]/.test(value);
                    const isEmail = emailRegex.test(value);

                    if (isEmail || hasLetters) {
                        $('.country-code-section').hide();
                        $('#recaptcha-container').hide();
                        isNumb = false;
                    } else {
                        $('.country-code-section').show();
                        $('#recaptcha-container').show();
                        isNumb = true;
                    }

                    if (confirmationResult) {
                        confirmationResult = null;
                        $('#firebase-otp-section').slideUp();
                        $('#firebase-otp').val('');
                    }

                    $('#email_or_phone').rules('remove', 'minlength maxlength');

                    if (isNumb) {
                        $('#email_or_phone').rules('add', {
                            minlength: 6,
                            maxlength: 15
                        });
                    }
                });

                // Existing validation.
                $('#loginForm').validate({
                    rules: {
                        email_or_phone: {
                            required: true
                        }
                    },
                    messages: {
                        email_or_phone: {
                            required: 'Please enter your email or phone number.',
                            minlength: 'Phone number must be at least 6 digits.',
                            maxlength: 'Phone number cannot exceed 15 digits.'
                        }
                    }
                });

                // Send email OTP through the existing Laravel flow,
                // or send phone OTP through Firebase.
                $('#get-otp-btn').on('click', async function () {
                    if (!$('#loginForm').valid()) {
                        return;
                    }

                    const value = $('#email_or_phone').val().trim();
                    const isEmail = emailRegex.test(value);
                    const $button = $(this);

                    if (isEmail) {
                        $.ajax({
                            url: loginUrl,
                            method: 'POST',
                            data: {
                                email_or_phone: value,
                                country_code: null,
                                _token: csrfToken
                            },
                            beforeSend: function () {
                                setButtonLoading($button, true, otpButtonText);
                            },
                            success: function (response) {
                                if (response.success) {
                                    window.location.href = emailVerifyUrl;
                                } else {
                                    showError(response.message || failedOtpMessage);
                                }
                            },
                            error: function (xhr) {
                                showError(getErrorMessage(xhr, failedOtpMessage));
                            },
                            complete: function () {
                                setButtonLoading($button, false, otpButtonText);
                            }
                        });

                        return;
                    }

                    try {
                        const phoneNumber = getPhoneNumber();

                        setButtonLoading($button, true, otpButtonText);

                        await setupRecaptcha();

                        confirmationResult = await auth.signInWithPhoneNumber(
                            phoneNumber,
                            recaptchaVerifier
                        );

                        $('#firebase-otp-section').slideDown();
                        $('#firebase-otp').trigger('focus');

                        showSuccess('OTP sent successfully.');
                    } catch (error) {
                        console.error('Firebase send OTP error:', error);

                        showError(firebaseErrorMessage(error));

                        resetRecaptcha();
                    } finally {
                        setButtonLoading($button, false, otpButtonText);
                    }
                });

                // Verify Firebase phone OTP and authenticate with Laravel.
                $('#verify-firebase-otp-btn').on('click', async function () {
                    const otp = $('#firebase-otp').val().trim();
                    const $button = $(this);

                    if (!/^\d{6}$/.test(otp)) {
                        showError('Please enter a valid 6-digit OTP.');
                        return;
                    }

                    if (!confirmationResult) {
                        showError('Please request an OTP first.');
                        return;
                    }

                    try {
                        $button.prop('disabled', true).text('Verifying...');

                        const credential = await confirmationResult.confirm(otp);
                        const idToken = await credential.user.getIdToken(true);

                        const selectedCountryCode = String($('#select-country-code').val() || '').replace(/\D/g, '');
                        const fullPhoneNumber = getPhoneNumber();
                        const response = await $.ajax({
                            url: verifyUrl,
                            method: 'POST',
                            contentType: 'application/json',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            data: JSON.stringify({
                                id_token: idToken,
                                email_or_phone: fullPhoneNumber.slice(selectedCountryCode.length + 1),
                                country_code: selectedCountryCode
                            })
                        });

                        if (response.success && response.redirect) {
                            showSuccess(response.message || 'Login successful!');
                            window.setTimeout(function () {
                                window.location.href = response.redirect;
                            }, 1000);
                            return;
                        }

                        showError(response.message || 'Login failed.');
                    } catch (error) {
                        console.error('Firebase OTP verification error:', error);

                        const verificationMessages = {
                            'auth/invalid-verification-code': 'Incorrect OTP. Please enter the latest code.',
                            'auth/code-expired': 'This OTP has expired. Please request a new one.',
                            'auth/session-expired': 'This OTP session has expired. Please request a new one.'
                        };
                        showError(error.responseJSON?.message || verificationMessages[error.code] || firebaseErrorMessage(error));
                    } finally {
                        $button.prop('disabled', false).text('Verify OTP');
                    }
                });

                // Resend phone OTP.
                $('#resend-firebase-otp-btn').on('click', async function () {
                    const $button = $(this);

                    confirmationResult = null;
                    $('#firebase-otp').val('');

                    resetRecaptcha();
                    $('#recaptcha-container').show();

                    $('#get-otp-btn').trigger('click');
                });

                // Existing demo account functionality.

                // Keep the existing Select2 country-code UI.
                $('#select-country-code').select2({
                    templateResult: function (data) {
                        if (!data.id) return data.text;

                        const imageUrl = $(data.element).data('image');

                        return $(`
                            <span>
                                <img
                                    src="${imageUrl}"
                                    class="flag-img"
                                    onerror="this.onerror=null;this.src='<?php echo e(asset('front/images/placeholder/49x37.png')); ?>';"
                                />
                                + ${data.text.trim()}
                            </span>
                        `);
                    },

                    templateSelection: function (data) {
                        if (!data.id) return data.text;

                        const imageUrl = $(data.element).data('image');

                        return $(`
                            <span>
                                <img
                                    src="${imageUrl}"
                                    class="flag-img"
                                    onerror="this.onerror=null;this.src='<?php echo e(asset('front/images/placeholder/49x37.png')); ?>';"
                                />
                                + ${data.text.trim()}
                            </span>
                        `);
                    },

                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

            });
        })(jQuery);
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('front.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\oreodigitalss-project\Taxido_laravel\Modules/Taxido\resources/views/front/auth/login.blade.php ENDPATH**/ ?>