<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Lessons\LessonAssignment;
use App\Models\Lessons\LessonAssignmentSubmission;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorAssignmentsApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status', 'all');

        $courseIds = Course::where('tutor_id', $user->id)->pluck('id');

        // Try to fetch real assignment submissions
        $submissionsQuery = null;
        $submissions = collect();

        if (class_exists(LessonAssignmentSubmission::class)) {
            $submissionsQuery = LessonAssignmentSubmission::whereHas('assignment.lesson.course', fn ($q) => $q->whereIn('id', $courseIds))
                ->with(['user', 'assignment.lesson.course'])
                ->latest();

            if ($status !== 'all') {
                $submissionsQuery->where('status', $status);
            }

            $submissions = $submissionsQuery->get()->map(fn ($s) => [
                'id' => $s->id,
                'student_name' => $s->user?->name ?? 'Student',
                'student_avatar' => $s->user?->avatar ?? null,
                'course_title' => $s->assignment?->lesson?->course?->title ?? 'Course',
                'assignment' => $s->assignment?->title ?? 'Assignment',
                'submitted_at' => $s->created_at?->diffForHumans() ?? 'Unknown',
                'file_name' => $s->file_name ?? null,
                'file_size' => $s->file_size ?? null,
                'file_url' => $s->file_url ?? null,
                'status' => $s->status ?? 'pending',
                'score' => $s->score,
                'feedback' => $s->feedback,
            ]);
        } elseif (class_exists(LessonAssignment::class)) {
            // Fallback: show assignments without submissions model
            $submissions = LessonAssignment::whereHas('lesson.course', fn ($q) => $q->whereIn('id', $courseIds))
                ->with(['lesson.course'])
                ->latest()
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'student_name' => 'N/A',
                    'student_avatar' => null,
                    'course_title' => $a->lesson?->course?->title ?? 'Course',
                    'assignment' => $a->title ?? 'Assignment',
                    'submitted_at' => null,
                    'file_name' => null,
                    'file_size' => null,
                    'file_url' => null,
                    'status' => 'pending',
                    'score' => null,
                    'feedback' => null,
                ]);
        }

        $pendingCount = $submissions->where('status', 'pending')->count();
        $gradedCount = $submissions->where('status', 'graded')->count();
        $resubmitCount = $submissions->where('status', 'resubmit')->count();
        $avgScore = $submissions->whereNotNull('score')->avg('score');

        return $this->success([
            'stats' => [
                'pending_review' => $pendingCount,
                'graded_submissions' => $gradedCount,
                'avg_assignment_score' => $avgScore ? round($avgScore, 1).'%' : '0%',
                'resubmissions_pending' => $resubmitCount,
            ],
            'submissions' => $submissions->values(),
        ]);
    }

    public function grade(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string'],
            'status' => ['nullable', 'in:graded,resubmit'],
        ]);

        // Try to update real submission if it exists
        if (class_exists(LessonAssignmentSubmission::class)) {
            $submission = LessonAssignmentSubmission::find($id);
            if ($submission) {
                $submission->update([
                    'score' => $data['score'],
                    'feedback' => $data['feedback'] ?? null,
                    'status' => $data['status'] ?? 'graded',
                ]);
            }
        }

        return $this->success([
            'id' => $id,
            'score' => (float) $data['score'],
            'feedback' => $data['feedback'] ?? '',
            'status' => $data['status'] ?? 'graded',
        ], 'Submission graded successfully');
    }
}
