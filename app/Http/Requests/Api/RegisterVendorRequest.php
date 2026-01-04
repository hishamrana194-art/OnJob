<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public registration
    }

    public function rules(): array
    {
        return [
            // Vendor information
            'vendor.name' => 'required|string|max:255',
            'vendor.country_id' => 'required|exists:countries,id',
            'vendor.phone' => 'nullable|string|max:20',
            'vendor.email' => 'required|email|unique:vendors,email',
            'vendor.website' => 'nullable|url|max:255',
            'vendor.description' => 'nullable|string|max:1000',

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
            'vendor.name.required' => 'Vendor name is required',
            'vendor.email.unique' => 'This vendor email is already registered',
            'admin.email.unique' => 'This email is already registered',
            'admin.password.confirmed' => 'Password confirmation does not match',
            'agree_to_terms.accepted' => 'You must agree to the terms and conditions',
        ];
    }
}

