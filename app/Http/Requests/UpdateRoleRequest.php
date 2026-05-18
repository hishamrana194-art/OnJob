<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // policy enforced in controller
    }

    public function rules(): array
    {
        $roleId = $this->route('role');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                $this->uniqueNameRule($roleId),
            ],
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

    private function uniqueNameRule(mixed $roleId): \Illuminate\Validation\Rules\Unique
    {
        $user = $this->user();
        $rule = Rule::unique('roles', 'name')
            ->ignore($roleId)
            ->where('guard_name', 'web');

        if ($user && $user->user_type === 'employer_user') {
            $rule->where('employer_id', $user->employer_id);
        } elseif ($user && $user->user_type === 'vendor_user') {
            $rule->where('vendor_id', $user->vendor_id);
        }

        return $rule;
    }
}
