<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['en' => 'Afghanistan', 'ar' => 'أفغانستان', 'code' => 'AF'],
            ['en' => 'Albania', 'ar' => 'ألبانيا', 'code' => 'AL'],
            ['en' => 'Algeria', 'ar' => 'الجزائر', 'code' => 'DZ'],
            ['en' => 'Andorra', 'ar' => 'أندورا', 'code' => 'AD'],
            ['en' => 'Angola', 'ar' => 'أنغولا', 'code' => 'AO'],
            ['en' => 'Argentina', 'ar' => 'الأرجنتين', 'code' => 'AR'],
            ['en' => 'Armenia', 'ar' => 'أرمينيا', 'code' => 'AM'],
            ['en' => 'Australia', 'ar' => 'أستراليا', 'code' => 'AU'],
            ['en' => 'Austria', 'ar' => 'النمسا', 'code' => 'AT'],
            ['en' => 'Azerbaijan', 'ar' => 'أذربيجان', 'code' => 'AZ'],
            ['en' => 'Bahamas', 'ar' => 'جزر البهاما', 'code' => 'BS'],
            ['en' => 'Bahrain', 'ar' => 'البحرين', 'code' => 'BH'],
            ['en' => 'Bangladesh', 'ar' => 'بنغلاديش', 'code' => 'BD'],
            ['en' => 'Belgium', 'ar' => 'بلجيكا', 'code' => 'BE'],
            ['en' => 'Brazil', 'ar' => 'البرازيل', 'code' => 'BR'],
            ['en' => 'Canada', 'ar' => 'كندا', 'code' => 'CA'],
            ['en' => 'Chile', 'ar' => 'تشيلي', 'code' => 'CL'],
            ['en' => 'China', 'ar' => 'الصين', 'code' => 'CN'],
            ['en' => 'Colombia', 'ar' => 'كولومبيا', 'code' => 'CO'],
            ['en' => 'Cuba', 'ar' => 'كوبا', 'code' => 'CU'],
            ['en' => 'Denmark', 'ar' => 'الدنمارك', 'code' => 'DK'],
            ['en' => 'Egypt', 'ar' => 'مصر', 'code' => 'EG'],
            ['en' => 'Finland', 'ar' => 'فنلندا', 'code' => 'FI'],
            ['en' => 'France', 'ar' => 'فرنسا', 'code' => 'FR'],
            ['en' => 'Germany', 'ar' => 'ألمانيا', 'code' => 'DE'],
            ['en' => 'Greece', 'ar' => 'اليونان', 'code' => 'GR'],
            ['en' => 'India', 'ar' => 'الهند', 'code' => 'IN'],
            ['en' => 'Indonesia', 'ar' => 'إندونيسيا', 'code' => 'ID'],
            ['en' => 'Iran', 'ar' => 'إيران', 'code' => 'IR'],
            ['en' => 'Iraq', 'ar' => 'العراق', 'code' => 'IQ'],
            ['en' => 'Ireland', 'ar' => 'أيرلندا', 'code' => 'IE'],
            ['en' => 'Israel', 'ar' => 'إسرائيل', 'code' => 'IL'],
            ['en' => 'Italy', 'ar' => 'إيطاليا', 'code' => 'IT'],
            ['en' => 'Japan', 'ar' => 'اليابان', 'code' => 'JP'],
            ['en' => 'Jordan', 'ar' => 'الأردن', 'code' => 'JO'],
            ['en' => 'Kuwait', 'ar' => 'الكويت', 'code' => 'KW'],
            ['en' => 'Lebanon', 'ar' => 'لبنان', 'code' => 'LB'],
            ['en' => 'Libya', 'ar' => 'ليبيا', 'code' => 'LY'],
            ['en' => 'Malaysia', 'ar' => 'ماليزيا', 'code' => 'MY'],
            ['en' => 'Mexico', 'ar' => 'المكسيك', 'code' => 'MX'],
            ['en' => 'Morocco', 'ar' => 'المغرب', 'code' => 'MA'],
            ['en' => 'Netherlands', 'ar' => 'هولندا', 'code' => 'NL'],
            ['en' => 'New Zealand', 'ar' => 'نيوزيلندا', 'code' => 'NZ'],
            ['en' => 'Nigeria', 'ar' => 'نيجيريا', 'code' => 'NG'],
            ['en' => 'Norway', 'ar' => 'النرويج', 'code' => 'NO'],
            ['en' => 'Oman', 'ar' => 'عُمان', 'code' => 'OM'],
            ['en' => 'Pakistan', 'ar' => 'باكستان', 'code' => 'PK'],
            ['en' => 'Palestine', 'ar' => 'فلسطين', 'code' => 'PS'],
            ['en' => 'Philippines', 'ar' => 'الفلبين', 'code' => 'PH'],
            ['en' => 'Poland', 'ar' => 'بولندا', 'code' => 'PL'],
            ['en' => 'Portugal', 'ar' => 'البرتغال', 'code' => 'PT'],
            ['en' => 'Qatar', 'ar' => 'قطر', 'code' => 'QA'],
            ['en' => 'Russia', 'ar' => 'روسيا', 'code' => 'RU'],
            ['en' => 'Saudi Arabia', 'ar' => 'المملكة العربية السعودية', 'code' => 'SA'],
            ['en' => 'Singapore', 'ar' => 'سنغافورة', 'code' => 'SG'],
            ['en' => 'South Africa', 'ar' => 'جنوب أفريقيا', 'code' => 'ZA'],
            ['en' => 'South Korea', 'ar' => 'كوريا الجنوبية', 'code' => 'KR'],
            ['en' => 'Spain', 'ar' => 'إسبانيا', 'code' => 'ES'],
            ['en' => 'Sudan', 'ar' => 'السودان', 'code' => 'SD'],
            ['en' => 'Sweden', 'ar' => 'السويد', 'code' => 'SE'],
            ['en' => 'Switzerland', 'ar' => 'سويسرا', 'code' => 'CH'],
            ['en' => 'Syria', 'ar' => 'سوريا', 'code' => 'SY'],
            ['en' => 'Taiwan', 'ar' => 'تايوان', 'code' => 'TW'],
            ['en' => 'Thailand', 'ar' => 'تايلاند', 'code' => 'TH'],
            ['en' => 'Tunisia', 'ar' => 'تونس', 'code' => 'TN'],
            ['en' => 'Turkey', 'ar' => 'تركيا', 'code' => 'TR'],
            ['en' => 'Ukraine', 'ar' => 'أوكرانيا', 'code' => 'UA'],
            ['en' => 'United Arab Emirates', 'ar' => 'الإمارات العربية المتحدة', 'code' => 'AE'],
            ['en' => 'United Kingdom', 'ar' => 'المملكة المتحدة', 'code' => 'GB'],
            ['en' => 'United States', 'ar' => 'الولايات المتحدة', 'code' => 'US'],
            ['en' => 'Yemen', 'ar' => 'اليمن', 'code' => 'YE'],
        ];

        foreach ($countries as $country) {
            Country::create([
                'name' => ['en' => $country['en'], 'ar' => $country['ar']],
                'code' => $country['code'],
            ]);
        }
    }
}
