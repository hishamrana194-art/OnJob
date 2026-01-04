<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterEmployerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public registration
    }

    public function rules(): array
    {
        return [
            // Company information
            'company.name' => 'required|string|max:255',
            'company.country_id' => 'required|exists:countries,id',
            'company.phone' => 'nullable|string|max:20',
            'company.email' => 'required|email|unique:employers,email',
            'company.website' => 'nullable|url|max:255',

            // Admin user information
            'admin.name' => 'required|string|max:255',
            'admin.email' => 'required|email|unique:users,email',
            'admin.phone' => 'nullable|string|max:20',
            'admin.password' => 'required|string|min:8|confirmed',

            // Package selection
            'package_id' => 'required|exists:packages,id',
            'billing_period' => 'required|in:monthly,yearly',

            // Terms acceptance
            'agree_to_terms' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'company.name.required' => 'Company name is required',
            'company.email.unique' => 'This company email is already registered',
            'admin.email.unique' => 'This email is already registered',
            'admin.password.confirmed' => 'Password confirmation does not match',
            'agree_to_terms.accepted' => 'You must agree to the terms and conditions',
        ];
    }
}

