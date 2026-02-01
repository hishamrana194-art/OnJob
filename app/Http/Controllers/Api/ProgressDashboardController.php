<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserCourseEnrollment;
use App\Models\UserModuleProgress;
use App\Models\UserQuizAttempt;
use Illuminate\Http\Request;

class ProgressDashboardController extends Controller
{
    public function myProgress()
    {
        $user = auth()->user();

        // 🟢 Enrolled Courses
        $enrollments = UserCourseEnrollment::where('user_id', $user->id)
            ->with('course.modules')
            ->get();

        $courses = [];
        $totalCompletedModules = 0;
        $totalModules = 0;

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;

            $modulesCount = $course->modules->count();

            $completedModules = UserModuleProgress::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('is_completed', true)
                ->count();

            $completionPercentage = $modulesCount > 0
                ? round(($completedModules / $modulesCount) * 100, 2)
                : 0;

            $courses[] = [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'completion_percentage' => $completionPercentage,
                'modules_completed' => $completedModules,
                'total_modules' => $modulesCount,
            ];

            $totalCompletedModules += $completedModules;
            $totalModules += $modulesCount;
        }

        // 🟢 Passed Quizzes
        $passedQuizzes = UserQuizAttempt::where('user_id', $user->id)
            ->where('is_passed', true)
            ->count();

        return response()->api(
            'My progress retrieved successfully',
            [
                'total_courses' => count($courses),
                'total_modules' => $totalModules,
                'completed_modules' => $totalCompletedModules,
                'passed_quizzes' => $passedQuizzes,
                'total_points' => $user->points,
                'courses' => $courses,
            ]
        );
    }
}
