<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\Reel;
use Illuminate\Database\Seeder;

class ReelSeeder extends Seeder
{
    // Short public MP4 videos hosted by Google (no auth needed, always reachable)
    private const VIDEOS = [
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WhatCarCanYouGetForAGrand.mp4',
        'https://flutter.github.io/assets-for-api-docs/assets/videos/bee.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
        'https://media.w3.org/2010/05/sintel/trailer.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
        'https://flutter.github.io/assets-for-api-docs/assets/videos/butterfly.mp4',
    ];

    public function run(): void
    {
        $courses = Course::where('status', 'published')->get();
        $quizzes = Quiz::all();

        $reels = [
            // --- Safety ---
            [
                'title' => ['en' => 'Quick Safety Tip 🔒', 'ar' => 'نصيحة سريعة عن السلامة 🔒'],
                'description' => ['en' => '3 simple steps to keep yourself safe on site.', 'ar' => '٣ خطوات بسيطة تحافظ فيها على سلامتك في الموقع.'],
                'duration' => 15,
                'coins_on_complete' => 5,
                'xp_on_complete' => 10,
                'order' => 1,
            ],
            [
                'title' => ['en' => 'Common Field Mistakes 🚫', 'ar' => 'أخطاء شائعة في الميدان 🚫'],
                'description' => ['en' => 'Avoid 3 mistakes that increase the likelihood of accidents.', 'ar' => 'تجنّب ٣ أخطاء تزيد من احتمالية الحوادث.'],
                'duration' => 20,
                'coins_on_complete' => 5,
                'xp_on_complete' => 10,
                'order' => 2,
            ],
            [
                'title' => ['en' => 'Emergency Exit Procedures 🚪', 'ar' => 'إجراءات مخرج الطوارئ 🚪'],
                'description' => ['en' => 'Know exactly what to do in case of an emergency.', 'ar' => 'اعرف تمامًا ماذا تفعل في حالة الطوارئ.'],
                'duration' => 25,
                'coins_on_complete' => 10,
                'xp_on_complete' => 15,
                'order' => 3,
            ],
            // --- Leadership ---
            [
                'title' => ['en' => 'Leadership in 60 Seconds ⚡', 'ar' => 'القيادة في ٦٠ ثانية ⚡'],
                'description' => ['en' => 'The key difference between a manager and a leader.', 'ar' => 'الفرق الجوهري بين المدير والقائد.'],
                'duration' => 60,
                'coins_on_complete' => 10,
                'xp_on_complete' => 20,
                'order' => 4,
            ],
            [
                'title' => ['en' => 'How Great Leaders Listen 👂', 'ar' => 'كيف يستمع القادة العظماء 👂'],
                'description' => ['en' => 'Active listening is the #1 leadership skill. Here\'s why.', 'ar' => 'الاستماع الفعّال هو المهارة القيادية الأولى. إليك السبب.'],
                'duration' => 45,
                'coins_on_complete' => 10,
                'xp_on_complete' => 20,
                'order' => 5,
            ],
            [
                'title' => ['en' => 'Giving Feedback That Works 💬', 'ar' => 'تقديم تغذية راجعة فعّالة 💬'],
                'description' => ['en' => 'The SBI method: Situation, Behavior, Impact.', 'ar' => 'طريقة SBI: الموقف، السلوك، التأثير.'],
                'duration' => 50,
                'coins_on_complete' => 10,
                'xp_on_complete' => 20,
                'order' => 6,
            ],
            // --- Customer Service ---
            [
                'title' => ['en' => 'The Art of Customer Service 👤', 'ar' => 'فن التعامل مع العميل 👤'],
                'description' => ['en' => 'One sentence that transforms the entire customer experience.', 'ar' => 'جملة واحدة تغيّر تجربة العميل بالكامل.'],
                'duration' => 30,
                'coins_on_complete' => 5,
                'xp_on_complete' => 10,
                'order' => 7,
            ],
            [
                'title' => ['en' => 'Handling Angry Customers 😤', 'ar' => 'التعامل مع العملاء الغاضبين 😤'],
                'description' => ['en' => '4-step technique to calm any difficult customer.', 'ar' => 'أسلوب من ٤ خطوات لتهدئة أي عميل صعب.'],
                'duration' => 40,
                'coins_on_complete' => 10,
                'xp_on_complete' => 15,
                'order' => 8,
            ],
            // --- Productivity ---
            [
                'title' => ['en' => 'Time Management Hack ⏰', 'ar' => 'خدعة إدارة الوقت ⏰'],
                'description' => ['en' => 'Double your productivity with this one simple rule.', 'ar' => 'ضاعف إنتاجيتك بهذه القاعدة البسيطة الواحدة.'],
                'duration' => 35,
                'coins_on_complete' => 5,
                'xp_on_complete' => 10,
                'order' => 9,
            ],
            [
                'title' => ['en' => 'Beat Procrastination Now ✅', 'ar' => 'تغلّب على التسويف الآن ✅'],
                'description' => ['en' => 'Use the 2-minute rule to stop delaying tasks forever.', 'ar' => 'استخدم قاعدة الدقيقتين لوقف التأجيل نهائيًا.'],
                'duration' => 30,
                'coins_on_complete' => 5,
                'xp_on_complete' => 10,
                'order' => 10,
            ],
            [
                'title' => ['en' => 'The Power of Deep Work 🧠', 'ar' => 'قوة العمل العميق 🧠'],
                'description' => ['en' => 'How top performers eliminate distraction and stay focused.', 'ar' => 'كيف يتخلص أفضل الموظفين من التشتت ويبقون مركّزين.'],
                'duration' => 55,
                'coins_on_complete' => 10,
                'xp_on_complete' => 20,
                'order' => 11,
            ],
            // --- Communication ---
            [
                'title' => ['en' => 'Email That Gets Replies 📧', 'ar' => 'بريد إلكتروني يحصل على ردود 📧'],
                'description' => ['en' => 'Write clearer emails with the BLUF technique.', 'ar' => 'اكتب رسائل أوضح باستخدام أسلوب BLUF.'],
                'duration' => 25,
                'coins_on_complete' => 5,
                'xp_on_complete' => 10,
                'order' => 12,
            ],
            [
                'title' => ['en' => 'Presenting with Confidence 🎤', 'ar' => 'التقديم بثقة 🎤'],
                'description' => ['en' => '3 body language tricks that make you look confident.', 'ar' => '٣ حيل لغة جسد تجعلك تبدو واثقًا.'],
                'duration' => 40,
                'coins_on_complete' => 10,
                'xp_on_complete' => 15,
                'order' => 13,
            ],
            // --- Teamwork ---
            [
                'title' => ['en' => 'Build Trust in Your Team 🤝', 'ar' => 'ابنِ الثقة في فريقك 🤝'],
                'description' => ['en' => 'Why psychological safety = better team results.', 'ar' => 'لماذا السلامة النفسية = نتائج أفضل للفريق.'],
                'duration' => 45,
                'coins_on_complete' => 10,
                'xp_on_complete' => 20,
                'order' => 14,
            ],
            [
                'title' => ['en' => 'Conflict Resolution at Work ⚖️', 'ar' => 'حل النزاعات في العمل ⚖️'],
                'description' => ['en' => 'Turn team conflicts into growth opportunities.', 'ar' => 'حوّل نزاعات الفريق إلى فرص للنمو.'],
                'duration' => 50,
                'coins_on_complete' => 10,
                'xp_on_complete' => 20,
                'order' => 15,
            ],
        ];

        foreach ($reels as $i => $data) {
            // Cycle through real video URLs
            $videoUrl = self::VIDEOS[$i % count(self::VIDEOS)];

            // Link some reels to a course/quiz
            $course = ($i % 3 === 0 && $courses->isNotEmpty()) ? $courses[$i % $courses->count()] : null;
            $quiz   = ($i % 4 === 0 && $quizzes->isNotEmpty()) ? $quizzes[$i % $quizzes->count()] : null;

            Reel::updateOrCreate(
                ['order' => $data['order']],
                [
                    ...$data,
                    'video_url'     => $videoUrl,
                    'thumbnail_url' => null,
                    'course_id'     => $course?->id,
                    'quiz_id'       => $quiz?->id,
                    'is_active'     => true,
                ]
            );
        }

        $this->command->info('Seeded ' . count($reels) . ' reels with real video URLs.');
    }
}
