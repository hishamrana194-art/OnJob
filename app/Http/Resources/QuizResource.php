<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'module_item_id' => $this->module_item_id,
            'course_id' => $this->course_id,
            'course' => $this->whenLoaded('course', function () {
                return [
                    'id' => $this->course->id,
                    'title' => [
                        'en' => $this->course->getTranslation('title', 'en'),
                        'ar' => $this->course->getTranslation('title', 'ar'),
                    ],
                ];
            }),
            'title' => [
                'en' => $this->getTranslation('title', 'en'),
                'ar' => $this->getTranslation('title', 'ar'),
            ],
            'description' => [
                'en' => $this->getTranslation('description', 'en'),
                'ar' => $this->getTranslation('description', 'ar'),
            ],
            'time_limit' => $this->time_limit,
            'passing_score' => $this->passing_score,
            'max_attempts' => $this->max_attempts,
            'shuffle_questions' => $this->shuffle_questions,
            'show_correct_answers' => $this->show_correct_answers,
            'is_active' => $this->is_active,
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'questions_count' => $this->whenCounted('questions'),
            'total_points' => $this->when($this->relationLoaded('questions'), function () {
                return $this->questions->sum('points');
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

