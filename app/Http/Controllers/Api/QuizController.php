<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Http\Traits\Paginatable;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    use Paginatable;

    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $quizzes = Quiz::with(['course', 'moduleItem'])
            ->withCount('questions')
            ->paginate($params['per_page']);

        return response()->api(
            'Quizzes retrieved successfully',
            $this->paginationResponse($quizzes, QuizResource::class),
            200
        );
    }

    public function store(StoreQuizRequest $request)
    {
        $quiz = Quiz::create($request->validated());

        return response()->api(
            'Quiz created successfully',
            new QuizResource($quiz->load('course')),
            201
        );
    }

    public function show(string $id)
    {
        $quiz = Quiz::with(['course', 'moduleItem', 'questions.options', 'questions.matches', 'questions.blanks'])
            ->withCount('questions')
            ->findOrFail($id);

        return response()->api(
            'Quiz retrieved successfully',
            new QuizResource($quiz),
            200
        );
    }

    public function update(UpdateQuizRequest $request, string $id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->update($request->validated());

        return response()->api(
            'Quiz updated successfully',
            new QuizResource($quiz->load('course')),
            200
        );
    }

    public function destroy(string $id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return response()->api(
            'Quiz deleted successfully',
            null,
            200
        );
    }

    // Get quizzes by course
    public function byCourse(Request $request, string $courseId)
    {
        $params = $this->getPaginationParams($request);

        $quizzes = Quiz::where('course_id', $courseId)
            ->with(['questions'])
            ->withCount('questions')
            ->paginate($params['per_page']);

        return response()->api(
            'Quizzes retrieved successfully',
            $this->paginationResponse($quizzes, QuizResource::class),
            200
        );
    }
}

