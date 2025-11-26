<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
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
            'employer_id' => 'sometimes|exists:employers,id',
            'package_id' => 'sometimes|exists:packages,id',
            'vendor_id' => 'sometimes|exists:vendors,id',
            'status' => 'sometimes|in:active,expired,suspended',
            'seats_limit' => 'sometimes|integer',
            'billing_period' => 'sometimes|in:monthly,yearly',
            'price_per_period' => 'sometimes|numeric',
            'started_at' => 'sometimes|date',
            'ends_at' => 'sometimes|date',
        ];
    }
}
