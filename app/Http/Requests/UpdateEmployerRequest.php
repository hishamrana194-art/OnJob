<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployerRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'country_id' => 'sometimes|exists:countries,id',
            'phone_number' => 'required|string|regex:/^[0-9]+$/|max:20',
            'company' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'gender' => 'sometimes|in:male,female',
            'status' => 'sometimes|boolean',
        ];
    }
}
