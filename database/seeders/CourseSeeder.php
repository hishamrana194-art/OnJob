<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = Vendor::first();
        $categories = Category::whereNull('parent_id')->get();

        $courses = [
            [
                'title' => ['en' => 'Complete Web Development Bootcamp', 'ar' => 'معسكر تطوير الويب الشامل'],
                'description' => ['en' => 'Learn HTML, CSS, JavaScript, React, Node.js and more. Build real projects.', 'ar' => 'تعلم HTML, CSS, JavaScript, React, Node.js والمزيد. ابني مشاريع حقيقية.'],
                'level' => 'beginner',
                'duration' => 120,
                'status' => 'published',
                'is_mandatory' => true,
            ],
            [
                'title' => ['en' => 'Advanced Python Programming', 'ar' => 'برمجة بايثون المتقدمة'],
                'description' => ['en' => 'Master Python with advanced concepts, data structures, and algorithms.', 'ar' => 'أتقن بايثون مع المفاهيم المتقدمة وهياكل البيانات والخوارزميات.'],
                'level' => 'advanced',
                'duration' => 80,
                'status' => 'published',
                'is_mandatory' => false,
            ],
            [
                'title' => ['en' => 'Digital Marketing Masterclass', 'ar' => 'ماستركلاس التسويق الرقمي'],
                'description' => ['en' => 'Learn SEO, Social Media Marketing, Google Ads, and content strategy.', 'ar' => 'تعلم تحسين محركات البحث، التسويق عبر وسائل التواصل، إعلانات جوجل، واستراتيجية المحتوى.'],
                'level' => 'intermediate',
                'duration' => 60,
                'status' => 'published',
                'is_mandatory' => true,
            ],
            [
                'title' => ['en' => 'UI/UX Design Fundamentals', 'ar' => 'أساسيات تصميم واجهات المستخدم'],
                'description' => ['en' => 'Create beautiful and user-friendly interfaces using Figma and Adobe XD.', 'ar' => 'أنشئ واجهات جميلة وسهلة الاستخدام باستخدام فيجما وأدوبي إكس دي.'],
                'level' => 'beginner',
                'duration' => 45,
                'status' => 'published',
                'is_mandatory' => false,
            ],
            [
                'title' => ['en' => 'Leadership and Management Skills', 'ar' => 'مهارات القيادة والإدارة'],
                'description' => ['en' => 'Develop essential leadership skills for the modern workplace.', 'ar' => 'طور مهارات القيادة الأساسية لبيئة العمل الحديثة.'],
                'level' => 'intermediate',
                'duration' => 30,
                'status' => 'published',
                'is_mandatory' => true,
            ],
            [
                'title' => ['en' => 'Mobile App Development with Flutter', 'ar' => 'تطوير تطبيقات الموبايل بفلتر'],
                'description' => ['en' => 'Build cross-platform mobile apps with Flutter and Dart.', 'ar' => 'ابني تطبيقات موبايل متعددة المنصات بفلتر ودارت.'],
                'level' => 'intermediate',
                'duration' => 90,
                'status' => 'draft',
                'is_mandatory' => false,
            ],
            [
                'title' => ['en' => 'Data Science with Python', 'ar' => 'علم البيانات مع بايثون'],
                'description' => ['en' => 'Learn data analysis, visualization, and machine learning.', 'ar' => 'تعلم تحليل البيانات والتصور البياني وتعلم الآلة.'],
                'level' => 'advanced',
                'duration' => 100,
                'status' => 'published',
                'is_mandatory' => false,
            ],
            [
                'title' => ['en' => 'English for Business', 'ar' => 'الإنجليزية للأعمال'],
                'description' => ['en' => 'Improve your business English communication skills.', 'ar' => 'حسن مهاراتك في التواصل بالإنجليزية للأعمال.'],
                'level' => 'beginner',
                'duration' => 40,
                'status' => 'published',
                'is_mandatory' => false,
            ],
        ];

        foreach ($courses as $index => $courseData) {
            $category = $categories[$index % $categories->count()] ?? $categories->first();
            
            Course::create([
                ...$courseData,
                'vendor_id' => $vendor?->id ?? 1,
                'category_id' => $category?->id ?? 1,
            ]);
        }
    }
}

