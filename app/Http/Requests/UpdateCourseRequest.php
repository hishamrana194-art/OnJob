<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => 'sometimes|exists:vendors,id',
            'category_id' => 'sometimes|exists:categories,id',
            'title' => 'sometimes|array',
            'title.en' => 'sometimes|string|max:255',
            'title.ar' => 'sometimes|string|max:255',
            'description' => 'sometimes|array',
            'description.en' => 'sometimes|string',
            'description.ar' => 'sometimes|string',
            'thumbnail_url' => 'nullable|string|max:255',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'duration' => 'integer|min:0',
            'status' => 'in:draft,published,archived',
            'order' => 'integer',
        ];
    }
}

