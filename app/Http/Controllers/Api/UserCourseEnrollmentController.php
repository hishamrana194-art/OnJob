<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserCourseEnrollment;
use Illuminate\Support\Facades\Auth;

class UserCourseEnrollmentController extends Controller
{
    // Enroll user in a course
    public function enroll(Request $request)
    {
        $user=auth()->user();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $enrollment = UserCourseEnrollment::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $request->course_id
            ],
            [
                'enrolled_at' => now(),
                'progress_percentage' => 0,
                'points_earned' => 0
            ]
        );

        return response()->api('User enrolled successfully', $enrollment);
    }

    // Get enrolled courses for authenticated user
    public function myEnrollments()
    {
        $user=auth()->user();

        $enrollments = UserCourseEnrollment::byUser($user->id)->get();

        return response()->api('User enrollments retrieved', $enrollments);
    }
    public function completedCourses()
    {
        $user=auth()->user();

        $courses = UserCourseEnrollment::with('course')
            ->byUser($user->id)
            ->completed()
            ->get();

        return response()->api(
            'Completed courses fetched successfully',
            $courses
        );
    }

    /**
     * Get in-progress courses (by token)
     */
    public function inProgressCourses()
    {
        $user=auth()->user();

        $courses = UserCourseEnrollment::with('course')
            ->byUser($user->id)
            ->inProgress()
            ->get();

        return response()->api(
            'In progress courses fetched successfully',
            $courses
        );
    }

}
