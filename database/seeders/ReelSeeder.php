<?php

namespace Database\Seeders;

use App\Models\Reel;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class ReelSeeder extends Seeder
{
    public function run(): void
    {
        $firstCourse = Course::first();
        $firstQuiz = Quiz::first();

        $reels = [
            [
                'title' => ['en' => 'Quick Safety Tip 🔒', 'ar' => 'نصيحة سريعة عن السلامة 🔒'],
                'description' => ['en' => '3 simple steps to keep yourself safe on site.', 'ar' => '٣ خطوات بسيطة تحافظ فيها على سلامتك في الموقع.'],
                'video_url' => 'https://flutter.github.io/assets-for-api-docs/assets/videos/bee.mp4',
                'duration' => 30,
                'thumbnail_url' => null,
                'course_id' => $firstCourse?->id,
                'quiz_id' => $firstQuiz?->id,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => ['en' => 'The Art of Customer Service 👤', 'ar' => 'فن التعامل مع العميل 👤'],
                'description' => ['en' => 'One sentence that transforms the entire customer experience.', 'ar' => 'جملة واحدة تغيّر تجربة العميل بالكامل.'],
                'video_url' => 'https://flutter.github.io/assets-for-api-docs/assets/videos/bee.mp4',
                'duration' => 45,
                'thumbnail_url' => null,
                'course_id' => null,
                'quiz_id' => null,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => ['en' => 'Common Field Mistakes 🚫', 'ar' => 'أخطاء شائعة في الميدان 🚫'],
                'description' => ['en' => 'Avoid 3 mistakes that increase the likelihood of accidents.', 'ar' => 'تجنّب ٣ أخطاء تزيد من احتمالية الحوادث.'],
                'video_url' => 'https://flutter.github.io/assets-for-api-docs/assets/videos/bee.mp4',
                'duration' => 40,
                'thumbnail_url' => null,
                'course_id' => null,
                'quiz_id' => null,
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => ['en' => 'Leadership in 60 Seconds ⚡', 'ar' => 'القيادة في ٦٠ ثانية ⚡'],
                'description' => ['en' => 'The key difference between a manager and a leader.', 'ar' => 'الفرق الجوهري بين المدير والقائد.'],
                'video_url' => 'https://flutter.github.io/assets-for-api-docs/assets/videos/bee.mp4',
                'duration' => 60,
                'thumbnail_url' => null,
                'course_id' => null,
                'quiz_id' => null,
                'is_active' => true,
                'order' => 4,
            ],
            [
                'title' => ['en' => 'Time Management Hack ⏰', 'ar' => 'خدعة إدارة الوقت ⏰'],
                'description' => ['en' => 'Double your productivity with this one simple rule.', 'ar' => 'ضاعف إنتاجيتك بهذه القاعدة البسيطة الواحدة.'],
                'video_url' => 'https://flutter.github.io/assets-for-api-docs/assets/videos/bee.mp4',
                'duration' => 35,
                'thumbnail_url' => null,
                'course_id' => null,
                'quiz_id' => null,
                'is_active' => true,
                'order' => 5,
            ],
        ];

        foreach ($reels as $reel) {
            Reel::create($reel);
        }
    }
}
