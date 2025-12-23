<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all();
        return response()->api(
            'Courses retrieved successfully',
            $courses,
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());
        return response()->api(
            'Course created successfully',
            $course,
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::findorFail($id);
        return response()->api(
            'Course retrieved successfully',
            $course,
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, string $id)
    {
        $course = Course::findorFail($id);
        $course->update($request->validated());
        return response()->api(
            'Course updated successfully',
            $course,
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::findorFail($id);
        $course->delete();
        return response()->api(
            'Course deleted successfully',
            null,
            200
        );
    }
}
