<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateEmployerWithAdminRequest;
use App\Http\Requests\Api\CreateVendorWithAdminRequest;
use App\Http\Resources\EmployerResource;
use App\Http\Resources\VendorResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Employer;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    /**
     * Create employer with first admin user and subscription
     */
    public function createEmployerWithAdmin(CreateEmployerWithAdminRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Create Employer
            $employer = Employer::create([
                'name' => $data['employer_name'],
                'email' => $data['employer_email'],
                'phone_number' => $data['employer_phone'] ?? null,
                'country_id' => $data['employer_country_id'],
                'company' => $data['employer_company'] ?? null,
                'address' => $data['employer_address'] ?? null,
                'status' => 'active',
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
                'status' => 'active',
                'seats_limit' => $package->max_employees,
                'seats_used' => 1, // Admin user counts as 1
                'billing_period' => $data['billing_period'],
                'price_per_period' => $price,
                'started_at' => $data['subscription_starts_at'],
                'ends_at' => $data['subscription_ends_at'],
            ]);

            // 3. Create Admin User
            $user = User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'phone' => $data['admin_phone'] ?? null,
                'password' => Hash::make($data['admin_password']),
                'user_type' => 'employer_user',
                'employer_id' => $employer->id,
                'vendor_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // 4. Assign employer_admin role
            $user->assignRole('employer_admin');

            DB::commit();

            return response()->api(
                'Employer created successfully with admin user',
                [
                    'employer' => new EmployerResource($employer),
                    'admin' => new UserResource($user->load('roles')),
                    'subscription' => new SubscriptionResource($subscription->load('package')),
                    'credentials' => [
                        'email' => $data['admin_email'],
                        'password' => $data['admin_password'],
                    ],
                ],
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->api(
                'Failed to create employer: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Create vendor with first admin user and subscription
     */
    public function createVendorWithAdmin(CreateVendorWithAdminRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Create Vendor
            $vendor = Vendor::create([
                'name' => $data['vendor_name'],
                'email' => $data['vendor_email'],
                'phone_number' => $data['vendor_phone'] ?? null,
                'country_id' => $data['vendor_country_id'],
                'description' => $data['vendor_description'] ?? null,
                'website' => $data['vendor_website'] ?? null,
                'status' => 'active',
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
                'status' => 'active',
                'seats_limit' => $package->max_employees,
                'seats_used' => 1,
                'billing_period' => $data['billing_period'],
                'price_per_period' => $price,
                'started_at' => $data['subscription_starts_at'],
                'ends_at' => $data['subscription_ends_at'],
            ]);

            // 3. Create Admin User
            $user = User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'phone' => $data['admin_phone'] ?? null,
                'password' => Hash::make($data['admin_password']),
                'user_type' => 'vendor_user',
                'employer_id' => null,
                'vendor_id' => $vendor->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // 4. Assign vendor_admin role
            $user->assignRole('vendor_admin');

            DB::commit();

            return response()->api(
                'Vendor created successfully with admin user',
                [
                    'vendor' => new VendorResource($vendor),
                    'admin' => new UserResource($user->load('roles')),
                    'subscription' => new SubscriptionResource($subscription->load('package')),
                    'credentials' => [
                        'email' => $data['admin_email'],
                        'password' => $data['admin_password'],
                    ],
                ],
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->api(
                'Failed to create vendor: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
}

