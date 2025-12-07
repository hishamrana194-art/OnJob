<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'module' => $this->whenLoaded('module', function () {
                return [
                    'id' => $this->module->id,
                    'title' => [
                        'en' => $this->module->getTranslation('title', 'en'),
                        'ar' => $this->module->getTranslation('title', 'ar'),
                    ],
                ];
            }),
            'type' => $this->type,
            'title' => [
                'en' => $this->getTranslation('title', 'en'),
                'ar' => $this->getTranslation('title', 'ar'),
            ],
            'content' => [
                'en' => $this->getTranslation('content', 'en'),
                'ar' => $this->getTranslation('content', 'ar'),
            ],
            'file_url' => $this->file_url,
            'duration' => $this->duration,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'quiz' => $this->whenLoaded('quiz', function () {
                return new QuizResource($this->quiz);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

