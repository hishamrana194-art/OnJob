<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateVendorWithAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only Admin model (super admin) can access
        return auth()->user() instanceof \App\Models\Admin;
    }

    public function rules(): array
    {
        return [
            // Vendor info
            'vendor_name' => 'required|array',
            'vendor_name.en' => 'required|string|max:255',
            'vendor_name.ar' => 'nullable|string|max:255',
            'vendor_email' => 'required|email|unique:vendors,email',
            'vendor_phone' => 'nullable|string|max:20',
            'vendor_country_id' => 'required|exists:countries,id',
            'vendor_description' => 'nullable|array',
            'vendor_description.en' => 'nullable|string|max:1000',
            'vendor_description.ar' => 'nullable|string|max:1000',
            'vendor_website' => 'nullable|url|max:255',

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
            'vendor_name.en.required' => 'Vendor name in English is required',
            'vendor_email.unique' => 'This vendor email is already registered',
            'admin_email.unique' => 'This admin email is already registered',
            'package_id.exists' => 'Selected package does not exist',
        ];
    }
}

