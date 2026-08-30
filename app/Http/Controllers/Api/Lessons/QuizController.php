<?php

namespace App\Http\Controllers\Api\Lessons;

use App\Data\Lessons\QuizData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Lessons\QuizResource;
use App\Models\Lessons\Lesson;
use App\Models\Quizzes\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct()
    {
        QuizResource::withoutWrapping();
    }

    public function index(Lesson $lesson)
    {
        $data = $lesson->quizzes()->with('quizOptions')->get();

        return QuizResource::collection($data);
    }

    public function store(Lesson $lesson, QuizData $data)
    {
        $quiz = $lesson->quizzes()->create([
            'question' => $data->question,
            'order' => $lesson->quizzes()->count() + 1,
        ]);

        $quiz->quizOptions()->createMany($data->options->toArray());

        return QuizResource::make($quiz->load('quizOptions'));
    }

    public function reorder(Lesson $lesson, Request $request)
    {
        $data = $request->validate([
            'data' => ['required', 'array'],
            'data.*.id' => ['required', 'integer'],
            'data.*.index' => ['required', 'integer'],
        ]);

        $lesson->load(['quizzes.quizOptions']);

        $data = collect($data['data'])->keyBy('id')->toArray();
        $lesson
            ->quizzes
            ->each(fn (Quiz $quiz) => $quiz->update(['order' => $data[$quiz->id]['index']]));

        $result = $lesson->quizzes->sortBy('order')->values();

        return QuizResource::collection($result);
    }

    public function update(int $lesson, Quiz $quiz, QuizData $data)
    {
        $quiz->update($data->except('options')->toArray());

        $quiz->quizOptions()->delete();
        $quiz->quizOptions()->createMany($data->options->toArray());

        return QuizResource::make($quiz->load('quizOptions'));
    }

    public function delete(Lesson $lesson, Quiz $quiz)
    {
        $quiz->quizOptions()->delete();
        $quiz->delete();

        return response()->noContent();
    }
}
