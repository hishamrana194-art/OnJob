<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Flags ────────────────────────────────────────────────────────
            'is_global' => 'sometimes|boolean',

            // ── Ownership ────────────────────────────────────────────────────
            // Only validate the cross-field rule when at least one of the two
            // fields is actually being updated.
            'vendor_id' => [
                'sometimes',
                'nullable',
                'exists:vendors,id',
                function ($attribute, $value, $fail) {
                    // Resolve effective is_global: incoming value OR existing DB value.
                    // $this->route('course') returns the raw ID (no implicit model binding),
                    // so we look up the course ourselves when is_global is not in the payload.
                    if ($this->has('is_global')) {
                        $isGlobal = filter_var($this->input('is_global'), FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $courseId = $this->route('course');
                        $existing = $courseId ? \App\Models\Course::find($courseId) : null;
                        $isGlobal = (bool) ($existing?->is_global ?? false);
                    }

                    if ($isGlobal && $value !== null) {
                        $fail('A global course must not be assigned to a vendor (vendor_id must be null).');
                    }

                    if (!$isGlobal && $value === null) {
                        $fail('A non-global course requires a vendor_id.');
                    }
                },
            ],

            // ── Content ──────────────────────────────────────────────────────
            'category_id'    => 'sometimes|exists:categories,id',
            'title'          => 'sometimes|array',
            'title.en'       => 'sometimes|string|max:255',
            'title.ar'       => 'sometimes|string|max:255',
            'description'    => 'sometimes|array',
            'description.en' => 'sometimes|string',
            'description.ar' => 'sometimes|string',
            'thumbnail_url'  => 'nullable|string|max:255',
            'level'          => 'sometimes|in:beginner,intermediate,advanced',
            'duration'       => 'sometimes|integer|min:0',
            'status'         => 'sometimes|in:draft,published,archived',
            'is_mandatory'   => 'sometimes|boolean',
            'order'          => 'sometimes|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'vendor_id.exists' => 'The selected vendor does not exist.',
        ];
    }
}
