<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Http\Traits\Paginatable;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    use Paginatable;

    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $questions = Question::with(['options', 'matches', 'blanks'])
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Questions retrieved successfully',
            $this->paginationResponse($questions, QuestionResource::class),
            200
        );
    }

    public function store(StoreQuestionRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validated();

            // Create question
            $question = Question::create([
                'quiz_id' => $validated['quiz_id'],
                'type' => $validated['type'],
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'image' => $validated['image'] ?? null,
                'points' => $validated['points'] ?? 1,
                'order' => $validated['order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Create related data based on type
            $this->createQuestionRelations($question, $validated);

            return response()->api(
                'Question created successfully',
                new QuestionResource($question->load(['options', 'matches', 'blanks'])),
                201
            );
        });
    }

    public function show(string $id)
    {
        $question = Question::with(['options', 'matches', 'blanks', 'quiz'])
            ->findOrFail($id);

        return response()->api(
            'Question retrieved successfully',
            new QuestionResource($question),
            200
        );
    }

    public function update(UpdateQuestionRequest $request, string $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $question = Question::findOrFail($id);
            $validated = $request->validated();

            $question->update($validated);

            // Update related data
            $this->updateQuestionRelations($question, $validated);

            return response()->api(
                'Question updated successfully',
                new QuestionResource($question->load(['options', 'matches', 'blanks'])),
                200
            );
        });
    }

    public function destroy(string $id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->api(
            'Question deleted successfully',
            null,
            200
        );
    }

    // Get questions by quiz
    public function byQuiz(Request $request, string $quizId)
    {
        $params = $this->getPaginationParams($request);

        $questions = Question::where('quiz_id', $quizId)
            ->with(['options', 'matches', 'blanks'])
            ->orderBy('order')
            ->paginate($params['per_page']);

        return response()->api(
            'Questions retrieved successfully',
            $this->paginationResponse($questions, QuestionResource::class),
            200
        );
    }

    private function createQuestionRelations(Question $question, array $validated): void
    {
        if (in_array($question->type, ['multiple_choice', 'single_choice']) && isset($validated['options'])) {
            foreach ($validated['options'] as $option) {
                $question->options()->create($option);
            }
        }

        if ($question->type === 'matching' && isset($validated['matches'])) {
            foreach ($validated['matches'] as $match) {
                $question->matches()->create($match);
            }
        }

        if ($question->type === 'complete' && isset($validated['blanks'])) {
            foreach ($validated['blanks'] as $blank) {
                $question->blanks()->create($blank);
            }
        }
    }

    private function updateQuestionRelations(Question $question, array $validated): void
    {
        if (in_array($question->type, ['multiple_choice', 'single_choice']) && isset($validated['options'])) {
            $this->syncOptions($question, $validated['options']);
        }

        if ($question->type === 'matching' && isset($validated['matches'])) {
            $this->syncMatches($question, $validated['matches']);
        }

        if ($question->type === 'complete' && isset($validated['blanks'])) {
            $this->syncBlanks($question, $validated['blanks']);
        }
    }

    private function syncOptions(Question $question, array $options): void
    {
        $existingIds = [];

        foreach ($options as $option) {
            if (isset($option['id'])) {
                $existingOption = $question->options()->find($option['id']);
                if ($existingOption) {
                    $existingOption->update($option);
                    $existingIds[] = $option['id'];
                }
            } else {
                $newOption = $question->options()->create($option);
                $existingIds[] = $newOption->id;
            }
        }

        // Delete removed options
        $question->options()->whereNotIn('id', $existingIds)->delete();
    }

    private function syncMatches(Question $question, array $matches): void
    {
        $existingIds = [];

        foreach ($matches as $match) {
            if (isset($match['id'])) {
                $existingMatch = $question->matches()->find($match['id']);
                if ($existingMatch) {
                    $existingMatch->update($match);
                    $existingIds[] = $match['id'];
                }
            } else {
                $newMatch = $question->matches()->create($match);
                $existingIds[] = $newMatch->id;
            }
        }

        $question->matches()->whereNotIn('id', $existingIds)->delete();
    }

    private function syncBlanks(Question $question, array $blanks): void
    {
        $existingIds = [];

        foreach ($blanks as $blank) {
            if (isset($blank['id'])) {
                $existingBlank = $question->blanks()->find($blank['id']);
                if ($existingBlank) {
                    $existingBlank->update($blank);
                    $existingIds[] = $blank['id'];
                }
            } else {
                $newBlank = $question->blanks()->create($blank);
                $existingIds[] = $newBlank->id;
            }
        }

        $question->blanks()->whereNotIn('id', $existingIds)->delete();
    }
}

