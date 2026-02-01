<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;

class QuizSubmissionController extends Controller
{
    public function submit(Request $request, $id)
    {
        $user = auth()->user();

        $quiz = Quiz::with('questions')->find($id);

        if (!$quiz) {
            return response()->api('Quiz not found', null, 404);
        }

        $request->validate([
            'answers' => 'required|array',
            'time_taken_seconds' => 'required|numeric|min:0',
        ]);

        // ❌ تحقق من أن المستخدم لم يحاول الامتحان من قبل
        $alreadyAttempted = UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->get();

        if ($alreadyAttempted) {
            return response()->api(
                'You have already attempted this quiz. Retakes are not allowed.',
                $alreadyAttempted,
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
                    if ($userAnswer === $question->correct_answer) $score += $question->points;
                    break;
                case 'multiple_choice':
                    if (is_array($userAnswer) &&
                        count(array_diff($question->correct_answer, $userAnswer)) === 0 &&
                        count(array_diff($userAnswer, $question->correct_answer)) === 0
                    ) $score += $question->points;
                    break;
                case 'fill_in_blanks':
                    if (strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer))) $score += $question->points;
                    break;
                case 'matching':
                    if (is_array($userAnswer) && $userAnswer == $question->correct_answer) $score += $question->points;
                    break;
            }
        }

        $isPassed = ($score / $totalPoints) * 100 >= $quiz->passing_score;

        $attempt = UserQuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'module_id' => $quiz->module_id,
            'attempt_number' => 1,
            'score' => $score,
            'answers' => $answers,
            'is_passed' => $isPassed,
            'time_taken_seconds' => $request->time_taken_seconds,
        ]);

        if ($isPassed) {
            $user->increment('points', $quiz->points_reward ?? 0);
        }

        return response()->api('Quiz submitted successfully', $attempt, 201);
    }
}
