<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonTracking;
use App\Models\Certificate;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Handles lesson progress tracking and certificate generation for mobile students.
 */
class MobileProgressController extends Controller
{
    use ApiResponse;

    // ── Show Lesson ───────────────────────────────────────────────────────────

    public function showLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user('sanctum') ?? auth('sanctum')->user() ?? $request->user();
        if (!$user) {
            return $this->unauthorized();
        }

        $courseId = $lesson->course_id ?? $lesson->courseSection?->course_id;

        // Verify student is enrolled in the course
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if (! $enrollment && ! ($lesson->is_preview ?? false)) {
            return $this->forbidden('You must be enrolled to access this lesson.');
        }

        $tracking = LessonTracking::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        return $this->success([
            'id'              => $lesson->id,
            'title'           => $lesson->title,
            'content'         => $lesson->content ?? null,
            'video_id'        => $lesson->getVideoId(),
            'video_url'       => $lesson->video_url ?? null,
            'video_path'      => $lesson->video_path ?? null,
            'duration'        => $lesson->duration ?? 0,
            'is_completed'    => $tracking?->completed_at !== null,
            'watch_position'  => $tracking?->watch_position ?? 0,
            'section_id'      => $lesson->section_id,
            'lesson_order'    => $lesson->lesson_order,
            'next_lesson_id'  => $lesson->next()?->id,
            'prev_lesson_id'  => $lesson->previous()?->id,
            'attachments'     => $lesson->attachments ?? [],
            'quizzes_count'   => $lesson->quizzes()->count(),
            'resources'       => $lesson->resources->map(fn ($r) => [
                'id'       => $r->id,
                'title'    => $r->title,
                'file_url' => $r->file ?? ($r->file_path ? asset('storage/' . $r->file_path) : null),
                'type'     => $r->file_type?->name ?? 'file',
            ])->toArray(),
        ]);
    }

    // ── Complete Lesson ───────────────────────────────────────────────────────

    public function completeLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'watch_duration_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $user     = $request->user('sanctum') ?? auth('sanctum')->user() ?? $request->user();
        $courseId = $lesson->course_id ?? $lesson->courseSection?->course_id;

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Mark lesson tracking as complete (idempotent)
        LessonTracking::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'course_id'     => $courseId,
                'completed_at'  => now(),
                'watch_position'=> $request->watch_duration_seconds ?? 0,
            ]
        );

        // Recalculate course progress
        $totalLessons    = Lesson::where('course_id', $courseId)->count();
        if ($totalLessons === 0) {
            $totalLessons = Lesson::whereHas('courseSection', fn ($q) => $q->where('course_id', $courseId))->count();
        }

        $completedCount  = LessonTracking::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->whereHas('lesson', fn ($q) => $q->where('course_id', $courseId))
            ->count();

        $progressPercent = $totalLessons > 0
            ? (int) round($completedCount / $totalLessons * 100)
            : 0;

        $isCourseComplete = $progressPercent >= 100;

        $enrollment->update([
            'progress'  => $progressPercent,
            'passed_at' => $isCourseComplete && ! $enrollment->passed_at ? now() : $enrollment->passed_at,
        ]);

        // Auto-generate certificate if course is now complete
        $certificate = null;
        if ($isCourseComplete) {
            $certificate = $this->ensureCertificate($user, $enrollment->course, $enrollment);
        }

        return $this->success([
            'lesson_id'              => $lesson->id,
            'is_completed'           => true,
            'course_progress_percent'=> $progressPercent,
            'is_course_complete'     => $isCourseComplete,
            'next_lesson_id'         => $lesson->next()?->id,
            'certificate'            => $certificate ? [
                'id'         => $certificate->id,
                'file_url'   => $certificate->file_url ?? null,
                'issued_at'  => $certificate->created_at->toISOString(),
            ] : null,
        ], 'Lesson marked as complete');
    }

    // ── Update Progress (watch position) ──────────────────────────────────────

    public function updateProgress(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'watch_position' => ['nullable', 'integer', 'min:0'],
            'watch_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $user     = $request->user('sanctum') ?? auth('sanctum')->user() ?? $request->user();
        $courseId = $lesson->course_id ?? $lesson->courseSection?->course_id;

        // Verify enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $tracking = LessonTracking::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'course_id'        => $courseId,
                'watch_position'   => $request->watch_position ?? 0,
                'watch_percentage' => $request->watch_percentage ?? 0,
            ]
        );

        if ($request->filled('watch_percentage') && $request->watch_percentage > 90) {
            if (!$tracking->completed_at) {
                // Call completeLesson logic manually to reuse certificate and progress calculations
                $req = new Request();
                $req->setUserResolver(fn() => $user);
                $this->completeLesson($req, $lesson);
                return $this->success(['is_completed' => true], 'Progress saved and lesson auto-completed');
            }
        }

        return $this->success(['is_completed' => $tracking->completed_at !== null], 'Progress saved');
    }

    // ── Get Certificate ───────────────────────────────────────────────────────

    public function getCertificate(Request $request, Course $course): JsonResponse
    {
        $user = $request->user('sanctum') ?? auth('sanctum')->user() ?? $request->user();
        if (!$user) {
            return $this->unauthorized();
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment) {
            return $this->notFound('You are not enrolled in this course.');
        }

        if (! $enrollment->passed_at) {
            return $this->error('You have not completed this course yet.', [
                'progress' => $enrollment->progress ?? 0,
            ], 422);
        }

        $certificate = $this->ensureCertificate($user, $course, $enrollment);

        return $this->success([
            'id'               => $certificate->id,
            'course_title'     => $course->title,
            'student_name'     => $user->name,
            'tutor_name'       => $course->tutor?->name ?? 'Unknown',
            'certificate_number' => $certificate->certificate_number ?? Str::upper(Str::random(12)),
            'issued_at'        => $certificate->created_at->toISOString(),
            'file_url'         => $certificate->file_url ?? null,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Retrieve an existing certificate or generate a new one.
     */
    private function ensureCertificate($user, $course, Enrollment $enrollment): Certificate
    {
        return Certificate::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'enrollment_id'       => $enrollment->id,
                'certificate_number'  => Str::upper(Str::random(12)),
                'score'               => $enrollment->score ?? 100,
                'issued_at'           => now(),
            ]
        );
    }
}
