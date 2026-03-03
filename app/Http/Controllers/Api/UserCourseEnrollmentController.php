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
        $enrollment->load(['user', 'course.vendor']);

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
            ->with(['user', 'course.vendor'])
            ->get();

        // Ensure progress is always accurate on first load.
        // Some old records may have stale progress_percentage; recompute from completed module items.
        foreach ($enrollments as $enrollment) {
            try {
                $courseId = $enrollment->course_id;
                $totalItems = \App\Models\ModuleItem::whereHas('module', function ($q) use ($courseId) {
                        $q->where('course_id', $courseId);
                    })
                    ->count();

                $completedItems = \App\Models\UserModuleItemProgress::where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->whereIn('module_item_id', function ($sub) use ($courseId) {
                        $sub->select('module_items.id')
                            ->from('module_items')
                            ->join('modules', 'modules.id', '=', 'module_items.module_id')
                            ->where('modules.course_id', $courseId);
                    })
                    ->count();

                $percent = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
                // Persist to keep other endpoints consistent
                if ((float)$enrollment->progress_percentage !== (float)$percent) {
                    $enrollment->progress_percentage = $percent;
                    if ($percent >= 100 && !$enrollment->completed_at) {
                        $enrollment->completed_at = now();
                    }
                    $enrollment->save();
                }
            } catch (\Throwable $e) {
                // Don't break list due to a progress calc edge-case
            }
        }

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
            ->with(['user', 'course.vendor'])
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
            ->with(['user', 'course.vendor'])
            ->get();

        return response()->api(
            'In progress courses fetched successfully',
            UserCourseEnrollmentResource::collection($courses)
        );
    }
}
