<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:employer,vendor,provider',
            'description' => 'required|string',
            'price_monthly' => 'required|numeric',
            'price_yearly' => 'required|numeric',
            'max_employees' => 'required|integer',
            'max_content' => 'required|integer',
            'features' => 'required|string',
            'is_active' => 'required|boolean',
        ];
    }
}
