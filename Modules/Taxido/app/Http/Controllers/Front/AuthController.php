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

            $emailOrPhone = $request->email_or_phone;
            $isEmail = filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL);
            $request->session()->put('email_or_phone', $emailOrPhone);
            if (!$isEmail) {
                if(strlen((string)$emailOrPhone) < 6 || strlen((string)$emailOrPhone) > 15) {
                    return response()->json(['success' => false, 'message' => "Phone number must be 6 to 15 digits."], 422);
                }

                $request->session()->put('country_code', $request->country_code);
            }

            if ($isEmail) {
                return $this->sendEmailToken($emailOrPhone);
            } else {
                if (!$request->country_code) {
                    return response()->json(['success' => false, 'message' => 'Country code is required for phone login.'], 422);
                }
                return $this->sendPhoneToken($request->country_code, $emailOrPhone);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function sendEmailToken($email)
    {
        $token = $this->generateToken(email: $email);
        if(!isDemoMode()) {
            Mail::to($email)->queue(new VerifyEmail($token));
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

    public function generateToken($country_code = null, $phone = null, $email = null)
    {
        $token = rand(111111, 999999);
        if (isDemoMode()) {
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
                'email_or_phone' => 'required',
                'country_code' => 'nullable',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $otp = implode('', $request->otp);
            $input = $request->email_or_phone;
            $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);
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

            Auth::login($rider);
            $request->session()->regenerate();
            return redirect()->route('front.cab.dashboard.index')->with('success', 'Login successful!');
        } catch (Exception $e) {
            return back()->withErrors(['otp' => 'An error occurred. Please try again.']);
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
