<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => [
                'en' => $this->getTranslation('name', 'en'),
                'ar' => $this->getTranslation('name', 'ar'),
            ],
            'description' => [
                'en' => $this->getTranslation('description', 'en'),
                'ar' => $this->getTranslation('description', 'ar'),
            ],
            'slug' => $this->slug,
            'image' => $this->image,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', function () {
                return new CategoryResource($this->parent);
            }),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'is_active' => $this->is_active,
            'order' => $this->order,
            'courses_count' => $this->whenCounted('courses'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

