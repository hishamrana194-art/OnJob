<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModuleItem;
use App\Models\UserCourseEnrollment;
use App\Models\UserModuleItemProgress;
use App\Models\UserQuizAttempt;

class ProgressDashboardController extends Controller
{
    public function myProgress()
    {
        $user = auth()->user();

        // Enrolled courses
        $enrollments = UserCourseEnrollment::where('user_id', $user->id)
            ->with('course.modules')
            ->get();

        $courses = [];
        $totalCompletedItems = 0;
        $totalItems = 0;

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;

            $itemsCount = ModuleItem::whereHas('module', fn($q) => $q->where('course_id', $course->id))->count();
            $completedCount = UserModuleItemProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->whereHas('moduleItem', fn($q) => $q->whereHas('module', fn($q2) => $q2->where('course_id', $course->id)))
                ->count();

            $completionPercentage = $itemsCount > 0
                ? round(($completedCount / $itemsCount) * 100, 2)
                : 0;

            $courses[] = [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'completion_percentage' => $completionPercentage,
                // Backward compatibility keys
                'modules_completed' => $completedCount,
                'total_modules' => $itemsCount,
                // New clearer keys
                'items_completed' => $completedCount,
                'total_items' => $itemsCount,
            ];

            $totalCompletedItems += $completedCount;
            $totalItems += $itemsCount;
        }

        $passedQuizzes = UserQuizAttempt::where('user_id', $user->id)
            ->where('is_passed', true)
            ->count();

        $xp = (int)($user->xp ?? 0);
        $coins = (int)($user->coins ?? 0);

        return response()->api(
            'My progress retrieved successfully',
            [
                'total_courses' => count($courses),
                // Backward compatibility (Flutter expects these)
                'total_modules' => $totalItems,
                'completed_modules' => $totalCompletedItems,

                // New clearer keys
                'total_items' => $totalItems,
                'completed_items' => $totalCompletedItems,

                'passed_quizzes' => $passedQuizzes,

                // Backward compatibility
                'total_points' => $xp,

                'xp' => $xp,
                'coins' => $coins,

                'courses' => $courses,
            ]
        );
    }
}
