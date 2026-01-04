<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Employer roles
     */
    protected array $employerRoles = ['employer_admin', 'hr_manager', 'department_manager', 'team_leader', 'employee'];

    /**
     * Vendor roles
     */
    protected array $vendorRoles = ['vendor_admin', 'vendor_editor'];

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
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'user_type' => 'required|in:employer_user,vendor_user,training_provider',

            'employer_id' => [
                'nullable',
                'exists:employers,id',
                function ($attribute, $value, $fail) {
                    if ($this->input('user_type') === 'employer_user' && !$value) {
                        $fail('Employer is required for employer users.');
                    }
                    if ($this->input('user_type') !== 'employer_user' && $value) {
                        $fail('Employer should only be set for employer users.');
                    }
                },
            ],

            'vendor_id' => [
                'nullable',
                'exists:vendors,id',
                function ($attribute, $value, $fail) {
                    if ($this->input('user_type') === 'vendor_user' && !$value) {
                        $fail('Vendor is required for vendor users.');
                    }
                    if ($this->input('user_type') !== 'vendor_user' && $value) {
                        $fail('Vendor should only be set for vendor users.');
                    }
                },
            ],

            'role' => [
                'required',
                'in:employer_admin,hr_manager,department_manager,team_leader,employee,vendor_admin,vendor_editor,training_provider',
                function ($attribute, $value, $fail) {
                    $userType = $this->input('user_type');

                    if ($userType === 'employer_user' && !in_array($value, $this->employerRoles)) {
                        $fail('Invalid role for employer user. Allowed: ' . implode(', ', $this->employerRoles));
                    }
                    if ($userType === 'vendor_user' && !in_array($value, $this->vendorRoles)) {
                        $fail('Invalid role for vendor user. Allowed: ' . implode(', ', $this->vendorRoles));
                    }
                    if ($userType === 'training_provider' && $value !== 'training_provider') {
                        $fail('Training provider users must have the training_provider role.');
                    }
                },
            ],

            'points' => 'nullable|integer|min:0',
            'locale' => 'nullable|in:en,ar',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'user_type.required' => 'User type is required.',
            'user_type.in' => 'Invalid user type. Must be employer_user, vendor_user, or training_provider.',
            'role.required' => 'Role is required.',
            'role.in' => 'Invalid role selected.',
        ];
    }
}

