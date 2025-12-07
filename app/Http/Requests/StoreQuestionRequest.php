<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'quiz_id' => 'required|exists:quizzes,id',
            'type' => 'required|in:multiple_choice,single_choice,complete,matching',
            'question_text' => 'required|array',
            'question_text.en' => 'required|string',
            'question_text.ar' => 'required|string',
            'explanation' => 'nullable|array',
            'explanation.en' => 'nullable|string',
            'explanation.ar' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'points' => 'integer|min:1',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];

        // Add rules based on question type
        if (in_array($this->type, ['multiple_choice', 'single_choice'])) {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.option_text'] = 'required|array';
            $rules['options.*.option_text.en'] = 'required|string';
            $rules['options.*.option_text.ar'] = 'required|string';
            $rules['options.*.is_correct'] = 'boolean';
            $rules['options.*.order'] = 'integer';
        }

        if ($this->type === 'matching') {
            $rules['matches'] = 'required|array|min:2';
            $rules['matches.*.left_side'] = 'required|array';
            $rules['matches.*.left_side.en'] = 'required|string';
            $rules['matches.*.left_side.ar'] = 'required|string';
            $rules['matches.*.right_side'] = 'required|array';
            $rules['matches.*.right_side.en'] = 'required|string';
            $rules['matches.*.right_side.ar'] = 'required|string';
            $rules['matches.*.order'] = 'integer';
        }

        if ($this->type === 'complete') {
            $rules['blanks'] = 'required|array|min:1';
            $rules['blanks.*.blank_position'] = 'required|integer|min:1';
            $rules['blanks.*.correct_answer'] = 'required|array';
            $rules['blanks.*.correct_answer.en'] = 'required|string';
            $rules['blanks.*.correct_answer.ar'] = 'required|string';
            $rules['blanks.*.acceptable_answers'] = 'nullable|array';
            $rules['blanks.*.acceptable_answers.en'] = 'nullable|string';
            $rules['blanks.*.acceptable_answers.ar'] = 'nullable|string';
            $rules['blanks.*.order'] = 'integer';
        }

        return $rules;
    }
}

