<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $vendor   = Vendor::first();
        $employer = Employer::first();

        $users = [
            // Employer admin / management
            [
                'name'        => 'Employer Admin',
                'email'       => 'employer@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966501234567',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employer_admin',
                'points'      => 520,
                'locale'      => 'en',
                'is_active'   => true,
            ],
            [
                'name'        => 'HR Manager',
                'email'       => 'hr@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966502345678',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'hr_manager',
                'points'      => 380,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            // Employees (varied XP for leaderboard testing)
            [
                'name'        => 'Ahmed Al-Rashidi',
                'email'       => 'ahmed@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966503456789',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 890,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sara Al-Zahrani',
                'email'       => 'sara@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966504567890',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 750,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Mohammed Al-Ghamdi',
                'email'       => 'mohammed@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966505678901',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 620,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Fatima Al-Otaibi',
                'email'       => 'fatima@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966506789012',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 540,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Khalid Al-Shehri',
                'email'       => 'khalid@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966507890123',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 410,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Nora Al-Qahtani',
                'email'       => 'nora@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966508901234',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 320,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Omar Al-Dossari',
                'email'       => 'omar@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966509012345',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 210,
                'locale'      => 'en',
                'is_active'   => true,
            ],
            [
                'name'        => 'Layla Al-Harbi',
                'email'       => 'layla@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966510123456',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 150,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Yousef Al-Anazi',
                'email'       => 'yousef@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966511234567',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 95,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Reem Al-Mutairi',
                'email'       => 'reem@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966512345678',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 60,
                'locale'      => 'ar',
                'is_active'   => true,
            ],
            [
                'name'        => 'Employee User',
                'email'       => 'employee@example.com',
                'password'    => Hash::make('password'),
                'phone'       => '+966513456789',
                'user_type'   => 'employer_user',
                'employer_id' => $employer?->id,
                'role'        => 'employee',
                'points'      => 25,
                'locale'      => 'en',
                'is_active'   => true,
            ],
            // Vendor users
            [
                'name'      => 'Vendor Admin',
                'email'     => 'vendor_admin@example.com',
                'password'  => Hash::make('password'),
                'phone'     => '+966514567890',
                'user_type' => 'vendor_user',
                'vendor_id' => $vendor?->id,
                'role'      => 'vendor_admin',
                'points'    => 200,
                'locale'    => 'en',
                'is_active' => true,
            ],
            [
                'name'      => 'Vendor Editor',
                'email'     => 'vendor_editor@example.com',
                'password'  => Hash::make('password'),
                'phone'     => '+966515678901',
                'user_type' => 'vendor_user',
                'vendor_id' => $vendor?->id,
                'role'      => 'vendor_editor',
                'points'    => 150,
                'locale'    => 'ar',
                'is_active' => true,
            ],
            // Training Provider
            [
                'name'      => 'Training Provider',
                'email'     => 'trainer@example.com',
                'password'  => Hash::make('password'),
                'phone'     => '+966516789012',
                'user_type' => 'training_provider',
                'role'      => 'training_provider',
                'points'    => 100,
                'locale'    => 'en',
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

        $this->command->info('Seeded ' . count($users) . ' users.');
    }
}
