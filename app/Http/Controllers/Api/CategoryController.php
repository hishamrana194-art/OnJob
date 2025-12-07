<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Traits\Paginatable;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use Paginatable;

    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $categories = Category::with(['parent', 'children'])
            ->withCount('courses')
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Categories retrieved successfully',
            $this->paginationResponse($categories, CategoryResource::class),
            200
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->api(
            'Category created successfully',
            new CategoryResource($category),
            201
        );
    }

    public function show(string $id)
    {
        $category = Category::with(['parent', 'children', 'courses'])
            ->withCount('courses')
            ->findOrFail($id);

        return response()->api(
            'Category retrieved successfully',
            new CategoryResource($category),
            200
        );
    }

    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        return response()->api(
            'Category updated successfully',
            new CategoryResource($category),
            200
        );
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->api(
            'Category deleted successfully',
            null,
            200
        );
    }
}

