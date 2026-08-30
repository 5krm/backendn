<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorStudentsApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $search = $request->query('search');

        $courseIds = Course::where('tutor_id', $user->id)->pluck('id');

        $enrollmentsQuery = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->latest();

        if ($search) {
            $enrollmentsQuery->whereHas('user', fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $enrollments = $enrollmentsQuery->get();

        $students = $enrollments->map(fn ($e) => [
            'id' => $e->user?->id ?? $e->user_id,
            'name' => $e->user?->name ?? 'Student',
            'email' => $e->user?->email ?? '',
            'course' => $e->course?->title ?? 'Course',
            'progress' => (int) ($e->progress ?? 0),
            'score' => ($e->score ?? 0).'%',
            'last_active' => $e->updated_at?->diffForHumans() ?? 'Unknown',
            'status' => $this->resolveStudentStatus($e),
            'graduated' => ! is_null($e->passed_at),
        ]);

        $totalActive = $enrollments->filter(fn ($e) => is_null($e->passed_at) && ($e->progress ?? 0) >= 30)->count();
        $graduated = $enrollments->filter(fn ($e) => ! is_null($e->passed_at))->count();
        $atRisk = $enrollments->filter(fn ($e) => is_null($e->passed_at) && ($e->progress ?? 0) < 30)->count();
        $avgScore = $enrollments->whereNotNull('score')->avg('score');

        return $this->success([
            'stats' => [
                'active_students' => $totalActive,
                'graduated' => $graduated,
                'avg_score' => $avgScore ? round($avgScore, 1).'%' : '0%',
                'at_risk_count' => $atRisk,
            ],
            'students' => $students->values(),
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $courseIds = Course::where('tutor_id', $user->id)->pluck('id');

        $student = User::find($id);
        if (! $student) {
            return $this->notFound('Student not found.');
        }

        $enrollments = Enrollment::where('user_id', $id)
            ->whereIn('course_id', $courseIds)
            ->with('course')
            ->get();

        if ($enrollments->isEmpty()) {
            return $this->notFound('Student is not enrolled in any of your courses.');
        }

        $certificates = Certificate::where('user_id', $id)
            ->where('tutor_id', $user->id)
            ->with('course')
            ->get()
            ->map(fn ($c) => [
                'title' => $c->course?->title ?? 'Course',
                'cert_id' => $c->certificate_number ?? ('C-'.$c->id),
                'date' => $c->issued_at?->format('M d, Y') ?? now()->format('M d, Y'),
            ]);

        $enrolledCourses = $enrollments->map(fn ($e) => [
            'title' => $e->course?->title ?? 'Course',
            'progress' => (int) ($e->progress ?? 0),
            'lessons' => 'In progress',
            'score' => ($e->score ?? 0).'%',
        ]);

        $avgScore = $enrollments->whereNotNull('score')->avg('score');
        $overallProgress = $enrollments->avg('progress') ?? 0;

        return $this->success([
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'enrolled_date' => $enrollments->first()?->created_at?->format('M d, Y') ?? 'Unknown',
            'overall_progress' => (int) $overallProgress,
            'avg_score' => (int) ($avgScore ?? 0),
            'status' => $this->resolveStudentStatus($enrollments->first()),
            'enrolled_courses' => $enrolledCourses,
            'quiz_history' => [],
            'certificates_earned' => $certificates,
            'tutor_notes' => '',
        ]);
    }

    public function addNote(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'note' => ['required', 'string'],
        ]);

        return $this->success(['note' => $data['note']], 'Tutor note saved successfully');
    }

    private function resolveStudentStatus(?Enrollment $enrollment): string
    {
        if (! $enrollment) {
            return 'active';
        }

        if (! is_null($enrollment->passed_at)) {
            return 'graduated';
        }

        if (($enrollment->progress ?? 0) < 30) {
            return 'at_risk';
        }

        return 'active';
    }
}
