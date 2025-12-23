<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => 'required|exists:vendors,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.ar' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.ar' => 'required|string',
            'thumbnail_url' => 'nullable|string|max:255',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration' => 'integer|min:0',
            'status' => 'in:draft,published,archived',
            'order' => 'integer',
        ];
    }
}

