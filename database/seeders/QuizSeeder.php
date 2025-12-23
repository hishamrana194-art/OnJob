<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            // Create 1-2 quizzes per course
            $quizCount = rand(1, 2);
            
            for ($i = 0; $i < $quizCount; $i++) {
                Quiz::create([
                    'course_id' => $course->id,
                    'title' => [
                        'en' => $i === 0 ? 'Mid-Course Assessment' : 'Final Exam',
                        'ar' => $i === 0 ? 'تقييم منتصف الدورة' : 'الاختبار النهائي',
                    ],
                    'description' => [
                        'en' => $i === 0 
                            ? 'Test your understanding of the concepts covered so far' 
                            : 'Comprehensive test covering all course materials',
                        'ar' => $i === 0 
                            ? 'اختبر فهمك للمفاهيم التي تمت تغطيتها حتى الآن' 
                            : 'اختبار شامل يغطي جميع مواد الدورة',
                    ],
                    'time_limit' => $i === 0 ? 15 : 30,
                    'passing_score' => $i === 0 ? 60 : 70,
                    'max_attempts' => $i === 0 ? 3 : 2,
                    'shuffle_questions' => true,
                    'show_correct_answers' => $i === 0,
                    'is_active' => true,
                ]);
            }
        }
    }
}

