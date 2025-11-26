<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
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
        'subscribable_id' => 'required|integer',
        'subscribable_type' => 'required|string|in:App\\Models\\Employer,App\\Models\\Vendor',
        'package_id' => 'required|exists:packages,id',
        'status' => 'required|in:active,expired,suspended',
        'seats_limit' => 'required|integer',
        'billing_period' => 'required|in:monthly,yearly',
        'price_per_period' => 'required|numeric',
        'started_at' => 'required|date',
        'ends_at' => 'required|date',
    ];
}

}
