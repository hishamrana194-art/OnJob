<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterEmployerRequest;
use App\Http\Requests\Api\RegisterVendorRequest;
use App\Models\Employer;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\EmailVerificationToken;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * Register a new employer with admin user
     */
    public function registerEmployer(RegisterEmployerRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Create Employer (pending status)
            $employer = Employer::create([
                'name' => ['en' => $data['company']['name']],
                'email' => $data['company']['email'],
                'phone_number' => $data['company']['phone'] ?? null,
                'country_id' => $data['company']['country_id'],
                'company' => ['en' => $data['company']['name']],
                'address' => null,
                'status' => 'pending',
            ]);

            // 2. Get Package and create Subscription
            $package = Package::findOrFail($data['package_id']);
            $price = $data['billing_period'] === 'monthly'
                ? $package->price_monthly
                : $package->price_yearly;

            $subscription = Subscription::create([
                'subscribable_id' => $employer->id,
                'subscribable_type' => Employer::class,
                'package_id' => $package->id,
                'status' => 'pending', // Will be activated after verification
                'seats_limit' => $package->max_employees,
                'seats_used' => 0,
                'billing_period' => $data['billing_period'],
                'price_per_period' => $price,
                'started_at' => now(),
                'ends_at' => $data['billing_period'] === 'monthly'
                    ? now()->addMonth()
                    : now()->addYear(),
            ]);

            // 3. Create Admin User (inactive)
            $user = User::create([
                'name' => $data['admin']['name'],
                'email' => $data['admin']['email'],
                'phone' => $data['admin']['phone'] ?? null,
                'password' => Hash::make($data['admin']['password']),
                'user_type' => 'employer_user',
                'employer_id' => $employer->id,
                'vendor_id' => null,
                'is_active' => false, // Inactive until verified
                'email_verified_at' => null,
            ]);

            // 4. Assign employer_admin role
            $user->assignRole('employer_admin');

            // 5. Create verification token
            $verificationToken = EmailVerificationToken::createForUser($user, $employer);

            // 6. Send verification email
            $user->notify(new VerifyEmailNotification($verificationToken));

            DB::commit();

            return response()->api(
                'Registration successful. Please check your email to verify your account.',
                [
                    'employer_id' => $employer->id,
                    'verification_required' => true,
                    'email' => $user->email,
                ],
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->api(
                'Registration failed: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Register a new vendor with admin user
     */
    public function registerVendor(RegisterVendorRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Create Vendor (pending status)
            $vendor = Vendor::create([
                'name' => ['en' => $data['vendor']['name']],
                'email' => $data['vendor']['email'],
                'phone_number' => $data['vendor']['phone'] ?? null,
                'country_id' => $data['vendor']['country_id'],
                'website' => $data['vendor']['website'] ?? null,
                'description' => isset($data['vendor']['description'])
                    ? ['en' => $data['vendor']['description']]
                    : null,
                'status' => 'pending',
            ]);

            // 2. Get Package and create Subscription
            $package = Package::findOrFail($data['package_id']);
            $price = $data['billing_period'] === 'monthly'
                ? $package->price_monthly
                : $package->price_yearly;

            $subscription = Subscription::create([
                'subscribable_id' => $vendor->id,
                'subscribable_type' => Vendor::class,
                'package_id' => $package->id,
                'status' => 'pending',
                'seats_limit' => $package->max_employees,
                'seats_used' => 0,
                'billing_period' => $data['billing_period'],
                'price_per_period' => $price,
                'started_at' => now(),
                'ends_at' => $data['billing_period'] === 'monthly'
                    ? now()->addMonth()
                    : now()->addYear(),
            ]);

            // 3. Create Admin User (inactive)
            $user = User::create([
                'name' => $data['admin']['name'],
                'email' => $data['admin']['email'],
                'phone' => $data['admin']['phone'] ?? null,
                'password' => Hash::make($data['admin']['password']),
                'user_type' => 'vendor_user',
                'employer_id' => null,
                'vendor_id' => $vendor->id,
                'is_active' => false,
                'email_verified_at' => null,
            ]);

            // 4. Assign vendor_admin role
            $user->assignRole('vendor_admin');

            // 5. Create verification token
            $verificationToken = EmailVerificationToken::createForUser($user, $vendor);

            // 6. Send verification email
            $user->notify(new VerifyEmailNotification($verificationToken));

            DB::commit();

            return response()->api(
                'Registration successful. Please check your email to verify your account.',
                [
                    'vendor_id' => $vendor->id,
                    'verification_required' => true,
                    'email' => $user->email,
                ],
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->api(
                'Registration failed: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:64',
        ]);

        $verificationToken = EmailVerificationToken::where('token', $request->token)->first();

        if (!$verificationToken) {
            return response()->api('Invalid verification token', null, 400);
        }

        if ($verificationToken->isUsed()) {
            return response()->api('This token has already been used', null, 400);
        }

        if ($verificationToken->isExpired()) {
            return response()->api(
                'This verification link has expired. Please request a new one.',
                ['can_resend' => true, 'email' => $verificationToken->email],
                400
            );
        }

        try {
            DB::beginTransaction();

            // 1. Get the user
            $user = $verificationToken->user;

            if (!$user) {
                return response()->api('User not found', null, 404);
            }

            // 2. Activate user
            $user->update([
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // 3. Activate subscription
            if ($verificationToken->verifiable) {
                $subscription = $verificationToken->verifiable->activeSubscription
                    ?? $verificationToken->verifiable->subscriptions()->latest()->first();

                if ($subscription) {
                    $subscription->update([
                        'status' => 'active',
                        'seats_used' => 1, // Count admin user
                    ]);
                }

                // Activate employer/vendor
                $verificationToken->verifiable->update(['status' => 'active']);
            }

            // 4. Mark token as used
            $verificationToken->markAsVerified();

            // 5. Generate auth token for auto-login
            $authToken = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->api(
                'Email verified successfully. Your account is now active.',
                [
                    'verified' => true,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $authToken,
                ],
                200
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->api('Verification failed: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->api('User not found', null, 404);
        }

        if ($user->email_verified_at) {
            return response()->api('Email is already verified', null, 400);
        }

        // Determine verifiable (employer or vendor)
        $verifiable = $user->employer ?? $user->vendor;

        // Create new verification token
        $verificationToken = EmailVerificationToken::createForUser($user, $verifiable);

        // Send verification email
        $user->notify(new VerifyEmailNotification($verificationToken));

        return response()->api(
            'Verification email sent successfully. Please check your inbox.',
            ['email' => $user->email],
            200
        );
    }
}

