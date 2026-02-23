<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
            'vendor' => $this->whenLoaded('vendor', function () {
                return new VendorResource($this->vendor);
            }),
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
            'title' => $this->title,
            'description' => $this->description
                ,
            'thumbnail_url' => $this->thumbnail_url,
            'level' => $this->level,
            'duration' => $this->duration,
            'status' => $this->status,
            'is_mandatory' => (bool) ($this->is_mandatory ?? false),
            'order' => $this->order,
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
            'modules_count' => $this->whenCounted('modules'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

