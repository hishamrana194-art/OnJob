<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionBlank;
use App\Models\QuestionMatch;
use App\Models\QuestionOption;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $quizzes = Quiz::all();

        foreach ($quizzes as $quiz) {
            $this->createSingleChoiceQuestion($quiz, 1);
            $this->createMultipleChoiceQuestion($quiz, 2);
            $this->createMatchingQuestion($quiz, 3);
            $this->createCompleteQuestion($quiz, 4);
        }
    }

    private function createSingleChoiceQuestion(Quiz $quiz, int $order): void
    {
        $question = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'single_choice',
            'question_text' => [
                'en' => 'What is the main purpose of this course?',
                'ar' => 'ما هو الهدف الرئيسي من هذه الدورة؟',
            ],
            'explanation' => [
                'en' => 'The correct answer focuses on practical skill development.',
                'ar' => 'الإجابة الصحيحة تركز على تطوير المهارات العملية.',
            ],
            'points' => 10,
            'order' => $order,
            'is_active' => true,
        ]);

        $options = [
            ['text' => ['en' => 'To learn theoretical concepts only', 'ar' => 'لتعلم المفاهيم النظرية فقط'], 'correct' => false],
            ['text' => ['en' => 'To develop practical skills through hands-on learning', 'ar' => 'لتطوير المهارات العملية من خلال التعلم التطبيقي'], 'correct' => true],
            ['text' => ['en' => 'To get a certificate', 'ar' => 'للحصول على شهادة'], 'correct' => false],
            ['text' => ['en' => 'To watch videos only', 'ar' => 'لمشاهدة الفيديوهات فقط'], 'correct' => false],
        ];

        foreach ($options as $i => $opt) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $opt['text'],
                'is_correct' => $opt['correct'],
                'order' => $i + 1,
            ]);
        }
    }

    private function createMultipleChoiceQuestion(Quiz $quiz, int $order): void
    {
        $question = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
            'question_text' => [
                'en' => 'Which of the following are key learning outcomes? (Select all that apply)',
                'ar' => 'أي مما يلي يعتبر من نتائج التعلم الرئيسية؟ (اختر كل ما ينطبق)',
            ],
            'points' => 15,
            'order' => $order,
            'is_active' => true,
        ]);

        $options = [
            ['text' => ['en' => 'Understanding core concepts', 'ar' => 'فهم المفاهيم الأساسية'], 'correct' => true],
            ['text' => ['en' => 'Memorizing without understanding', 'ar' => 'الحفظ بدون فهم'], 'correct' => false],
            ['text' => ['en' => 'Applying knowledge to real scenarios', 'ar' => 'تطبيق المعرفة على سيناريوهات حقيقية'], 'correct' => true],
            ['text' => ['en' => 'Building practical projects', 'ar' => 'بناء مشاريع عملية'], 'correct' => true],
        ];

        foreach ($options as $i => $opt) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $opt['text'],
                'is_correct' => $opt['correct'],
                'order' => $i + 1,
            ]);
        }
    }

    private function createMatchingQuestion(Quiz $quiz, int $order): void
    {
        $question = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'matching',
            'question_text' => [
                'en' => 'Match the terms with their definitions:',
                'ar' => 'طابق المصطلحات مع تعريفاتها:',
            ],
            'points' => 20,
            'order' => $order,
            'is_active' => true,
        ]);

        $matches = [
            ['left' => ['en' => 'Module', 'ar' => 'موديول'], 'right' => ['en' => 'A section of the course', 'ar' => 'قسم من الدورة']],
            ['left' => ['en' => 'Quiz', 'ar' => 'اختبار'], 'right' => ['en' => 'Assessment tool', 'ar' => 'أداة تقييم']],
            ['left' => ['en' => 'Certificate', 'ar' => 'شهادة'], 'right' => ['en' => 'Proof of completion', 'ar' => 'إثبات إتمام']],
        ];

        foreach ($matches as $i => $match) {
            QuestionMatch::create([
                'question_id' => $question->id,
                'left_side' => $match['left'],
                'right_side' => $match['right'],
                'order' => $i + 1,
            ]);
        }
    }

    private function createCompleteQuestion(Quiz $quiz, int $order): void
    {
        $question = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'complete',
            'question_text' => [
                'en' => 'A course is divided into [blank] which contain [blank].',
                'ar' => 'تنقسم الدورة إلى [blank] والتي تحتوي على [blank].',
            ],
            'points' => 10,
            'order' => $order,
            'is_active' => true,
        ]);

        $blanks = [
            ['position' => 1, 'answer' => ['en' => 'modules', 'ar' => 'موديولات']],
            ['position' => 2, 'answer' => ['en' => 'items', 'ar' => 'عناصر']],
        ];

        foreach ($blanks as $i => $blank) {
            QuestionBlank::create([
                'question_id' => $question->id,
                'blank_position' => $blank['position'],
                'correct_answer' => $blank['answer'],
                'order' => $i + 1,
            ]);
        }
    }
}

