<?php

namespace Modules\Taxido\Http\Controllers\Front;

use Exception;
use Illuminate\Http\Request;
use Modules\Taxido\Models\Rider;
use Illuminate\Support\Facades\DB;
use Modules\Taxido\Enums\RoleEnum;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Taxido\Services\FirebasePhoneVerifier;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
                'phone' => 'required|min:6|max:15|unique:users,phone,NULL,id,deleted_at,NULL',
                'country_code' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
                'name' => 'required|string|max:255',
                'firebase_id_token' => 'required|string',
            ]);

            // The browser check improves the form flow; this server check is the trust boundary.
            $firebaseIdentity = app(FirebasePhoneVerifier::class)->identity($validated['firebase_id_token']);
            $submittedPhone = '+' . preg_replace('/\D+/', '', $validated['country_code'])
                . preg_replace('/\D+/', '', $validated['phone']);
            if (!$firebaseIdentity || $firebaseIdentity['phone_number'] !== $submittedPhone) {
                throw new Exception('Please verify the phone number before registering.');
            }

            if (\App\Models\User::where('firebase_uid', $firebaseIdentity['uid'])->exists()) {
                throw new Exception('This Firebase account is already linked to another user.');
            }

            $rider = Rider::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country_code' => $validated['country_code'],
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
