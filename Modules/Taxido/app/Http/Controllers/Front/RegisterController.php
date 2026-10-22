<?php

namespace Modules\Taxido\Http\Controllers\Front;

use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use Modules\Taxido\Models\Rider;
use Illuminate\Support\Facades\DB;
use Modules\Taxido\Enums\RoleEnum;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Taxido\Services\FirebasePhoneVerifier;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $phone = preg_replace('/\D+/', '', (string) $request->phone);
            $countryCode = preg_replace('/\D+/', '', (string) $request->country_code);

            // If this verified number already belongs to a rider, return them to
            // login with a clear message instead of a generic unique error.
            $existingAccount = User::whereNull('deleted_at')
                ->where('phone', $phone)
                ->where('country_code', $countryCode)
                ->first();

            if ($existingAccount) {
                DB::rollBack();

                return redirect()->route('front.cab.login.index')
                    ->with('error', 'An account already exists with this phone number. Please log in.');
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'phone' => 'required|min:6|max:15|unique:users,phone,NULL,id,deleted_at,NULL',
                'country_code' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
                'name' => 'required|string|max:255',
                'firebase_id_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $validated = $validator->validated();

            // The browser check improves the form flow; this server check is the trust boundary.
            $firebaseIdentity = app(FirebasePhoneVerifier::class)->identity($validated['firebase_id_token']);
            $submittedPhone = '+' . $countryCode . $phone;
            if (!$firebaseIdentity || $firebaseIdentity['phone_number'] !== $submittedPhone) {
                throw new Exception('Please verify the phone number before registering.');
            }

            if (\App\Models\User::where('firebase_uid', $firebaseIdentity['uid'])->exists()) {
                throw new Exception('This Firebase account is already linked to another user.');
            }

            $rider = Rider::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $phone,
                'country_code' => $countryCode,
                'password' => Hash::make($validated['password']),
                'referral_code' => $this->getReferralCode($validated['name']),
                'status' => true,
            ]);

            $rider->assignRole(RoleEnum::RIDER);
            $rider->forceFill(['firebase_uid' => $firebaseIdentity['uid']])->save();
            DB::commit();

            Auth::login($rider);
            $request->session()->regenerate();
            return redirect()->route('front.cab.dashboard.index')->with('success', 'Registration successful!');
        } catch (Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    private function getReferralCode($name)
    {
        return strtoupper(substr($name, 0, 3)) . rand(1000, 9999);
    }
}
