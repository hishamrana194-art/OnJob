<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Flags ────────────────────────────────────────────────────────
            // is_global must be supplied first so the vendor_id closure can read it.
            'is_global' => 'sometimes|boolean',

            // ── Ownership ────────────────────────────────────────────────────
            // vendor_id is nullable for global courses; required otherwise.
            'vendor_id' => [
                'nullable',
                'exists:vendors,id',
                function ($attribute, $value, $fail) {
                    $isGlobal = filter_var($this->input('is_global', false), FILTER_VALIDATE_BOOLEAN);

                    if ($isGlobal && $value !== null) {
                        $fail('A global course must not be assigned to a vendor (vendor_id must be null).');
                    }

                    if (!$isGlobal && $value === null) {
                        $fail('A non-global course requires a vendor_id.');
                    }
                },
            ],

            // ── Content ──────────────────────────────────────────────────────
            'category_id'   => 'required|exists:categories,id',
            'title'         => 'required|array',
            'title.en'      => 'required|string|max:255',
            'title.ar'      => 'required|string|max:255',
            'description'   => 'required|array',
            'description.en'=> 'required|string',
            'description.ar'=> 'required|string',
            'thumbnail_url' => 'nullable|string|max:255',
            'level'         => 'required|in:beginner,intermediate,advanced',
            'duration'      => 'sometimes|integer|min:0',
            'status'        => 'sometimes|in:draft,published,archived',
            'is_mandatory'  => 'sometimes|boolean',
            'order'         => 'sometimes|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'vendor_id.exists' => 'The selected vendor does not exist.',
        ];
    }
}
