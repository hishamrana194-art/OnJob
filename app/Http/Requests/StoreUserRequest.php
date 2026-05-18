<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /** Roles that an employer_admin may assign to new users they create. */
    protected array $subordinateEmployerRoles = [
        'hr_manager',
        'department_manager',
        'team_leader',
        'employee',
    ];

    /** Full set of valid employer roles (used by super-admin / Admin callers). */
    protected array $employerRoles = [
        'employer_admin',
        'hr_manager',
        'department_manager',
        'team_leader',
        'employee',
    ];

    protected array $vendorRoles = ['vendor_admin', 'vendor_editor'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Determine if the authenticated caller is an employer_admin.
     * An employer_admin is any User with user_type = employer_user.
     * (The controller will further enforce they can only create employees,
     * but we lock down validation here too.)
     */
    protected function callerIsEmployerUser(): bool
    {
        $caller = $this->user();
        return $caller instanceof User && $caller->user_type === 'employer_user';
    }

    public function rules(): array
    {
        $callerIsEmployerUser = $this->callerIsEmployerUser();

        // ── user_type ─────────────────────────────────────────────────────────
        // When an employer_admin is creating a user, user_type is always
        // 'employer_user' — the controller enforces this, so we make the
        // field optional and default it in validation.
        $userTypeRule = $callerIsEmployerUser
            ? 'sometimes|in:employer_user'          // optional; if sent must be correct
            : 'required|in:employer_user,vendor_user,training_provider';

        // ── employer_id / vendor_id ───────────────────────────────────────────
        // Employer admins do not need to supply employer_id or vendor_id;
        // the controller auto-assigns from the authenticated user's context.
        if ($callerIsEmployerUser) {
            $employerIdRules = ['nullable', 'exists:employers,id'];
            $vendorIdRules   = ['nullable', 'exists:vendors,id'];
        } else {
            $employerIdRules = [
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
            ];
            $vendorIdRules = [
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
            ];
        }

        // ── role ──────────────────────────────────────────────────────────────
        // employer_admin may only assign subordinate roles — they cannot create
        // another employer_admin or any vendor/training-provider role.
        if ($callerIsEmployerUser) {
            $subordinate = $this->subordinateEmployerRoles;
            $roleRules = [
                'required',
                'in:' . implode(',', $subordinate),
                function ($attribute, $value, $fail) use ($subordinate) {
                    if (!in_array($value, $subordinate, true)) {
                        $fail('As an employer admin you may only assign: ' . implode(', ', $subordinate) . '.');
                    }
                },
            ];
        } else {
            $roleRules = [
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
            ];
        }

        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email',
            'password'  => ['required', 'confirmed', Password::min(8)],
            'phone'     => 'nullable|string|max:20',
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_type' => $userTypeRule,
            'employer_id' => $employerIdRules,
            'vendor_id'   => $vendorIdRules,
            'role'        => $roleRules,
            'points'    => 'nullable|integer|min:0',
            'locale'    => 'nullable|in:en,ar',
            'is_active' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'      => 'This email is already registered.',
            'user_type.required'=> 'User type is required.',
            'user_type.in'      => 'Invalid user type. Must be employer_user, vendor_user, or training_provider.',
            'role.required'     => 'Role is required.',
            'role.in'           => 'Invalid role selected.',
        ];
    }
}
