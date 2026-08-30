<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\LessonComment\Comment;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutorDashboardApiController extends Controller
{
    use ApiResponse;

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $tutorId = $user->id;

        $courses = Course::where('tutor_id', $tutorId)->get();
        $courseIds = $courses->pluck('id');

        $totalCourses = $courses->count();
        $publishedCourses = $courses->where('status', CourseStatus::published)->count();
        $draftCourses = $courses->where('status', CourseStatus::draft)->count();

        $totalStudents = Enrollment::whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');

        $certificatesCount = Certificate::where('tutor_id', $tutorId)->count();

        // Revenue calculation
        $totalRevenue = (float) (DB::table('courses')
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->where('courses.tutor_id', $tutorId)
            ->where('courses.is_free', false)
            ->sum('courses.price') ?: 0);

        // Completion calculation
        $totalEnrollmentsCount = Enrollment::whereIn('course_id', $courseIds)->count();
        $passedEnrollmentsCount = Enrollment::whereIn('course_id', $courseIds)->whereNotNull('passed_at')->count();
        $avgCompletion = $totalEnrollmentsCount > 0
            ? round(($passedEnrollmentsCount / $totalEnrollmentsCount) * 100, 1)
            : 0;

        // Enrollment trend (Last 8 months) — real data grouped by month
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
        $enrollmentTrend = collect($months)->map(fn ($m, $i) => [
            'm' => $m,
            'v' => Enrollment::whereIn('course_id', $courseIds)
                ->whereMonth('created_at', $i + 1)
                ->whereYear('created_at', now()->year)
                ->count(),
        ])->toArray();

        // Completion funnel — real data
        $started = max($totalEnrollmentsCount, 0);
        $completionFunnel = [
            ['name' => 'Enrolled', 'v' => $started],
            ['name' => 'Active',   'v' => Enrollment::whereIn('course_id', $courseIds)->where('progress', '>', 0)->count()],
            ['name' => 'Halfway',  'v' => Enrollment::whereIn('course_id', $courseIds)->where('progress', '>=', 50)->count()],
            ['name' => 'Completed', 'v' => $passedEnrollmentsCount],
        ];

        // Recent comments — real data
        $recentComments = Comment::whereHas('lesson.course', fn ($q) => $q->where('tutor_id', $tutorId))
            ->with(['user', 'lesson.course'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'student_name' => $c->user?->name ?? 'Student',
                'course_title' => $c->lesson?->course?->title ?? 'Course',
                'message' => $c->content,
                'created_at' => $c->created_at?->diffForHumans() ?? 'Just now',
            ]);

        // At-risk students — real data
        $atRiskStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('progress', '<', 30)
            ->whereNull('passed_at')
            ->with(['user', 'course'])
            ->limit(5)
            ->get()
            ->map(fn ($e) => [
                'name' => $e->user?->name ?? 'Student',
                'course' => $e->course?->title ?? 'Course',
                'progress' => (int) ($e->progress ?? 0),
            ]);

        // Top performing courses — real data
        $topCourses = $courses->map(function ($c) {
            $studentsCount = $c->students()->count();

            return [
                'id' => $c->id,
                'title' => $c->title,
                'students' => $studentsCount,
                'completion' => 0,
                'rating' => $c->average_rating ? (float) $c->average_rating : 0,
                'revenue' => '$'.number_format(($c->price ?: 0) * $studentsCount),
            ];
        })->take(5);

        return $this->success([
            'stats' => [
                'total_courses' => $totalCourses,
                'published_courses' => $publishedCourses,
                'draft_courses' => $draftCourses,
                'total_students' => $totalStudents,
                'certificates_issued' => $certificatesCount,
                'avg_completion' => $avgCompletion.'%',
                'satisfaction_rate' => '0%',
                'total_revenue' => '$'.number_format($totalRevenue, 2),
                'available_balance' => '$0.00',
                'pending_clearance' => '$0.00',
            ],
            'enrollment_trends' => $enrollmentTrend,
            'completion_funnel' => $completionFunnel,
            'upcoming_milestones' => [],
            'recent_notifications' => [],
            'recent_comments' => $recentComments,
            'at_risk_students' => $atRiskStudents,
            'courses_need_attention' => [],
            'draft_content' => [],
            'top_courses' => $topCourses,
        ]);
    }
}
