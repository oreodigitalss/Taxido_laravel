<?php

namespace Modules\Taxido\Http\Controllers\Front;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Taxido\Models\Rider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Taxido\Emails\VerifyEmail;
use Illuminate\Support\Facades\Validator;
use Modules\Taxido\Services\FirebasePhoneVerifier;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('front.cab.dashboard.index');
        }

        return response()->view('taxido::front.auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('front.cab.dashboard.index');
        }
        return response()->view('taxido::front.auth.register')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function loginWithCredential(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email_or_phone' => 'required|string',
                'country_code' => 'nullable|required_if:email_or_phone,phone|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $emailOrPhone = trim($request->email_or_phone);
            if (str_contains($emailOrPhone, '@') && !filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['success' => false, 'message' => 'Please enter a valid email address.'], 422);
            }
            $isEmail = filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL);
            if ($isEmail) {
                $emailOrPhone = mb_strtolower($emailOrPhone);
                $request->session()->put('email_or_phone', $emailOrPhone);
                $request->session()->put('otp_login_method', 'email');
                return $this->sendEmailToken($emailOrPhone);
            } else {
                if(strlen((string)$emailOrPhone) < 6 || strlen((string)$emailOrPhone) > 15) {
                    return response()->json(['success' => false, 'message' => "Phone number must be 6 to 15 digits."], 422);
                }

                if (!$request->country_code) {
                    return response()->json(['success' => false, 'message' => 'Country code is required for phone login.'], 422);
                }

                $request->session()->put('email_or_phone', $emailOrPhone);
                $request->session()->put('otp_login_method', 'phone');
                $request->session()->put('country_code', $request->country_code);
            }

            return $this->sendPhoneToken($request->country_code, $emailOrPhone);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function sendEmailToken($email)
    {
        DB::table('auth_tokens')->where('email', $email)->delete();
        $token = $this->generateToken(email: $email, forceRandom: true);

        try {
            // Email login always uses a real, random OTP and sends it now. Demo
            // mode must not replace it with the phone/SMS demo code or skip mail.
            Mail::to($email)->send(new VerifyEmail($token));
        } catch (Exception $e) {
            DB::table('auth_tokens')->where('email', $email)->where('token', $token)->delete();
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'We could not send the email verification code. Please check the mail configuration and try again.',
            ], 503);
        }

        return response()->json(['message' => 'OTP sent successfully to your email!', 'success' => true], 200);
    }

    private function sendPhoneToken($country_code, $phone)
    {
        $token = $this->generateToken($country_code, $phone);
        if(!isDemoMode()) {
            sendSMS('+' . $country_code . $phone, $token);
        }
        return response()->json(['message' => 'OTP sent successfully to your phone!', 'success' => true], 200);
    }

    public function generateToken($country_code = null, $phone = null, $email = null, bool $forceRandom = false)
    {
        $token = random_int(100000, 999999);
        if (isDemoMode() && !$forceRandom) {
            $token = 123456;
        }
        DB::table('auth_tokens')->insert([
            'token' => $token,
            'phone' => '+' . $country_code . $phone,
            'email' => $email,
            'created_at' => Carbon::now()
        ]);

        return $token;
    }

    public function verifyOtp()
    {
        if (!session('email_or_phone')) {
            return redirect()->route('front.cab.login.index')->with('error', 'Please request an OTP first.');
        }

        return view('taxido::front.auth.verify_otp');
    }

    public function verifyOtpStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|array|size:6',
                'otp.*' => 'required|digits:1',
                'email_or_phone' => 'required|string|max:255',
                'country_code' => 'nullable',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $otp = implode('', $request->otp);
            $input = trim($request->email_or_phone);
            $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
            if ($isEmail) {
                $input = mb_strtolower($input);
                if ($request->session()->get('otp_login_method') !== 'email'
                    || !hash_equals((string) $request->session()->get('email_or_phone', ''), $input)) {
                    return redirect()->route('front.cab.login.index')->with('error', 'Please request a new email verification code.');
                }
            }
            $countryCode = preg_replace('/\D+/', '', (string) $request->country_code);
            $nationalPhone = preg_replace('/\D+/', '', (string) $input);
            $phone = $isEmail ? null : '+' . $countryCode . $nationalPhone;

            $verify_otp = DB::table('auth_tokens')
                ->where('token', $otp)
                ->where(function ($query) use ($input, $isEmail, $phone) {
                    if ($isEmail) {
                        $query->where('email', $input);
                    } else {
                        $query->where('phone', $phone);
                    }
                })
                ->where('created_at', '>', Carbon::now()->subHours(1))
                ->first();

            if (!$verify_otp) {
                return redirect()->back()->with('error', __('taxido::front.otp_invalid'));
            }

            $riderQuery = Rider::whereNull('deleted_at');
            if ($isEmail) {
                $riderQuery->where('email', $input);
            } else {
                $riderQuery->where('phone', $nationalPhone)->where('country_code', $countryCode);
            }
            $rider = $riderQuery->first();

            if (!$rider) {
                return redirect()->route('front.cab.login.index')->with('error', __('taxido::front.user_not_found'));
            }

            if (!$rider->status) {
                return redirect()->route('front.cab.login.index')->with('error', 'Account is disabled.');
            }

            if ($isEmail) {
                // auth_tokens has no primary key; consume by email + token.
                DB::table('auth_tokens')->where('email', $input)->where('token', $otp)->delete();
                $request->session()->forget(['email_or_phone', 'otp_login_method']);
            }

            Auth::login($rider);
            $request->session()->regenerate();
            return redirect()->route('front.cab.dashboard.index')->with('success', 'Login successful!');
        } catch (Exception $e) {
            return back()->withErrors(['otp' => 'An error occurred. Please try again.']);
        }
    }

    public function loginWithGoogle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string|max:8192',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $identity = app(\Modules\Taxido\Services\FirebaseGoogleTokenVerifier::class)
                ->identity($request->id_token);

            if (!$identity) {
                return response()->json(['success' => false, 'message' => 'Google sign-in could not be verified. Please try again.'], 401);
            }

            $riders = Rider::whereRaw('LOWER(TRIM(email)) = ?', [$identity['email']])
                ->limit(2)
                ->get();

            if ($riders->count() > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'More than one rider account uses this email. Please contact support to resolve the duplicate accounts.',
                ], 409);
            }

            $rider = $riders->first();
            if (!$rider) {
                return response()->json([
                    'success' => false,
                    'message' => 'This Google email is verified in Firebase, but no rider account is registered in this app with that email. Please sign up in the app first.',
                ], 404);
            }

            if (!$rider->status) {
                return response()->json(['success' => false, 'message' => 'This account is disabled. Please contact support.'], 403);
            }

            $linkedAccount = \App\Models\User::where('firebase_uid', $identity['uid'])
                ->where('id', '<>', $rider->id)
                ->exists();
            if ($linkedAccount) {
                return response()->json(['success' => false, 'message' => 'This Google account is linked to a different user.'], 409);
            }

            // Keep an existing phone-auth UID intact; the verified Google email
            // identifies this Rider, and we only store a UID when none is set.
            if (!$rider->firebase_uid) {
                $rider->forceFill(['firebase_uid' => $identity['uid']])->save();
            }

            Auth::login($rider);
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Google login successful!',
                'redirect' => route('front.cab.dashboard.index'),
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Unable to verify Google sign-in right now. Please try again shortly.'], 503);
        }
    }

    public function verifyFirebaseOtp(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'id_token' => 'required|string',
                'email_or_phone' => 'required|string',
                'country_code' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $input = $request->email_or_phone;
            $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
            if ($isEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'This verification endpoint is only for Firebase phone sign-in.'
                ], 422);
            }

            $countryCode = preg_replace('/\D+/', '', (string) $request->country_code);
            $nationalPhone = preg_replace('/\D+/', '', (string) $input);
            $phone = '+' . $countryCode . $nationalPhone;
            if (strlen($nationalPhone) < 6 || strlen($nationalPhone) > 15) {
                return response()->json(['success' => false, 'message' => "Phone number must be 6 to 15 digits."], 422);
            }
            if (!$countryCode) {
                return response()->json(['success' => false, 'message' => 'Country code is required.'], 422);
            }

            // Verify the Firebase credential server side and bind it to the submitted phone.
            $firebaseIdentity = app(FirebasePhoneVerifier::class)->identity($request->id_token);
            if (!$firebaseIdentity) {
                return response()->json([
                    'success' => false,
                    'message' => 'This OTP is expired or belongs to an old Firebase session. Reload this page and request a new OTP.'
                ], 401);
            }
            if ($firebaseIdentity['phone_number'] !== $phone) {
                return response()->json(['success' => false, 'message' => 'Verified phone number does not match.'], 401);
            }

            $riderQuery = Rider::whereNull('deleted_at');
            $riderQuery->where('phone', $nationalPhone)->where('country_code', $countryCode);
            $rider = $riderQuery->first();
            if (!$rider) {
                return response()->json([
                    'success' => true,
                    'message' => 'User not found. Please register first.',
                    'redirect' => route('front.cab.register.index')
                ]);
            } else {
                if (!$rider->status) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Account is disabled.'
                    ], 403);
                }

                if ($rider->firebase_uid && $rider->firebase_uid !== $firebaseIdentity['uid']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This phone number is linked to a different Firebase account.'
                    ], 401);
                }

                if (!$rider->firebase_uid) {
                    $rider->forceFill(['firebase_uid' => $firebaseIdentity['uid']])->save();
                }
            }

            Auth::login($rider);
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => route('front.cab.dashboard.index')
            ]);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to verify OTP right now. Please try again shortly.'
            ], 503);
        }
    }
}
