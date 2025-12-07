<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $employers = Employer::all();
        $vendors = Vendor::all();
        $packages = Package::all();

        // Create subscriptions for employers
        foreach ($employers as $employer) {
            $package = $packages->where('type', 'employer')->random();
            $billingPeriod = collect(['monthly', 'yearly'])->random();

            Subscription::create([
                'subscribable_type' => Employer::class,
                'subscribable_id' => $employer->id,
                'package_id' => $package->id,
                'status' => collect(['active', 'active', 'active', 'expired', 'suspended'])->random(),
                'seats_limit' => $package->max_employees,
                'billing_period' => $billingPeriod,
                'price_per_period' => $billingPeriod === 'monthly' ? $package->price_monthly : $package->price_yearly,
                'started_at' => Carbon::now()->subDays(rand(0, 30)),
                'ends_at' => Carbon::now()->addDays(rand(30, 90)),
            ]);
        }

        // Create subscriptions for vendors
        foreach ($vendors as $vendor) {
            $package = $packages->where('type', 'vendor')->first() ?? $packages->first();
            $billingPeriod = collect(['monthly', 'yearly'])->random();

            Subscription::create([
                'subscribable_type' => Vendor::class,
                'subscribable_id' => $vendor->id,
                'package_id' => $package->id,
                'status' => collect(['active', 'active', 'expired'])->random(),
                'seats_limit' => $package->max_employees,
                'billing_period' => $billingPeriod,
                'price_per_period' => $billingPeriod === 'monthly' ? $package->price_monthly : $package->price_yearly,
                'started_at' => Carbon::now()->subDays(rand(0, 15)),
                'ends_at' => Carbon::now()->addDays(rand(30, 60)),
            ]);
        }
    }
}

