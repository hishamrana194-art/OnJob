<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => 'sometimes|exists:courses,id',
            'title' => 'sometimes|array',
            'title.en' => 'sometimes|string|max:255',
            'title.ar' => 'sometimes|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}

