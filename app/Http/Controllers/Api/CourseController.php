<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Http\Traits\Paginatable;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use Paginatable;

    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $courses = Course::with(['vendor', 'category'])
            ->withCount('modules')
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Courses retrieved successfully',
            $this->paginationResponse($courses, CourseResource::class),
            200
        );
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());

        return response()->api(
            'Course created successfully',
            new CourseResource($course->load(['vendor', 'category'])),
            201
        );
    }

    public function show(string $id)
    {
        $course = Course::with(['vendor', 'category', 'modules.items'])
            ->withCount('modules')
            ->findOrFail($id);

        return response()->api(
            'Course retrieved successfully',
            new CourseResource($course),
            200
        );
    }

    public function update(UpdateCourseRequest $request, string $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->validated());

        return response()->api(
            'Course updated successfully',
            new CourseResource($course->load(['vendor', 'category'])),
            200
        );
    }

    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->api(
            'Course deleted successfully',
            null,
            200
        );
    }
}
