<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendor = Vendor::first();
        $employer = Employer::first();

        $users = [
            // Employer users
            [
                'name' => 'Employer Admin',
                'email' => 'employer@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966501234567',
                'user_type' => 'employer_user',
                'employer_id' => $employer?->id,
                'role' => 'employer_admin',
                'points' => 100,
                'locale' => 'en',
                'is_active' => true,
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966502345678',
                'user_type' => 'employer_user',
                'employer_id' => $employer?->id,
                'role' => 'hr_manager',
                'points' => 50,
                'locale' => 'en',
                'is_active' => true,
            ],
            [
                'name' => 'Employee User',
                'email' => 'employee@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966503456789',
                'user_type' => 'employer_user',
                'employer_id' => $employer?->id,
                'role' => 'employee',
                'points' => 25,
                'locale' => 'en',
                'is_active' => true,
            ],
            // Vendor users
            [
                'name' => 'Vendor Admin',
                'email' => 'vendor_admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966504567890',
                'user_type' => 'vendor_user',
                'vendor_id' => $vendor?->id,
                'role' => 'vendor_admin',
                'points' => 200,
                'locale' => 'en',
                'is_active' => true,
            ],
            [
                'name' => 'Vendor Editor',
                'email' => 'vendor_editor@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966505678901',
                'user_type' => 'vendor_user',
                'vendor_id' => $vendor?->id,
                'role' => 'vendor_editor',
                'points' => 150,
                'locale' => 'ar',
                'is_active' => true,
            ],
            // Training Provider
            [
                'name' => 'Training Provider',
                'email' => 'trainer@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966506789012',
                'user_type' => 'training_provider',
                'role' => 'training_provider',
                'points' => 100,
                'locale' => 'en',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}

