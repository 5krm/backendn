<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Http\Controllers\Controller;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonTracking;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Serves quizzes and grades submissions for mobile students.
 *
 * Quiz questions are returned WITHOUT the correct_answer field
 * to prevent cheating on the client side.
 */
class MobileQuizController extends Controller
{
    use ApiResponse;

    // ── Show Quiz ─────────────────────────────────────────────────────────────

    public function show(Request $request, $quizId): JsonResponse
    {
        // quizId is actually the lesson ID
        $lesson = Lesson::with(['quizzes.quizOptions', 'section'])->find($quizId);

        if (! $lesson) {
            return $this->notFound('Quiz/Lesson not found.');
        }

        // Verify enrollment
        $courseId = $lesson->section?->course_id;
        if ($courseId) {
            $enrolled = Enrollment::where('user_id', $request->user()->id)
                ->where('course_id', $courseId)
                ->exists();

            if (! $enrolled) {
                return $this->forbidden('Enroll in the course to access this quiz.');
            }
        }

        $tracking = LessonTracking::firstOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            ['course_id' => $courseId]
        );

        $retakeLimit = $lesson->retake_limit ?? 3;
        $cooldown = $lesson->cooldown_minutes ?? 1440; // 24 hours

        // Check if exceeded max attempts and wait time
        $attempts = $tracking->attempts_count ?? 0;
        $lastAttempt = $tracking->last_attempt_at;

        $canRetake = true;
        $cooldownRemaining = 0;

        if ($attempts >= $retakeLimit && $lastAttempt) {
            $lastAttemptTime = Carbon::parse($lastAttempt);
            if (now()->diffInMinutes($lastAttemptTime) < $cooldown) {
                $canRetake = false;
                $cooldownRemaining = $cooldown - now()->diffInMinutes($lastAttemptTime);
            } else {
                // Cooldown passed, reset attempts
                $tracking->attempts_count = 0;
                $tracking->save();
                $attempts = 0;
            }
        }

        // Randomize questions (Task 119)
        $questions = $lesson->quizzes->shuffle()->map(fn ($q) => [
            'id' => $q->id,
            'text' => $q->question,
            'type' => 'single',
            // Randomize options (Task 119)
            'options' => $q->quizOptions->shuffle()->map(fn ($o) => [
                'id' => $o->id,
                'text' => $o->value,
                // correct_answer intentionally omitted
            ])->values(),
        ])->values();

        return $this->success([
            'id' => $lesson->id,
            'title' => $lesson->title,
            'description' => $lesson->content ?? null,
            'pass_percent' => $lesson->pass_percent ?? 100,
            'time_limit_min' => null,
            'questions' => $questions,
            'retake_limit' => $retakeLimit,
            'attempts' => $attempts,
            'can_retake' => $canRetake,
            'cooldown_remaining_mins' => $cooldownRemaining,
        ]);
    }

    // ── Submit Quiz ───────────────────────────────────────────────────────────

    public function submit(Request $request, $quizId): JsonResponse
    {
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.question_id' => ['required', 'integer'],
            'answers.*.option_id' => ['required', 'integer'],
        ]);

        $lesson = Lesson::with(['quizzes.quizOptions'])->find($quizId);

        if (! $lesson) {
            return $this->notFound('Quiz/Lesson not found.');
        }

        $tracking = LessonTracking::firstOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            ['course_id' => $lesson->section?->course_id]
        );

        $retakeLimit = $lesson->retake_limit ?? 3;
        $cooldown = $lesson->cooldown_minutes ?? 1440;

        $attempts = $tracking->attempts_count ?? 0;
        $lastAttempt = $tracking->last_attempt_at;

        if ($attempts >= $retakeLimit && $lastAttempt) {
            $lastAttemptTime = Carbon::parse($lastAttempt);
            if (now()->diffInMinutes($lastAttemptTime) < $cooldown) {
                return $this->error('Retake limit exceeded. Please wait cooldown period.', null, 403);
            } else {
                $attempts = 0; // Cooldown passed
            }
        }

        $questions = $lesson->quizzes;
        $totalQ = $questions->count();

        if ($totalQ === 0) {
            return $this->error('This quiz has no questions.', null, 422);
        }

        $correct = 0;
        $results = [];

        foreach ($questions as $question) {
            $submittedAnswer = collect($request->answers)
                ->firstWhere('question_id', $question->id);

            $submittedOptionId = $submittedAnswer['option_id'] ?? null;

            $correctOption = $question->quizOptions->firstWhere('is_correct', true);
            $isCorrect = $correctOption && $submittedOptionId === $correctOption->id;

            if ($isCorrect) {
                $correct++;
            }

            $results[] = [
                'question_id' => $question->id,
                'your_option_id' => $submittedOptionId,
                'correct_option_id' => $correctOption?->id,
                'is_correct' => $isCorrect,
            ];
        }

        $score = (int) round($correct / $totalQ * 100);
        $passPercent = $lesson->pass_percent ?? 100;
        $passed = $score >= $passPercent;

        // Update tracking logic (Task 116)
        $tracking->attempts_count = $attempts + 1;
        $tracking->last_attempt_at = now();

        if ($passed && ! $tracking->completed_at) {
            $tracking->completed_at = now();
        }
        $tracking->save();

        if ($passed) {
            // Also complete the lesson via the same logic or emit an event
            // But we already updated $tracking->completed_at
            // Recalculate enrollment progress:
            $courseId = $lesson->section?->course_id;
            $user = $request->user();
            if ($courseId) {
                $enrollment = Enrollment::where('user_id', $user->id)
                    ->where('course_id', $courseId)
                    ->first();

                if ($enrollment) {
                    $totalLessons = Lesson::whereHas('section', fn ($q) => $q->where('course_id', $courseId))->count();
                    $completedCount = LessonTracking::where('user_id', $user->id)
                        ->whereNotNull('completed_at')
                        ->whereHas('lesson.section', fn ($q) => $q->where('course_id', $courseId))
                        ->count();

                    $progressPercent = $totalLessons > 0
                        ? (int) round($completedCount / $totalLessons * 100)
                        : 0;

                    $isCourseComplete = $progressPercent >= 100;

                    $enrollment->update([
                        'progress' => $progressPercent,
                        'passed_at' => $isCourseComplete && ! $enrollment->passed_at ? now() : $enrollment->passed_at,
                    ]);
                }
            }
        }

        return $this->success([
            'quiz_id' => $lesson->id,
            'score' => $score,
            'correct' => $correct,
            'total' => $totalQ,
            'passed' => $passed,
            'pass_percent' => $passPercent,
            'results' => $results,
            'attempts_left' => max(0, $retakeLimit - $tracking->attempts_count),
        ], $passed ? 'Congratulations! You passed.' : 'Quiz completed. Keep trying!');
    }
}
