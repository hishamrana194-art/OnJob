<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Employer;
use Illuminate\Database\Seeder;

class EmployerSeeder extends Seeder
{
    public function run(): void
    {
        $countryId = Country::first()?->id ?? 1;

        $employers = [
            [
                'name' => ['en' => 'Ahmed Al-Rashid', 'ar' => 'أحمد الراشد'],
                'email' => 'ahmed@company.com',
                'phone_number' => '+966511234567',
                'company' => ['en' => 'Global Tech Solutions', 'ar' => 'حلول التقنية العالمية'],
                'address' => ['en' => 'Riyadh, King Fahd Road', 'ar' => 'الرياض، طريق الملك فهد'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Mohammed Al-Salem', 'ar' => 'محمد السالم'],
                'email' => 'mohammed@enterprise.com',
                'phone_number' => '+966512345678',
                'company' => ['en' => 'Enterprise Holdings', 'ar' => 'مجموعة إنتربرايز'],
                'address' => ['en' => 'Jeddah, Corniche', 'ar' => 'جدة، الكورنيش'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Khalid Al-Otaibi', 'ar' => 'خالد العتيبي'],
                'email' => 'khalid@startup.com',
                'phone_number' => '+966513456789',
                'company' => ['en' => 'Innovative Startup', 'ar' => 'الشركة الناشئة المبتكرة'],
                'address' => ['en' => 'Dammam, Business District', 'ar' => 'الدمام، حي الأعمال'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Fahad Al-Qahtani', 'ar' => 'فهد القحطاني'],
                'email' => 'fahad@construction.com',
                'phone_number' => '+966514567890',
                'company' => ['en' => 'Al-Qahtani Construction', 'ar' => 'مؤسسة القحطاني للمقاولات'],
                'address' => ['en' => 'Riyadh, Industrial Area', 'ar' => 'الرياض، المنطقة الصناعية'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => false,
            ],
            [
                'name' => ['en' => 'Saud Al-Malki', 'ar' => 'سعود المالكي'],
                'email' => 'saud@retail.com',
                'phone_number' => '+966515678901',
                'company' => ['en' => 'Retail Masters', 'ar' => 'أساتذة التجزئة'],
                'address' => ['en' => 'Mecca, Central District', 'ar' => 'مكة، الحي المركزي'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Omar Al-Harbi', 'ar' => 'عمر الحربي'],
                'email' => 'omar@logistics.com',
                'phone_number' => '+966516789012',
                'company' => ['en' => 'Fast Logistics', 'ar' => 'اللوجستيات السريعة'],
                'address' => ['en' => 'Riyadh, Logistics Zone', 'ar' => 'الرياض، منطقة اللوجستيات'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Turki Al-Dosari', 'ar' => 'تركي الدوسري'],
                'email' => 'turki@healthcare.com',
                'phone_number' => '+966517890123',
                'company' => ['en' => 'Healthcare Plus', 'ar' => 'الرعاية الصحية بلس'],
                'address' => ['en' => 'Jeddah, Medical District', 'ar' => 'جدة، الحي الطبي'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Nasser Al-Ghamdi', 'ar' => 'ناصر الغامدي'],
                'email' => 'nasser@education.com',
                'phone_number' => '+966518901234',
                'company' => ['en' => 'Education First', 'ar' => 'التعليم أولاً'],
                'address' => ['en' => 'Medina, University Area', 'ar' => 'المدينة، منطقة الجامعات'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Sultan Al-Shehri', 'ar' => 'سلطان الشهري'],
                'email' => 'sultan@finance.com',
                'phone_number' => '+966519012345',
                'company' => ['en' => 'Finance Pro', 'ar' => 'المالية المحترفة'],
                'address' => ['en' => 'Riyadh, Financial District', 'ar' => 'الرياض، الحي المالي'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Faisal Al-Mutairi', 'ar' => 'فيصل المطيري'],
                'email' => 'faisal@tech.com',
                'phone_number' => '+966520123456',
                'company' => ['en' => 'Tech Innovations', 'ar' => 'الابتكارات التقنية'],
                'address' => ['en' => 'Khobar, Tech Park', 'ar' => 'الخبر، حديقة التقنية'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => false,
            ],
            [
                'name' => ['en' => 'Abdulaziz Al-Zahrani', 'ar' => 'عبدالعزيز الزهراني'],
                'email' => 'abdulaziz@media.com',
                'phone_number' => '+966521234567',
                'company' => ['en' => 'Media House', 'ar' => 'بيت الإعلام'],
                'address' => ['en' => 'Riyadh, Media City', 'ar' => 'الرياض، المدينة الإعلامية'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Waleed Al-Shamrani', 'ar' => 'وليد الشمراني'],
                'email' => 'waleed@hospitality.com',
                'phone_number' => '+966522345678',
                'company' => ['en' => 'Hospitality Group', 'ar' => 'مجموعة الضيافة'],
                'address' => ['en' => 'Taif, Tourism Zone', 'ar' => 'الطائف، منطقة السياحة'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Salman Al-Anazi', 'ar' => 'سلمان العنزي'],
                'email' => 'salman@energy.com',
                'phone_number' => '+966523456789',
                'company' => ['en' => 'Energy Solutions', 'ar' => 'حلول الطاقة'],
                'address' => ['en' => 'Dhahran, Energy District', 'ar' => 'الظهران، حي الطاقة'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Rayan Al-Subaie', 'ar' => 'ريان السبيعي'],
                'email' => 'rayan@consulting.com',
                'phone_number' => '+966524567890',
                'company' => ['en' => 'Business Consulting', 'ar' => 'استشارات الأعمال'],
                'address' => ['en' => 'Riyadh, Business Bay', 'ar' => 'الرياض، خليج الأعمال'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
            [
                'name' => ['en' => 'Majed Al-Qahtani', 'ar' => 'ماجد القحطاني'],
                'email' => 'majed@manufacturing.com',
                'phone_number' => '+966525678901',
                'company' => ['en' => 'Manufacturing Co', 'ar' => 'شركة التصنيع'],
                'address' => ['en' => 'Jubail, Industrial City', 'ar' => 'الجبيل، المدينة الصناعية'],
                'country_id' => $countryId,
                'gender' => 'male',
                'status' => true,
            ],
        ];

        foreach ($employers as $employer) {
            Employer::create($employer);
        }
    }
}

