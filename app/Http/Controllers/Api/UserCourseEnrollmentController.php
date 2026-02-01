<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserCourseEnrollment;
use Illuminate\Http\Request;
use App\Http\Resources\UserCourseEnrollmentResource;

class UserCourseEnrollmentController extends Controller
{
    /**
     * 1️⃣ Enroll user in a course
     */
    public function enroll(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $enrollment = UserCourseEnrollment::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $request->course_id,
            ],
            [
                'enrolled_at' => now(),
                'progress_percentage' => 0,
                'points_earned' => 0,
            ]
        );

        // تحميل العلاقات عشان الـ resource يشتغل صح
        $enrollment->load(['user', 'course']);

        return response()->api(
            'User enrolled successfully',
            new UserCourseEnrollmentResource($enrollment)
        );
    }

    /**
     * 2️⃣ Get my enrolled courses
     */
    public function myEnrollments()
    {
        $user = auth()->user();

        $enrollments = UserCourseEnrollment::byUser($user->id)
            ->with(['user', 'course'])
            ->get();

        return response()->api(
            'My enrollments retrieved successfully',
            UserCourseEnrollmentResource::collection($enrollments)
        );
    }

    /**
     * 3️⃣ Completed courses
     */
    public function completedCourses()
    {
        $user = auth()->user();

        $courses = UserCourseEnrollment::byUser($user->id)
            ->whereNotNull('completed_at')
            ->with(['user', 'course'])
            ->get();

        return response()->api(
            'Completed courses fetched successfully',
            UserCourseEnrollmentResource::collection($courses)
        );
    }

    /**
     * 4️⃣ In-progress courses
     */
    public function inProgressCourses()
    {
        $user = auth()->user();

        $courses = UserCourseEnrollment::byUser($user->id)
            ->whereNull('completed_at')
            ->with(['user', 'course'])
            ->get();

        return response()->api(
            'In progress courses fetched successfully',
            UserCourseEnrollmentResource::collection($courses)
        );
    }
}
