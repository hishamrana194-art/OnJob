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
            'title' => [
                'en' => $this->getTranslation('title', 'en'),
                'ar' => $this->getTranslation('title', 'ar'),
            ],
            'description' => [
                'en' => $this->getTranslation('description', 'en'),
                'ar' => $this->getTranslation('description', 'ar'),
            ],
            'thumbnail_url' => $this->thumbnail_url,
            'level' => $this->level,
            'duration' => $this->duration,
            'status' => $this->status,
            'order' => $this->order,
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
            'modules_count' => $this->whenCounted('modules'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

