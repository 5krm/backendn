<?php

namespace App\Http\Controllers\Api\Mobile\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\LessonTracking;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tutor-only dashboard endpoints for the mobile app.
 *
 * Protected by `can:access-tutor-panel` middleware which checks
 * that the authenticated user has `is_tutor = true`.
 */
class MobileTutorDashboardController extends Controller
{
    use ApiResponse;

    // ── Dashboard Overview ────────────────────────────────────────────────────

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        $tutorCourseIds = Course::where('tutor_id', $user->id)->pluck('id');

        $totalCourses    = $tutorCourseIds->count();
        $totalStudents   = Enrollment::whereIn('course_id', $tutorCourseIds)
            ->distinct('user_id')
            ->count('user_id');
        $totalCompleted  = Enrollment::whereIn('course_id', $tutorCourseIds)
            ->whereNotNull('passed_at')
            ->count();

        $recentEnrollments = Enrollment::whereIn('course_id', $tutorCourseIds)
            ->with([
                'user:id,name,email',
                'course:id,title,slug',
            ])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn ($e) => [
                'student_name'  => $e->user?->name,
                'student_email' => $e->user?->email,
                'course_title'  => $e->course?->title,
                'enrolled_at'   => $e->created_at->toISOString(),
                'progress'      => $e->progress ?? 0,
            ]);

        // Monthly enrollment counts for the last 6 months
        $monthlyStats = Enrollment::whereIn('course_id', $tutorCourseIds)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return $this->success([
            'total_courses'       => $totalCourses,
            'total_students'      => $totalStudents,
            'completed_students'  => $totalCompleted,
            'recent_enrollments'  => $recentEnrollments,
            'monthly_enrollments' => $monthlyStats,
        ]);
    }

    // ── Tutor Courses ─────────────────────────────────────────────────────────

    public function courses(Request $request): JsonResponse
    {
        $courses = Course::where('tutor_id', $request->user()->id)
            ->with(['category:id,name', 'activePrice'])
            ->withCount(['students', 'lessons'])
            ->orderByDesc('created_at')
            ->get();

        return $this->success($courses->map(fn ($c) => [
            'id'              => $c->id,
            'slug'            => $c->slug,
            'title'           => $c->title,
            'cover_image'     => $c->cover_image,
            'status'          => $c->status?->value ?? $c->status,
            'level'           => $c->level?->value ?? $c->level,
            'is_free'         => (bool) $c->is_free,
            'price'           => $c->activePrice?->amount ?? 0,
            'students_count'  => $c->students_count,
            'lessons_count'   => $c->lessons_count,
            'average_rating'  => (float) ($c->average_rating ?? 0),
            'category_name'   => $c->category?->name ?? null,
            'created_at'      => $c->created_at->toISOString(),
        ]));
    }

    // ── Students for a Specific Course ────────────────────────────────────────

    public function students(Request $request, Course $course): JsonResponse
    {
        // Gate: only the course owner
        if ($course->tutor_id !== $request->user()->id) {
            return $this->forbidden('You do not own this course.');
        }

        $enrollments = Enrollment::where('course_id', $course->id)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalLessons = $course->lessons()->count();

        return $this->paginated(
            $enrollments,
            fn ($items) => collect($items)->map(fn ($e) => [
                'enrollment_id'    => $e->id,
                'student_id'       => $e->user_id,
                'student_name'     => $e->user?->name,
                'student_email'    => $e->user?->email,
                'enrolled_at'      => $e->created_at->toISOString(),
                'progress_percent' => $e->progress ?? 0,
                'completed_at'     => $e->passed_at?->toISOString(),
                'total_lessons'    => $totalLessons,
            ])->all()
        );
    }

    // ── Earnings ──────────────────────────────────────────────────────────────

    public function earnings(Request $request): JsonResponse
    {
        // Placeholder — real earnings require Stripe payment records
        return $this->success([
            'total_earnings'   => 0,
            'this_month'       => 0,
            'currency'         => 'USD',
            'note'             => 'Earnings tracking coming soon',
        ]);
    }
}
