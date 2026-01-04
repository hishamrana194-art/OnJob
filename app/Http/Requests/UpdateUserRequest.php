<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|max:255|unique:users,email,{$userId}",
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'user_type' => 'sometimes|required|in:employer_user,vendor_user,training_provider',

            'employer_id' => [
                'nullable',
                'exists:employers,id',
                function ($attribute, $value, $fail) {
                    $userType = $this->input('user_type');
                    if ($userType === 'employer_user' && !$value) {
                        $fail('Employer is required for employer users.');
                    }
                    if ($userType && $userType !== 'employer_user' && $value) {
                        $fail('Employer should only be set for employer users.');
                    }
                },
            ],

            'vendor_id' => [
                'nullable',
                'exists:vendors,id',
                function ($attribute, $value, $fail) {
                    $userType = $this->input('user_type');
                    if ($userType === 'vendor_user' && !$value) {
                        $fail('Vendor is required for vendor users.');
                    }
                    if ($userType && $userType !== 'vendor_user' && $value) {
                        $fail('Vendor should only be set for vendor users.');
                    }
                },
            ],

            'role' => [
                'sometimes',
                'required',
                'in:employer_admin,hr_manager,department_manager,team_leader,employee,vendor_admin,vendor_editor,training_provider',
                function ($attribute, $value, $fail) {
                    $userType = $this->input('user_type');
                    if (!$userType) {
                        return; // Skip validation if user_type not being updated
                    }

                    if ($userType === 'employer_user' && !in_array($value, $this->employerRoles)) {
                        $fail('Invalid role for employer user.');
                    }
                    if ($userType === 'vendor_user' && !in_array($value, $this->vendorRoles)) {
                        $fail('Invalid role for vendor user.');
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
            'user_type.in' => 'Invalid user type.',
            'role.in' => 'Invalid role selected.',
        ];
    }
}

