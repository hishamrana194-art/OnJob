<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'quiz_id' => 'sometimes|exists:quizzes,id',
            'type' => 'sometimes|in:multiple_choice,single_choice,complete,matching',
            'question_text' => 'sometimes|array',
            'question_text.en' => 'sometimes|string',
            'question_text.ar' => 'sometimes|string',
            'explanation' => 'nullable|array',
            'explanation.en' => 'nullable|string',
            'explanation.ar' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'points' => 'integer|min:1',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];

        $type = $this->type ?? $this->route('question')?->type;

        if (in_array($type, ['multiple_choice', 'single_choice'])) {
            $rules['options'] = 'sometimes|array|min:2';
            $rules['options.*.id'] = 'nullable|exists:question_options,id';
            $rules['options.*.option_text'] = 'required|array';
            $rules['options.*.option_text.en'] = 'required|string';
            $rules['options.*.option_text.ar'] = 'required|string';
            $rules['options.*.is_correct'] = 'boolean';
            $rules['options.*.order'] = 'integer';
        }

        if ($type === 'matching') {
            $rules['matches'] = 'sometimes|array|min:2';
            $rules['matches.*.id'] = 'nullable|exists:question_matches,id';
            $rules['matches.*.left_side'] = 'required|array';
            $rules['matches.*.left_side.en'] = 'required|string';
            $rules['matches.*.left_side.ar'] = 'required|string';
            $rules['matches.*.right_side'] = 'required|array';
            $rules['matches.*.right_side.en'] = 'required|string';
            $rules['matches.*.right_side.ar'] = 'required|string';
            $rules['matches.*.order'] = 'integer';
        }

        if ($type === 'complete') {
            $rules['blanks'] = 'sometimes|array|min:1';
            $rules['blanks.*.id'] = 'nullable|exists:question_blanks,id';
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

