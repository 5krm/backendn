<?php

namespace App\Services;

use App\Models\Courses\Course;
use App\Models\Quizzes\Quiz;
use Illuminate\Support\Collection;

class FinalExamService
{
    /**
     * Generate final exam questions from course lesson quizzes
     */
    public function generateFinalExam(Course $course, int $questionCount = 20): Collection
    {
        // Get all quizzes from course lessons
        $lessonQuizzes = Quiz::whereHas('lesson', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->with(['quizOptions', 'lesson'])
            ->get();

        // If we don't have enough questions, return all available
        if ($lessonQuizzes->count() <= $questionCount) {
            return $lessonQuizzes->shuffle();
        }

        // Randomly select questions ensuring we get from different lessons if possible
        return $this->selectDiverseQuestions($lessonQuizzes, $questionCount);
    }

    /**
     * Select questions trying to get variety from different lessons
     */
    private function selectDiverseQuestions(Collection $quizzes, int $count): Collection
    {
        $selectedQuizzes = collect();
        $quizzesByLesson = $quizzes->groupBy('lesson_id');

        // First pass: get one question from each lesson
        foreach ($quizzesByLesson as $lessonQuizzes) {
            if ($selectedQuizzes->count() >= $count) {
                break;
            }
            $selectedQuizzes->push($lessonQuizzes->random());
        }

        // Second pass: fill remaining slots randomly from all questions
        $remainingQuizzes = $quizzes->diff($selectedQuizzes);
        $remainingCount = $count - $selectedQuizzes->count();

        if ($remainingCount > 0 && $remainingQuizzes->isNotEmpty()) {
            $additionalQuizzes = $remainingQuizzes->random(
                min($remainingCount, $remainingQuizzes->count())
            );
            $selectedQuizzes = $selectedQuizzes->merge($additionalQuizzes);
        }

        return $selectedQuizzes->shuffle();
    }

    /**
     * Get total available questions for a course
     */
    public function getAvailableQuestionCount(Course $course): int
    {
        return Quiz::whereHas('lesson', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })->count();
    }

    /**
     * Check if course has enough questions for final exam
     */
    public function hasEnoughQuestions(Course $course, int $minimumQuestions = 1): bool
    {
        return $this->getAvailableQuestionCount($course) >= $minimumQuestions;
    }

    /**
     * Get final exam configuration for a course
     */
    public function getFinalExamConfig(Course $course): array
    {
        $availableQuestions = $this->getAvailableQuestionCount($course);

        return [
            'available_questions' => $availableQuestions,
            'exam_question_count' => min(20, $availableQuestions), // Max 20 questions or all available
            'passing_score' => 80, // 80% to pass
            'time_limit_minutes' => null, // No time limit
            'has_enough_questions' => $availableQuestions >= 1,
        ];
    }
}
