<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'module_id' => 'required|exists:modules,id',
            'type' => 'required|in:video,document,text,quiz',
            'title' => 'required',
            'content' => 'nullable',
            'file' => 'nullable|file|max:102400', // 100MB max
            'file_url' => 'nullable|string|max:255',
            'duration' => 'integer|min:0',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}

