<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert existing data to JSON format for translatable columns

        // Countries - name
        $countries = DB::table('countries')->get();
        foreach ($countries as $country) {
            $jsonName = json_encode(['en' => $country->name]);
            DB::table('countries')->where('id', $country->id)->update(['name' => $jsonName]);
        }

        // Packages - name, description, features
        $packages = DB::table('packages')->get();
        foreach ($packages as $package) {
            DB::table('packages')->where('id', $package->id)->update([
                'name' => json_encode(['en' => $package->name]),
                'description' => json_encode(['en' => $package->description]),
                'features' => json_encode(['en' => $package->features]),
            ]);
        }

        // Vendors - name
        $vendors = DB::table('vendors')->get();
        foreach ($vendors as $vendor) {
            DB::table('vendors')->where('id', $vendor->id)->update([
                'name' => json_encode(['en' => $vendor->name]),
            ]);
        }

        // Employers - name, company, address
        $employers = DB::table('employers')->get();
        foreach ($employers as $employer) {
            DB::table('employers')->where('id', $employer->id)->update([
                'name' => json_encode(['en' => $employer->name]),
                'company' => json_encode(['en' => $employer->company]),
                'address' => json_encode(['en' => $employer->address]),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert JSON back to plain strings (using 'en' locale)

        // Countries
        $countries = DB::table('countries')->get();
        foreach ($countries as $country) {
            $decoded = json_decode($country->name, true);
            $name = is_array($decoded) ? ($decoded['en'] ?? $country->name) : $country->name;
            DB::table('countries')->where('id', $country->id)->update(['name' => $name]);
        }

        // Packages
        $packages = DB::table('packages')->get();
        foreach ($packages as $package) {
            $decodedName = json_decode($package->name, true);
            $decodedDesc = json_decode($package->description, true);
            $decodedFeatures = json_decode($package->features, true);

            DB::table('packages')->where('id', $package->id)->update([
                'name' => is_array($decodedName) ? ($decodedName['en'] ?? $package->name) : $package->name,
                'description' => is_array($decodedDesc) ? ($decodedDesc['en'] ?? $package->description) : $package->description,
                'features' => is_array($decodedFeatures) ? ($decodedFeatures['en'] ?? $package->features) : $package->features,
            ]);
        }

        // Vendors
        $vendors = DB::table('vendors')->get();
        foreach ($vendors as $vendor) {
            $decoded = json_decode($vendor->name, true);
            $name = is_array($decoded) ? ($decoded['en'] ?? $vendor->name) : $vendor->name;
            DB::table('vendors')->where('id', $vendor->id)->update(['name' => $name]);
        }

        // Employers
        $employers = DB::table('employers')->get();
        foreach ($employers as $employer) {
            $decodedName = json_decode($employer->name, true);
            $decodedCompany = json_decode($employer->company, true);
            $decodedAddress = json_decode($employer->address, true);

            DB::table('employers')->where('id', $employer->id)->update([
                'name' => is_array($decodedName) ? ($decodedName['en'] ?? $employer->name) : $employer->name,
                'company' => is_array($decodedCompany) ? ($decodedCompany['en'] ?? $employer->company) : $employer->company,
                'address' => is_array($decodedAddress) ? ($decodedAddress['en'] ?? $employer->address) : $employer->address,
            ]);
        }
    }
};
