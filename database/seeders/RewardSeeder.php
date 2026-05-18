<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reward;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            // ── Vouchers (low cost) ───────────────────────────────────
            [
                'title'       => ['ar' => 'قسيمة قهوة', 'en' => 'Coffee Voucher'],
                'description' => ['ar' => 'استبدل كوينز بقسيمة قهوة مجانية', 'en' => 'Redeem coins for a free coffee voucher'],
                'cost_points' => 20,
                'type'        => 'voucher',
                'stock'       => null,
                'is_active'   => true,
            ],
            [
                'title'       => ['ar' => 'قسيمة غداء', 'en' => 'Lunch Voucher'],
                'description' => ['ar' => 'وجبة غداء مجانية في كافيتريا الشركة', 'en' => 'Free lunch at the company cafeteria'],
                'cost_points' => 50,
                'type'        => 'voucher',
                'stock'       => null,
                'is_active'   => true,
            ],
            [
                'title'       => ['ar' => 'قسيمة تسوق', 'en' => 'Shopping Voucher'],
                'description' => ['ar' => 'قسيمة تسوق بقيمة 50 ريال', 'en' => 'Shopping voucher worth 50 SAR'],
                'cost_points' => 150,
                'type'        => 'voucher',
                'stock'       => 30,
                'is_active'   => true,
            ],
            [
                'title'       => ['ar' => 'بطاقة هدية أمازون', 'en' => 'Amazon Gift Card'],
                'description' => ['ar' => 'بطاقة هدية أمازون بقيمة 100 ريال', 'en' => 'Amazon gift card worth 100 SAR'],
                'cost_points' => 300,
                'type'        => 'voucher',
                'stock'       => 20,
                'is_active'   => true,
            ],

            // ── Physical rewards ──────────────────────────────────────
            [
                'title'       => ['ar' => 'كتاب مهني', 'en' => 'Professional Book'],
                'description' => ['ar' => 'اختر كتابًا مهنيًا من مكتبة الشركة', 'en' => 'Choose a professional book from the company library'],
                'cost_points' => 80,
                'type'        => 'physical',
                'stock'       => 15,
                'is_active'   => true,
            ],
            [
                'title'       => ['ar' => 'هدية ترحيبية', 'en' => 'Welcome Gift Box'],
                'description' => ['ar' => 'صندوق هدايا يحتوي على مستلزمات مكتبية مميزة', 'en' => 'Gift box with premium office supplies'],
                'cost_points' => 200,
                'type'        => 'physical',
                'stock'       => 10,
                'is_active'   => true,
            ],

            // ── Benefits / Days off ───────────────────────────────────
            [
                'title'       => ['ar' => 'ساعة مرونة', 'en' => 'Flex Hour'],
                'description' => ['ar' => 'ابدأ يوم عملك بساعة متأخرة أو انهه مبكرًا', 'en' => 'Start your workday an hour late or leave an hour early'],
                'cost_points' => 100,
                'type'        => 'benefit',
                'stock'       => null,
                'is_active'   => true,
            ],
            [
                'title'       => ['ar' => 'يوم إجازة', 'en' => 'Day Off'],
                'description' => ['ar' => 'استبدلها بيوم إجازة مدفوعة إضافية', 'en' => 'Redeem for an extra paid day off'],
                'cost_points' => 200,
                'type'        => 'benefit',
                'stock'       => 10,
                'is_active'   => true,
            ],
            [
                'title'       => ['ar' => 'عمل من المنزل', 'en' => 'Work From Home Day'],
                'description' => ['ar' => 'يوم عمل عن بُعد إضافي من المنزل', 'en' => 'An additional remote working day from home'],
                'cost_points' => 120,
                'type'        => 'benefit',
                'stock'       => null,
                'is_active'   => true,
            ],
            [
                'title'       => ['ar' => 'وصول لدورة مميزة', 'en' => 'Premium Course Access'],
                'description' => ['ar' => 'وصول لمدة شهر لدورة تدريبية مميزة', 'en' => 'One month access to a premium training course'],
                'cost_points' => 500,
                'type'        => 'benefit',
                'stock'       => 5,
                'is_active'   => true,
            ],
        ];

        foreach ($rewards as $r) {
            Reward::updateOrCreate(
                ['type' => $r['type'], 'cost_points' => $r['cost_points']],
                $r
            );
        }

        $this->command->info('Seeded ' . count($rewards) . ' rewards.');
    }
}
