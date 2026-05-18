<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Http\Traits\Paginatable;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    use Paginatable;

    // ─── Read ────────────────────────────────────────────────────────────────

    /**
     * Paginated course listing scoped to the caller's visibility.
     *
     * Course::scopeForUser() holds the single source of truth for which courses
     * each actor may see. No if/else chains here.
     *
     * | Caller              | Scope applied                             |
     * |---------------------|-------------------------------------------|
     * | employer_user       | visibleToEmployer (is_global = true only) |
     * | vendor_user         | visibleToVendor (own vendor, not global)   |
     * | Admin / super_admin | no filter — all courses                   |
     * | unauthenticated     | no filter — all courses                   |
     */
    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $courses = Course::forUser(auth()->user())
            ->with(['vendor', 'category'])
            ->withCount('modules')
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Courses retrieved successfully',
            $this->paginationResponse($courses, CourseResource::class),
            200
        );
    }

    /**
     * Fetch a single course by ID.
     *
     * Uses CoursePolicy::view() to enforce the same visibility rules as the
     * listing query, but applied to a direct ID lookup. Returns 404 (not 403)
     * so callers cannot infer the existence of courses they cannot see.
     */
    public function show(string $id)
    {
        $course = Course::with(['vendor', 'category', 'modules.items'])
            ->withCount('modules')
            ->findOrFail($id);

        // For authenticated users: check policy. For guests: allow (public catalog).
        $caller = auth()->user();
        if ($caller !== null && Gate::denies('view', $course)) {
            // Return 404 instead of 403 to avoid leaking the existence of
            // courses the caller is not permitted to see.
            return response()->api('Course not found', null, 404);
        }

        return response()->api(
            'Course retrieved successfully',
            new CourseResource($course),
            200
        );
    }

    // ─── Write ───────────────────────────────────────────────────────────────

    /**
     * Create a new course.
     *
     * Policy rules (CoursePolicy::create):
     *   - employer_user   → denied
     *   - vendor_user     → allowed, but vendor_id is locked to their own
     *   - course_admin / Admin → allowed; may also set is_global = true
     *
     * Permission rules (CoursePolicy::markGlobal):
     *   - Setting is_global = true additionally requires courses.mark_global
     */
    public function store(StoreCourseRequest $request)
    {
        $this->authorize('create', Course::class);

        $caller = auth()->user();
        $data   = $request->validated();

        if ($caller instanceof User && $caller->user_type === 'vendor_user') {
            // Lock vendor users to their own vendor — ignore any payload values.
            $data['vendor_id'] = (int) $caller->vendor_id;
            $data['is_global'] = false;
        } elseif (!empty($data['is_global'])) {
            // Creating a global course requires the explicit permission.
            $this->authorize('markGlobal', Course::class);
            // Enforce constraint: global courses must never carry a vendor_id.
            $data['vendor_id'] = null;
        }

        $course = Course::create($data);

        return response()->api(
            'Course created successfully',
            new CourseResource($course->load(['vendor', 'category'])),
            201
        );
    }

    /**
     * Update an existing course.
     *
     * Policy rules (CoursePolicy::update):
     *   - employer_user   → denied
     *   - vendor_user     → only their own vendor's courses
     *   - course_admin / Admin → any course
     *
     * Permission rules (CoursePolicy::markGlobal):
     *   - Flipping is_global from false → true requires courses.mark_global
     *   - Already-global course staying global does NOT re-check (idempotent)
     */
    public function update(UpdateCourseRequest $request, string $id)
    {
        $course = Course::findOrFail($id);
        $this->authorize('update', $course);

        $caller = auth()->user();
        $data   = $request->validated();

        if ($caller instanceof User && $caller->user_type === 'vendor_user') {
            // Prevent vendor users from detaching their own vendor or flipping global.
            $data['vendor_id'] = (int) $caller->vendor_id;
            $data['is_global'] = false;
        } elseif (isset($data['is_global']) && $data['is_global']) {
            // Any attempt to set is_global = true requires the permission,
            // whether the course was previously global or not.
            $this->authorize('markGlobal', Course::class);
            // Enforce constraint: global courses must never carry a vendor_id.
            $data['vendor_id'] = null;
        }

        $course->update($data);

        return response()->api(
            'Course updated successfully',
            new CourseResource($course->load(['vendor', 'category'])),
            200
        );
    }

    /**
     * Delete a course.
     *
     * Policy rules (CoursePolicy::delete — delegates to update):
     *   - employer_user   → denied
     *   - vendor_user     → only their own vendor's courses
     *   - course_admin / Admin → any course
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $this->authorize('delete', $course);

        $course->delete();

        return response()->api('Course deleted successfully', null, 200);
    }
}
