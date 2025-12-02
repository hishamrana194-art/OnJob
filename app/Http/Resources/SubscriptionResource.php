<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscribable_id' => $this->subscribable_id,
            'subscribable_type' => $this->subscribable_type,
            'subscribable' => $this->whenLoaded('subscribable', function () {
                if ($this->subscribable instanceof \App\Models\Employer) {
                    return new EmployerResource($this->subscribable);
                } elseif ($this->subscribable instanceof \App\Models\Vendor) {
                    return new VendorResource($this->subscribable);
                }
                return null;
            }),
            'package_id' => $this->package_id,
            'package' => $this->whenLoaded('package', function () {
                return new PackageResource($this->package);
            }),
            'status' => $this->status,
            'seats_limit' => (int) $this->seats_limit,
            'billing_period' => $this->billing_period,
            'price_per_period' => (float) $this->price_per_period,
            'started_at' => $this->started_at,
            'ends_at' => $this->ends_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

