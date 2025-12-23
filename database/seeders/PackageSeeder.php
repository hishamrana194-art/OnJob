<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            // Employer Packages
            [
                'name' => ['en' => 'Basic Employer', 'ar' => 'الباقة الأساسية لصاحب العمل'],
                'description' => ['en' => 'Perfect for small businesses starting their journey', 'ar' => 'مثالية للشركات الصغيرة في بداية رحلتها'],
                'type' => 'employer',
                'price_monthly' => 99.00,
                'price_yearly' => 999.00,
                'max_employees' => 10,
                'max_content' => 5,
                'features' => ['en' => 'Post up to 5 jobs, Basic support, Email notifications', 'ar' => 'نشر حتى 5 وظائف، دعم أساسي، إشعارات بريدية'],
                'is_active' => true,
            ],
            [
                'name' => ['en' => 'Professional Employer', 'ar' => 'الباقة الاحترافية لصاحب العمل'],
                'description' => ['en' => 'Best for growing companies', 'ar' => 'الأفضل للشركات النامية'],
                'type' => 'employer',
                'price_monthly' => 249.00,
                'price_yearly' => 2499.00,
                'max_employees' => 50,
                'max_content' => 20,
                'features' => ['en' => 'Unlimited jobs, Priority support, Featured listings, Analytics', 'ar' => 'وظائف غير محدودة، دعم أولوي، قوائم مميزة، تحليلات'],
                'is_active' => true,
            ],
            [
                'name' => ['en' => 'Enterprise Employer', 'ar' => 'باقة المؤسسات لصاحب العمل'],
                'description' => ['en' => 'Complete solution for large enterprises', 'ar' => 'حل متكامل للمؤسسات الكبيرة'],
                'type' => 'employer',
                'price_monthly' => 499.00,
                'price_yearly' => 4999.00,
                'max_employees' => 200,
                'max_content' => 100,
                'features' => ['en' => 'Everything in Pro, Dedicated account manager, API access, Custom branding', 'ar' => 'كل مميزات الاحترافية، مدير حساب مخصص، وصول API، علامة تجارية مخصصة'],
                'is_active' => true,
            ],
            // Vendor Packages
            [
                'name' => ['en' => 'Starter Vendor', 'ar' => 'الباقة المبتدئة للمورد'],
                'description' => ['en' => 'Start selling your courses', 'ar' => 'ابدأ ببيع دوراتك'],
                'type' => 'vendor',
                'price_monthly' => 49.00,
                'price_yearly' => 499.00,
                'max_employees' => 5,
                'max_content' => 3,
                'features' => ['en' => 'Up to 3 courses, Basic analytics, Standard support', 'ar' => 'حتى 3 دورات، تحليلات أساسية، دعم قياسي'],
                'is_active' => true,
            ],
            [
                'name' => ['en' => 'Pro Vendor', 'ar' => 'الباقة الاحترافية للمورد'],
                'description' => ['en' => 'For serious course creators', 'ar' => 'لمنشئي الدورات الجادين'],
                'type' => 'vendor',
                'price_monthly' => 149.00,
                'price_yearly' => 1499.00,
                'max_employees' => 20,
                'max_content' => 50,
                'features' => ['en' => 'Unlimited courses, Advanced analytics, Priority support, Custom certificates', 'ar' => 'دورات غير محدودة، تحليلات متقدمة، دعم أولوي، شهادات مخصصة'],
                'is_active' => true,
            ],
            // Provider Packages
            [
                'name' => ['en' => 'Basic Provider', 'ar' => 'الباقة الأساسية للمزود'],
                'description' => ['en' => 'Entry level for service providers', 'ar' => 'مستوى الدخول لمزودي الخدمات'],
                'type' => 'provider',
                'price_monthly' => 79.00,
                'price_yearly' => 799.00,
                'max_employees' => 10,
                'max_content' => 10,
                'features' => ['en' => 'List services, Basic profile, Standard visibility', 'ar' => 'قائمة الخدمات، ملف تعريف أساسي، رؤية قياسية'],
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}

