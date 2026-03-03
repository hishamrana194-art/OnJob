<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\UserModuleItemProgress;
use App\Models\UserCourseEnrollment;
use App\Models\UserQuizAttempt;
use App\Services\GamificationService;
use App\Services\BadgeService;
use Illuminate\Http\Request;

class QuizSubmissionController extends Controller
{
    public function submit(Request $request, $id)
    {
        $user = auth()->user();

        $quiz = Quiz::with(['questions.options','questions.blanks','questions.matches','moduleItem','course'])->find($id);

        if (!$quiz) {
            return response()->api('Quiz not found', null, 404);
        }

        $request->validate([
            'answers' => 'required|array',
            'time_taken_seconds' => 'required|numeric|min:0',
        ]);

        // Enforce max_attempts (null = unlimited)
        $attemptCount = UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        $maxAllowed = $quiz->max_attempts; // null = unlimited
        if ($maxAllowed !== null && $attemptCount >= $maxAllowed) {
            $lastAttempt = UserQuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->latest()
                ->first();
            return response()->api(
                'Maximum attempts reached.',
                [
                    'attempt' => $lastAttempt,
                    'attempts_used' => $attemptCount,
                    'max_attempts' => $maxAllowed,
                ],
                403
            );
        }

        $answers = $request->input('answers');
        $score = 0;
        $totalPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $answers[$question->id] ?? null;

            switch ($question->type) {
                case 'single_choice':
                    // Flutter sends the selected option's ID as the answer value
                    $correctOption = $question->options->firstWhere('is_correct', true);
                    if ($correctOption && (int)$userAnswer === (int)$correctOption->id) {
                        $score += $question->points;
                    }
                    break;
                case 'multiple_choice':
                    // Flutter sends an array of selected option IDs
                    $correctIds = $question->options
                        ->where('is_correct', true)
                        ->pluck('id')
                        ->map(fn($v) => (int)$v)
                        ->sort()->values()->toArray();
                    $submittedIds = is_array($userAnswer)
                        ? collect($userAnswer)->map(fn($v) => (int)$v)->sort()->values()->toArray()
                        : [];
                    if ($correctIds === $submittedIds) $score += $question->points;
                    break;
                case 'fill_in_blanks':
                    // Flutter sends the typed text; compare against each blank's correct answer
                    $blank = $question->blanks->first();
                    $correctText = $blank?->correct_answer ?? '';
                    if (strtolower(trim((string)$userAnswer)) === strtolower(trim($correctText))) {
                        $score += $question->points;
                    }
                    break;
                case 'matching':
                    // Flutter sends {leftId: rightId, ...}; compare against QuestionMatch records
                    if (is_array($userAnswer)) {
                        $allCorrect = true;
                        foreach ($question->matches as $match) {
                            if ((int)($userAnswer[$match->id] ?? -1) !== (int)$match->match_id) {
                                $allCorrect = false;
                                break;
                            }
                        }
                        if ($allCorrect && $question->matches->isNotEmpty()) $score += $question->points;
                    }
                    break;
            }
        }

        $isPassed = $totalPoints > 0
            ? (($score / $totalPoints) * 100 >= $quiz->passing_score)
            : false;

        $attempt = UserQuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'module_id' => $quiz->module_id ?? null,
            'attempt_number' => $attemptCount + 1,
            'score' => $score,
            'answers' => $answers,
            'is_passed' => $isPassed,
            'time_taken_seconds' => $request->time_taken_seconds,
        ]);

        if ($isPassed) {
            // 1) Mark the quiz module_item as completed (so it counts in learning path progress)
            if ($quiz->module_item_id) {
                UserModuleItemProgress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'module_item_id' => (int)$quiz->module_item_id,
                    ],
                    [
                        'module_id' => $quiz->moduleItem?->module_id ?? null,
                        'is_completed' => true,
                        'watched_duration_seconds' => 0,
                    ]
                );
            }

            // 2) Award XP + Coins (once per quiz)
            GamificationService::awardXp($user, GamificationService::XP_QUIZ_PASS, 'quiz_pass', 'quiz', (int)$quiz->id);
            GamificationService::awardCoins($user, GamificationService::COINS_QUIZ_PASS, 'quiz_pass', 'quiz', (int)$quiz->id);

            // 3) Recalculate course enrollment progress immediately
            $courseId = (int)($quiz->course_id ?? 0);
            if ($courseId > 0) {
                $totalItems = \App\Models\ModuleItem::whereHas('module', function ($q) use ($courseId) {
                    $q->where('course_id', $courseId);
                })->count();

                $completedItemsForCourse = UserModuleItemProgress::where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->whereIn('module_item_id', \App\Models\ModuleItem::whereHas('module', function ($q) use ($courseId) {
                        $q->where('course_id', $courseId);
                    })->pluck('id'))
                    ->count();

                if ($totalItems > 0) {
                    $progressPercentage = (int)round(($completedItemsForCourse / $totalItems) * 100);
                    UserCourseEnrollment::where('user_id', $user->id)
                        ->where('course_id', $courseId)
                        ->update([
                            'progress_percentage' => $progressPercentage,
                            'started_at' => now(),
                        ]);
                }

                // 4) Course-context badge notification (ties the achievement to a learning path)
                try {
                    $courseTitle = $quiz->course?->getTranslation('title', $user->locale ?? 'ar')
                        ?? $quiz->course?->getTranslation('title', 'ar')
                        ?? $quiz->course?->getTranslation('title', 'en')
                        ?? null;

                    if ($courseTitle) {
                        BadgeService::award($user, 'first_quiz', $courseTitle);
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }

        return response()->api('Quiz submitted successfully', [
            'attempt' => $attempt,
            'xp' => (int)($user->fresh()->xp ?? 0),
            'coins' => (int)($user->fresh()->coins ?? 0),
            'attempts_used' => $attemptCount + 1,
            'max_attempts' => $quiz->max_attempts,
            'passing_score' => $quiz->passing_score,
        ], 201);
    }
}
