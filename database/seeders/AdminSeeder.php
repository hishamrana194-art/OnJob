<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@onjob.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Content Manager',
                'email' => 'content@onjob.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Support Admin',
                'email' => 'support@onjob.com',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($admins as $admin) {
            Admin::firstOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }
}

