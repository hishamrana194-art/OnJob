<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseUserController extends Controller
{

public function getCoursesUser()
{
    $user = auth()->user();

    // نتأكد إن اليوزر تابع لفيندور
    if (! $user->vendor) {
          return response()->api(
            'Unauthorized',
            null,
            403
        );
    }

    // نجيب الكورسات اللي تابعة للفيندور اللي اليوزر تابع ليه
    $courses = $user->vendor
        ->courses() // علاقة الفيندور بالكورسات
        ->where('status', 'published') // نجيب اللي منشورة بس
        ->get();

    // نرجع فقط الكورسات
      return response()->api(
        'Courses retrieved successfully',
        $courses,
        200
    );
}
}



