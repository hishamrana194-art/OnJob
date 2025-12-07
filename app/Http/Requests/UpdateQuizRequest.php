<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'module_item_id' => 'nullable|exists:module_items,id',
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'sometimes|array',
            'title.en' => 'sometimes|string|max:255',
            'title.ar' => 'sometimes|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'integer|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'show_correct_answers' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}

