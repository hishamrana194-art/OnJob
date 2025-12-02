<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployerResource extends JsonResource
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
            'name' => [
                'en' => $this->getTranslation('name', 'en'),
                'ar' => $this->getTranslation('name', 'ar'),
            ],
            'email' => $this->email,
            'country_id' => $this->country_id,
            'country' => $this->whenLoaded('country', function () {
                return new CountryResource($this->country);
            }),
            'phone_number' => $this->phone_number,
            'company' => [
                'en' => $this->getTranslation('company', 'en'),
                'ar' => $this->getTranslation('company', 'ar'),
            ],
            'address' => [
                'en' => $this->getTranslation('address', 'en'),
                'ar' => $this->getTranslation('address', 'ar'),
            ],
            'gender' => $this->gender,
            'status' => (bool) $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

