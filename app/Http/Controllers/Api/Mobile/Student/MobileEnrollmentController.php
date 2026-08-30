<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendCourseEmailJob;
use App\Models\Courses\Course;
use App\Models\Courses\CourseMail;
use App\Models\Courses\Enrollment;
use App\Models\Invoice;
use App\Models\Lessons\LessonTracking;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Manages student enrollments and in-app purchases for the mobile & desktop app.
 *
 * Supports free-course enrollment and in-app purchase (IAP) unlocking across
 * Android (Google Play), iOS & macOS (Apple StoreKit), and Windows/Web platforms.
 */
class MobileEnrollmentController extends Controller
{
    use ApiResponse;

    // ── Enroll (Free or Zero-Price Courses) ────────────────────────────────────

    public function enroll(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();

        // Only published courses
        $isPublished = ($course->status instanceof CourseStatus)
            ? $course->status->value === 'published'
            : $course->status === 'published';

        if (! $isPublished) {
            return $this->error('This course is not available for enrollment.', null, 422);
        }

        // Already enrolled?
        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $this->error('You are already enrolled in this course.', null, 409);
        }

        // Check prerequisites
        if ($course->prerequisite_course_id) {
            $prerequisiteEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->prerequisite_course_id)
                ->first();

            if (! $prerequisiteEnrollment || ! $prerequisiteEnrollment->passed_at) {
                return $this->error('You must complete the prerequisite course before enrolling.', null, 403);
            }
        }

        $coursePrice = (float) ($course->activePrice?->amount ?? $course->price ?? 0);

        // Free enrollment (if marked free OR price is 0)
        if ($course->is_free || $coursePrice <= 0) {
            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'progress' => 0,
                    'score' => 0,
                ]
            );

            return $this->created(
                $this->formatEnrollment($enrollment, $course),
                'Enrolled successfully!'
            );
        }

        // Paid course — client must purchase via In-App Purchase
        return $this->error(
            'This is a paid course. Please complete payment to enroll.',
            [
                'requires_payment' => true,
                'course_id' => $course->id,
                'price' => $coursePrice,
                'title' => $course->title,
            ],
            402
        );
    }

    // ── Purchase (In-App Purchases for Android, iOS, Windows, macOS, Web) ──────

    public function purchase(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();

        // Only published courses
        $isPublished = ($course->status instanceof CourseStatus)
            ? $course->status->value === 'published'
            : $course->status === 'published';

        if (! $isPublished) {
            return $this->error('This course is not available for purchase.', null, 422);
        }

        // Already enrolled?
        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $this->success(
                $this->formatEnrollment($existing, $course),
                'You are already enrolled in this course.'
            );
        }

        // Check prerequisites
        if ($course->prerequisite_course_id) {
            $prerequisiteEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->prerequisite_course_id)
                ->first();

            if (! $prerequisiteEnrollment || ! $prerequisiteEnrollment->passed_at) {
                return $this->error('You must complete the prerequisite course before enrolling.', null, 403);
            }
        }

        $purchaseId = (string) $request->input('purchase_id', 'iap_'.time().'_'.uniqid());
        $platform = (string) $request->input('platform', 'unknown');
        $price = (float) $request->input('price', $course->activePrice?->amount ?? $course->price ?? 0);

        $enrollment = DB::transaction(function () use ($user, $course, $purchaseId, $price) {
            $enrollment = Enrollment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'progress' => 0,
                    'score' => 0,
                ]
            );

            // Record Invoice if paid
            if (! $course->is_free && $price > 0) {
                Invoice::create([
                    'user_id' => $user->id,
                    'invoiceable_id' => $course->id,
                    'invoiceable_type' => Course::class,
                    'amount_total' => $price,
                    'amount_subtotal' => $price,
                    'stripe_session_id' => $purchaseId,
                ]);
            }

            // Dispatch welcome email if configured
            $mail = CourseMail::where('course_id', $course->id)->first();
            if (isset($mail)) {
                SendCourseEmailJob::dispatch($user, $mail);
            }

            return $enrollment;
        });

        return $this->created(
            $this->formatEnrollment($enrollment, $course),
            'Masterclass unlocked successfully! Welcome to the course 🎉'
        );
    }

    // ── My Enrollments ────────────────────────────────────────────────────────

    public function myEnrollments(Request $request): JsonResponse
    {
        $enrollments = Enrollment::where('user_id', $request->user()->id)
            ->with([
                'course' => fn ($q) => $q->with([
                    'tutor:id,name',
                    'category:id,name',
                    'activePrice',
                ])->withCount('lessons'),
            ])
            ->orderByDesc('created_at')
            ->get();

        return $this->success(
            $enrollments->map(fn ($e) => $this->formatEnrollment($e, $e->course))
        );
    }

    // ── Enrollment Detail ─────────────────────────────────────────────────────

    public function enrollmentDetail(Request $request, Enrollment $enrollment): JsonResponse
    {
        // Gate: only the owner can view their enrollment
        if ($enrollment->user_id !== $request->user()->id) {
            return $this->forbidden();
        }

        $course = $enrollment->course->load([
            'sections' => fn ($q) => $q->where('status', 'published')
                ->orderBy('order')
                ->with(['lessons' => fn ($lq) => $lq->orderBy('lesson_order')]),
        ]);

        $completedLessonIds = LessonTracking::where('user_id', $request->user()->id)
            ->whereNotNull('completed_at')
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        $totalLessons = $course->lessons()->count();
        $completedCount = count($completedLessonIds);
        $progressPercent = $totalLessons > 0 ? round($completedCount / $totalLessons * 100) : 0;

        return $this->success([
            'enrollment' => $this->formatEnrollment($enrollment, $course),
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedCount,
            'progress_percent' => $progressPercent,
            'sections' => $course->sections->map(fn ($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'order' => $s->order,
                'lessons' => $s->lessons->map(fn ($l) => [
                    'id' => $l->id,
                    'title' => $l->title,
                    'duration' => $l->duration ?? 0,
                    'is_completed' => in_array($l->id, $completedLessonIds),
                    'video_url' => $l->video_url ?? null,
                    'video_id' => $l->getVideoId(),
                ]),
            ]),
        ]);
    }

    // ── Student Dashboard ─────────────────────────────────────────────────────

    public function dashboard(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $totalEnrolled = Enrollment::where('user_id', $userId)->count();
        $completed = Enrollment::where('user_id', $userId)
            ->whereNotNull('passed_at')
            ->count();
        $inProgress = $totalEnrolled - $completed;

        $recentCourses = Enrollment::where('user_id', $userId)
            ->with(['course:id,slug,title', 'course.activePrice'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($e) => [
                'course_id' => $e->course_id,
                'course_title' => $e->course?->title,
                'course_slug' => $e->course?->slug,
                'progress' => $e->progress ?? 0,
            ]);

        return $this->success([
            'total_enrolled' => $totalEnrolled,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'recent_courses' => $recentCourses,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function formatEnrollment(Enrollment $enrollment, ?Course $course): array
    {
        return [
            'id' => $enrollment->id,
            'course_id' => $enrollment->course_id,
            'enrolled_at' => $enrollment->created_at?->toISOString(),
            'completed_at' => $enrollment->passed_at?->toISOString(),
            'progress_percent' => $enrollment->progress ?? 0,
            'course' => $course ? [
                'id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->title,
                'cover_image' => $course->cover_image,
                'tutor_name' => $course->tutor?->name ?? 'Unknown',
                'lessons_count' => $course->lessons_count ?? ($course->lessons()->count()),
            ] : null,
        ];
    }
}
