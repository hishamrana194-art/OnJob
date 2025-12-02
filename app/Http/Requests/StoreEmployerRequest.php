<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployerRequest extends FormRequest
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
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'email' => 'required|email|unique:employers',
            'country_id' => 'required|exists:countries,id',
            'phone_number' => 'required|string|regex:/^[0-9]+$/|max:20',
            'company' => 'required|array',
            'company.en' => 'required|string|max:255',
            'company.ar' => 'required|string|max:255',
            'address' => 'required|array',
            'address.en' => 'required|string|max:500',
            'address.ar' => 'required|string|max:500',
            'gender' => 'required|in:male,female',
            'status' => 'required|boolean',
        ];
    }
}
