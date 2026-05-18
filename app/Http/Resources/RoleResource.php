<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'guard_name'        => $this->guard_name,
            'is_system'         => (bool) $this->is_system,
            'employer_id'       => $this->employer_id,
            'vendor_id'         => $this->vendor_id,
            'permissions'       => $this->whenLoaded('permissions', fn () => $this->permissions->pluck('name')),
            'permissions_count' => $this->whenLoaded('permissions', fn () => $this->permissions->count()),
            'created_at'        => $this->created_at?->toDateTimeString(),
        ];
    }
}
