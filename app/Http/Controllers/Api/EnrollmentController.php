<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function enroll($id)
    {
        $user = auth()->user();

        // Validate course exists and is published
        $course = Course::where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$course) {
            return response()->api(
                'Course does not exist or is not published',
                null,
                404
            );
        }

        // Prevent duplicate enrollment
        $exists = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return response()->api(
                'You are already enrolled in this course',
                null,
                409
            );
        }

        // Create enrollment
        $enrollment = CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        return response()->api(
            'Enrolled in course successfully',
            $enrollment,
            201
        );
    }

    public function myEnrollments()
    {
        $user = auth()->user();

        $enrollments = CourseEnrollment::byUser($user->id)
            ->with('course')
            ->get();

        return response()->api(
            'My enrollments retrieved successfully',
            $enrollments
        );
    }

    public function courseEnrollments($id)
    {
        $course = Course::findOrFail($id);

        $enrollments = CourseEnrollment::where('course_id', $course->id)
            ->with('user')
            ->get();

        return response()->api(
            'Course enrollments retrieved successfully',
            $enrollments
        );
    }
}
