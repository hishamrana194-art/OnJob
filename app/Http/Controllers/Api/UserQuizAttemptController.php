<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;

class UserQuizAttemptController extends Controller
{
    /**
     * 1️⃣ Store quiz attempt (ONLY ONCE)
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'module_id' => 'required|exists:modules,id',
            'score' => 'required|numeric|min:0|max:100',
            'answers' => 'required|array',
            'time_taken_seconds' => 'required|numeric|min:0',
        ]);

        // ❌ Check if user already attempted this quiz
        $alreadyAttempted = UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $request->quiz_id)
            ->exists();

        if ($alreadyAttempted) {
            return response()->api(
                'You have already attempted this quiz. Retakes are not allowed.',
                null,
                403
            );
        }

        // ✅ Create the only allowed attempt
        $attempt = UserQuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $request->quiz_id,
            'module_id' => $request->module_id,
            'attempt_number' => 1, // always 1
            'score' => $request->score,
            'answers' => $request->answers,
            'is_passed' => $request->score >= 60,
            'time_taken_seconds' => $request->time_taken_seconds,
        ]);

        return response()->api(
            'Quiz submitted successfully',
            $attempt,
            201
        );
    }

    /**
     * 2️⃣ Get my quiz attempts
     */
    public function myAttempts()
    {
        $user = auth()->user();

        $attempts = UserQuizAttempt::byUser($user->id)
            ->latest()
            ->get();

        return response()->api(
            'My quiz attempts retrieved successfully',
            $attempts
        );
    }

    /**
     * 3️⃣ Get my attempt for a specific quiz
     */
    public function myAttemptByQuiz($quizId)
    {
        $user = auth()->user();

        $attempt = UserQuizAttempt::byUser($user->id)
            ->where('quiz_id', $quizId)
            ->first();

        if (!$attempt) {
            return response()->api(
                'No attempt found for this quiz',
                null,
                404
            );
        }

        return response()->api(
            'Quiz attempt retrieved successfully',
            $attempt
        );
    }

    /**
     * 4️⃣ Get passed quizzes
     */
    public function passedAttempts()
    {
        $user = auth()->user();

        $attempts = UserQuizAttempt::byUser($user->id)
            ->passed()
            ->get();

        return response()->api(
            'Passed quiz attempts retrieved successfully',
            $attempts
        );
    }

    /**
     * 5️⃣ Get failed quizzes
     */
    public function failedAttempts()
    {
        $user = auth()->user();

        $attempts = UserQuizAttempt::byUser($user->id)
            ->failed()
            ->get();

        return response()->api(
            'Failed quiz attempts retrieved successfully',
            $attempts
        );
    }
}
