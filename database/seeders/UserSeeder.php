<?php

namespace Database\Seeders;

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

        $users = [
            [
                'name' => 'Employer Admin',
                'email' => 'employer@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966501234567',
                'organization_id' => $vendor?->id,
                'role' => 'employer_t1',
                'points' => 100,
                'locale' => 'en',
                'is_active' => true,
            ],
            [
                'name' => 'Employer Manager',
                'email' => 'employer2@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966502345678',
                'organization_id' => $vendor?->id,
                'role' => 'employer_t2',
                'points' => 50,
                'locale' => 'en',
                'is_active' => true,
            ],
            [
                'name' => 'Employee User',
                'email' => 'employee@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966503456789',
                'organization_id' => $vendor?->id,
                'role' => 'employee',
                'points' => 25,
                'locale' => 'en',
                'is_active' => true,
            ],
            [
                'name' => 'Vendor Admin',
                'email' => 'vendor_admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966504567890',
                'organization_id' => $vendor?->id,
                'role' => 'vendor_admin',
                'points' => 200,
                'locale' => 'en',
                'is_active' => true,
            ],
            [
                'name' => 'Training Provider',
                'email' => 'trainer@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966505678901',
                'organization_id' => $vendor?->id,
                'role' => 'training_provider',
                'points' => 150,
                'locale' => 'ar',
                'is_active' => true,
            ],
            [
                'name' => 'Inactive User',
                'email' => 'inactive@example.com',
                'password' => Hash::make('password'),
                'phone' => '+966506789012',
                'organization_id' => $vendor?->id,
                'role' => 'employee',
                'points' => 0,
                'locale' => 'en',
                'is_active' => false,
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::create($userData);
            $user->assignRole($role);
        }
    }
}

