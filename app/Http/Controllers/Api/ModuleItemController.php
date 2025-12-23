<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreModuleItemRequest;
use App\Http\Requests\UpdateModuleItemRequest;
use App\Http\Resources\ModuleItemResource;
use App\Http\Traits\Paginatable;
use App\Models\ModuleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleItemController extends Controller
{
    use Paginatable;

    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $items = ModuleItem::with(['module', 'quiz'])
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Module items retrieved successfully',
            $this->paginationResponse($items, ModuleItemResource::class),
            200
        );
    }

    public function store(StoreModuleItemRequest $request)
    {
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $folder = $data['type'] === 'video' ? 'videos' : 'documents';
            $path = $file->store("module-items/{$folder}", 'public');
            $data['file_url'] = Storage::url($path);
        }

        // Parse JSON fields if they come as strings
        if (isset($data['title']) && is_string($data['title'])) {
            $data['title'] = json_decode($data['title'], true);
        }
        if (isset($data['content']) && is_string($data['content'])) {
            $data['content'] = json_decode($data['content'], true);
        }

        $item = ModuleItem::create($data);

        return response()->api(
            'Module item created successfully',
            new ModuleItemResource($item->load('module')),
            201
        );
    }

    public function show(string $id)
    {
        $item = ModuleItem::with(['module', 'quiz.questions'])
            ->findOrFail($id);

        return response()->api(
            'Module item retrieved successfully',
            new ModuleItemResource($item),
            200
        );
    }

    public function update(UpdateModuleItemRequest $request, string $id)
    {
        $item = ModuleItem::findOrFail($id);
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($item->file_url) {
                $oldPath = str_replace('/storage/', '', $item->file_url);
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('file');
            $folder = ($data['type'] ?? $item->type) === 'video' ? 'videos' : 'documents';
            $path = $file->store("module-items/{$folder}", 'public');
            $data['file_url'] = Storage::url($path);
        }

        // Parse JSON fields if they come as strings
        if (isset($data['title']) && is_string($data['title'])) {
            $data['title'] = json_decode($data['title'], true);
        }
        if (isset($data['content']) && is_string($data['content'])) {
            $data['content'] = json_decode($data['content'], true);
        }

        $item->update($data);

        return response()->api(
            'Module item updated successfully',
            new ModuleItemResource($item->load('module')),
            200
        );
    }

    public function destroy(string $id)
    {
        $item = ModuleItem::findOrFail($id);
        $item->delete();

        return response()->api(
            'Module item deleted successfully',
            null,
            200
        );
    }

    // Get items by module
    public function byModule(Request $request, string $moduleId)
    {
        $params = $this->getPaginationParams($request);

        $items = ModuleItem::where('module_id', $moduleId)
            ->with(['quiz'])
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Module items retrieved successfully',
            $this->paginationResponse($items, ModuleItemResource::class),
            200
        );
    }
}

