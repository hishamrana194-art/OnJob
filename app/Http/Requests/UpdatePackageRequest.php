<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|array',
            'name.en' => 'sometimes|string|max:255',
            'name.ar' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:employer,vendor,provider',
            'description' => 'sometimes|array',
            'description.en' => 'sometimes|string',
            'description.ar' => 'sometimes|string',
            'price_monthly' => 'sometimes|numeric',
            'price_yearly' => 'sometimes|numeric',
            'max_employees' => 'sometimes|integer',
            'max_content' => 'sometimes|integer',
            'features' => 'sometimes|array',
            'features.en' => 'sometimes|string',
            'features.ar' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
