<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Http\Traits\Paginatable;
use App\Models\Module;
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

    // Get modules by course
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
}
