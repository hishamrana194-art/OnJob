<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Fine-grained authorization is handled by RolePolicy via $this->authorize() in the controller.
        return true;
    }

    public function rules(): array
    {
        return [
            // Role names must be globally unique across the roles table.
            // The application prevents collisions at DB level via a composite index;
            // this rule keeps error messages user-friendly.
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                $this->uniqueNameRule(),
            ],
            'guard_name'    => ['sometimes', 'string', 'in:web,api,sanctum'],
            'permissions'   => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Role name may only contain lowercase letters, numbers, and underscores.',
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Build a unique rule scoped to the caller's tenant so that two different
     * employers/vendors can independently create roles with the same name, while
     * still preventing duplicates within the same tenant.
     */
    private function uniqueNameRule(): \Illuminate\Validation\Rules\Unique
    {
        $user = $this->user();
        $rule = Rule::unique('roles', 'name')->where('guard_name', $this->input('guard_name', 'web'));

        if ($user && $user->user_type === 'employer_user') {
            $rule->where('employer_id', $user->employer_id);
        } elseif ($user && $user->user_type === 'vendor_user') {
            $rule->where('vendor_id', $user->vendor_id);
        }

        return $rule;
    }
}
