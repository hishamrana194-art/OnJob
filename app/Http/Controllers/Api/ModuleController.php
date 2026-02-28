<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Http\Traits\Paginatable;
use App\Models\Module;
use App\Models\ModuleItem;
use App\Models\UserModuleItemProgress;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    use Paginatable;

    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $modules = Module::with(['course'])
            ->withCount('items')
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Modules retrieved successfully',
            $this->paginationResponse($modules, ModuleResource::class),
            200
        );
    }

    public function store(StoreModuleRequest $request)
    {
        $module = Module::create($request->validated());

        return response()->api(
            'Module created successfully',
            new ModuleResource($module->load('course')),
            201
        );
    }

    public function show(string $id)
    {
        $module = Module::with(['course', 'items.quiz'])
            ->withCount('items')
            ->findOrFail($id);

        return response()->api(
            'Module retrieved successfully',
            new ModuleResource($module),
            200
        );
    }

    public function update(UpdateModuleRequest $request, string $id)
    {
        $module = Module::findOrFail($id);
        $module->update($request->validated());

        return response()->api(
            'Module updated successfully',
            new ModuleResource($module->load('course')),
            200
        );
    }

    public function destroy(string $id)
    {
        $module = Module::findOrFail($id);
        $module->delete();

        return response()->api(
            'Module deleted successfully',
            null,
            200
        );
    }

    // Get modules by course (paginated, nested)
    public function byCourse(Request $request, string $courseId)
    {
        $params = $this->getPaginationParams($request);

        $modules = Module::where('course_id', $courseId)
            ->with(['items'])
            ->withCount('items')
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Modules retrieved successfully',
            $this->paginationResponse($modules, ModuleResource::class),
            200
        );
    }

    // Get flat lesson list for a course (mobile-friendly)
    public function lessonsByCourse(string $courseId)
    {
        $locale = app()->getLocale() ?: 'ar';
        $user = auth()->user();

        // Fetch completed item IDs for this user in one query
        $completedIds = $user
            ? UserModuleItemProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->pluck('module_item_id')
                ->toArray()
            : [];

        $items = ModuleItem::whereHas('module', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->with(['module', 'quiz'])
            ->orderBy('order')
            ->get()
            ->map(function ($item) use ($locale, $completedIds) {
                $fallback = $locale === 'ar' ? 'en' : 'ar';
                $titleArr = is_array($item->title) ? $item->title : [];
                $title = $titleArr[$locale] ?? $titleArr[$fallback] ?? $item->title ?? '';

                // Determine video_url and pdf_url based on type
                $videoUrl = null;
                $pdfUrl = null;
                if ($item->type === 'video') {
                    $videoUrl = $item->file_url;
                } elseif ($item->type === 'pdf' || $item->type === 'document') {
                    $pdfUrl = $item->file_url;
                }

                $isCompleted = in_array($item->id, $completedIds);

                return [
                    'id'            => $item->id,
                    'title'         => $titleArr ?: ['ar' => $title, 'en' => $title],
                    'type'          => $item->type ?? 'video',
                    'duration'      => $item->duration ?? 0,
                    'duration_type' => 'minutes',
                    'status'        => $isCompleted ? 'completed' : 'in_progress',
                    'quiz_id'       => $item->quiz?->id,
                    'video_url'     => $videoUrl,
                    'pdf_url'       => $pdfUrl,
                    'module_id'     => $item->module_id,
                    'order'         => $item->order,
                ];
            });

        return response()->api('Lessons retrieved successfully', $items);
    }
}
