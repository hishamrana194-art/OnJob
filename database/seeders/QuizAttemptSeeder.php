<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Quiz;
use App\Models\User;
use App\Models\UserCourseEnrollment;
use App\Models\UserQuizAttempt;
use Illuminate\Database\Seeder;

/**
 * Seeds one quiz attempt per (user, quiz) pair for enrolled employees.
 *
 * - Employees with points > 200 get a passing score.
 * - employee@example.com gets a failing score (low points = 25) so
 *   the "retry" flow is testable in the app.
 * - module_id is resolved from quiz->moduleItem->module_id.
 *   Quizzes without a moduleItem (course-level standalone quizzes)
 *   fall back to the first module of the course.
 */
class QuizAttemptSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('user_type', 'employer_user')->get();

        foreach ($employees as $employee) {
            $enrolledCourseIds = UserCourseEnrollment::where('user_id', $employee->id)
                ->pluck('course_id');

            if ($enrolledCourseIds->isEmpty()) {
                continue;
            }

            $quizzes = Quiz::whereIn('course_id', $enrolledCourseIds)
                ->where('is_active', true)
                ->with('moduleItem')
                ->get();

            foreach ($quizzes as $quiz) {
                // Resolve module_id
                $moduleId = $quiz->moduleItem?->module_id
                    ?? Module::where('course_id', $quiz->course_id)->value('id');

                if (!$moduleId) {
                    continue; // skip if no module found
                }

                $passingScore = $quiz->passing_score ?? 60;
                $passed       = $employee->points > 200;
                $score        = $passed
                    ? rand($passingScore, 100)
                    : rand(20, max(20, $passingScore - 1));

                UserQuizAttempt::firstOrCreate(
                    [
                        'user_id' => $employee->id,
                        'quiz_id' => $quiz->id,
                    ],
                    [
                        'module_id'          => $moduleId,
                        'attempt_number'     => 1,
                        'score'              => $score,
                        'answers'            => $this->fakeAnswers(),
                        'is_passed'          => $passed,
                        'time_taken_seconds' => rand(90, 600),
                    ]
                );
            }
        }

        $this->command->info('Seeded quiz attempts for ' . $employees->count() . ' employees.');
    }

    private function fakeAnswers(): array
    {
        return [
            ['question_id' => 1, 'answer' => 'option_2', 'is_correct' => true],
            ['question_id' => 2, 'answer' => ['option_1', 'option_3'], 'is_correct' => true],
            ['question_id' => 3, 'answer' => 'option_1', 'is_correct' => false],
        ];
    }
}
