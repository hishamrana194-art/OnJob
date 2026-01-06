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
        // Skip if already in JSON format

        // Countries - name
        $countries = DB::table('countries')->get();
        foreach ($countries as $country) {
            // Skip if already JSON
            if ($this->isJson($country->name)) {
                continue;
            }
            $jsonName = json_encode(['en' => $country->name]);
            DB::table('countries')->where('id', $country->id)->update(['name' => $jsonName]);
        }

        // Packages - name, description, features
        $packages = DB::table('packages')->get();
        foreach ($packages as $package) {
            $updates = [];
            if (!$this->isJson($package->name)) {
                $updates['name'] = json_encode(['en' => $package->name]);
            }
            if (!$this->isJson($package->description)) {
                $updates['description'] = json_encode(['en' => $package->description]);
            }
            if (!$this->isJson($package->features)) {
                $updates['features'] = json_encode(['en' => $package->features]);
            }
            if (!empty($updates)) {
                DB::table('packages')->where('id', $package->id)->update($updates);
            }
        }

        // Vendors - name
        $vendors = DB::table('vendors')->get();
        foreach ($vendors as $vendor) {
            if ($this->isJson($vendor->name)) {
                continue;
            }
            DB::table('vendors')->where('id', $vendor->id)->update([
                'name' => json_encode(['en' => $vendor->name]),
            ]);
        }

        // Employers - name, company, address
        $employers = DB::table('employers')->get();
        foreach ($employers as $employer) {
            $updates = [];
            if (!$this->isJson($employer->name)) {
                $updates['name'] = json_encode(['en' => $employer->name]);
            }
            if (!$this->isJson($employer->company)) {
                $updates['company'] = json_encode(['en' => $employer->company]);
            }
            if (!$this->isJson($employer->address)) {
                $updates['address'] = json_encode(['en' => $employer->address]);
            }
            if (!empty($updates)) {
                DB::table('employers')->where('id', $employer->id)->update($updates);
            }
        }
    }

    /**
     * Check if a string is valid JSON
     */
    private function isJson($string): bool
    {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Reverse the migrations.
     *
     * Note: Since columns have JSON constraints, we cannot convert back to plain strings.
     * This migration is essentially irreversible for data, but we keep JSON format.
     */
    public function down(): void
    {
        // Cannot convert back to plain strings because columns have JSON constraints.
        // Data will remain in JSON format which is still valid.
        // If you need to truly reverse this, you must first alter the column constraints.
    }
};
