<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        $moduleTemplates = [
            [
                'title' => ['en' => 'Introduction', 'ar' => 'المقدمة'],
                'description' => ['en' => 'Getting started with the course fundamentals', 'ar' => 'البدء مع أساسيات الدورة'],
            ],
            [
                'title' => ['en' => 'Core Concepts', 'ar' => 'المفاهيم الأساسية'],
                'description' => ['en' => 'Understanding the core principles', 'ar' => 'فهم المبادئ الأساسية'],
            ],
            [
                'title' => ['en' => 'Practical Applications', 'ar' => 'التطبيقات العملية'],
                'description' => ['en' => 'Hands-on exercises and projects', 'ar' => 'تمارين ومشاريع عملية'],
            ],
            [
                'title' => ['en' => 'Advanced Topics', 'ar' => 'المواضيع المتقدمة'],
                'description' => ['en' => 'Deep dive into advanced concepts', 'ar' => 'التعمق في المفاهيم المتقدمة'],
            ],
            [
                'title' => ['en' => 'Final Project', 'ar' => 'المشروع النهائي'],
                'description' => ['en' => 'Apply everything you learned', 'ar' => 'طبق كل ما تعلمته'],
            ],
        ];

        foreach ($courses as $course) {
            $moduleCount = rand(3, 5);
            
            for ($i = 0; $i < $moduleCount; $i++) {
                $template = $moduleTemplates[$i];
                
                Module::create([
                    'course_id' => $course->id,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'order' => $i + 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}

