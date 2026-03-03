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
            // NOTE:
            // The mobile UI currently supports only choice questions.
            // So we filter to keep: single_choice, multiple_choice.
            'questions' => $this->when(
                $this->relationLoaded('questions'),
                function () {
                    $questions = $this->questions
                        ->whereIn('type', ['single_choice', 'multiple_choice'])
                        ->values();
                    return QuestionResource::collection($questions);
                },
                []
            ),
            'questions_count' => $this->when(
                $this->relationLoaded('questions'),
                function () {
                    return $this->questions
                        ->whereIn('type', ['single_choice', 'multiple_choice'])
                        ->count();
                },
                0
            ),
            'total_points' => $this->when(
                $this->relationLoaded('questions'),
                function () {
                    return $this->questions
                        ->whereIn('type', ['single_choice', 'multiple_choice'])
                        ->sum('points');
                },
                0
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

