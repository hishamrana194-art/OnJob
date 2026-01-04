<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployerWithAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only Admin model (super admin) can access
        return auth()->user() instanceof \App\Models\Admin;
    }

    public function rules(): array
    {
        return [
            // Employer info
            'employer_name' => 'required|array',
            'employer_name.en' => 'required|string|max:255',
            'employer_name.ar' => 'nullable|string|max:255',
            'employer_email' => 'required|email|unique:employers,email',
            'employer_phone' => 'nullable|string|max:20',
            'employer_country_id' => 'required|exists:countries,id',
            'employer_company' => 'nullable|array',
            'employer_company.en' => 'nullable|string|max:255',
            'employer_company.ar' => 'nullable|string|max:255',
            'employer_address' => 'nullable|array',
            'employer_address.en' => 'nullable|string|max:500',
            'employer_address.ar' => 'nullable|string|max:500',

            // Admin user info
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_phone' => 'nullable|string|max:20',
            'admin_password' => 'required|string|min:8',

            // Subscription info
            'package_id' => 'required|exists:packages,id',
            'billing_period' => 'required|in:monthly,yearly',
            'subscription_starts_at' => 'required|date',
            'subscription_ends_at' => 'required|date|after:subscription_starts_at',
        ];
    }

    public function messages(): array
    {
        return [
            'employer_name.en.required' => 'Employer name in English is required',
            'employer_email.unique' => 'This employer email is already registered',
            'admin_email.unique' => 'This admin email is already registered',
            'package_id.exists' => 'Selected package does not exist',
        ];
    }
}

