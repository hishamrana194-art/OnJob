<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => ['en' => 'Tech Academy', 'ar' => 'أكاديمية التقنية'],
                'email' => 'tech@academy.com',
                'phone_number' => '+966501234567',
                'website' => 'https://techacademy.com',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Digital Learning Hub', 'ar' => 'مركز التعلم الرقمي'],
                'email' => 'info@digitallearning.com',
                'phone_number' => '+966502345678',
                'website' => 'https://digitallearning.com',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Professional Training Center', 'ar' => 'مركز التدريب المهني'],
                'email' => 'contact@ptc.com',
                'phone_number' => '+966503456789',
                'website' => 'https://ptc.com',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Business Skills Institute', 'ar' => 'معهد مهارات الأعمال'],
                'email' => 'info@bsi.com',
                'phone_number' => '+966504567890',
                'website' => 'https://bsi.com',
                'status' => false,
            ],
            [
                'name' => ['en' => 'Creative Learning Solutions', 'ar' => 'حلول التعلم الإبداعي'],
                'email' => 'hello@cls.com',
                'phone_number' => '+966505678901',
                'website' => 'https://cls.com',
                'status' => true,
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}

