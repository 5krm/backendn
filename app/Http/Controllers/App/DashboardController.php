<?php

namespace App\Http\Controllers\App;

use App\Enums\CourseStatus;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Models\Courses\CourseRating;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\LessonComment\Comment;
use App\Models\Lessons\LessonNote;
use App\Models\Lessons\LessonTracking;
use App\Models\Wishlist;
use App\ViewModels\Courses\CourseView;

class DashboardController extends Controller
{
    public function index(): View
    {
        $nextLesson = LessonTracking::with(['lesson', 'course.media'])
            ->whereRelation('course', 'status', CourseStatus::published->value)
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->first();

        $enrollments = Enrollment::query()
            ->with(['course' => function ($query) {
                $query->withCount('lessons');
            }, 'course.media', 'media', 'course.category', 'course.tutor', 'course.ratings'])

            ->where('user_id', auth()->id())
            ->get();

        $enrollments = $enrollments->filter(fn($en) => $en->course !== null && $en->course->status == CourseStatus::published);
        $progress = $enrollments->where('course_id', $nextLesson?->course_id)->first()?->progress ?? 0;


        $certificates = Certificate::query()
            ->with(['course.media', 'media', 'course.organization'])
            ->where('user_id', auth()->id())
            ->whereHas('course')
            ->get();
        $ratings = CourseRating::query()
            ->with(['course.media', 'course.organization', 'course.category', 'course.tutor'])
            ->where('user_id', auth()->id())
            ->whereHas('course')
            ->get();

        $comments = Comment::query()
            ->with(['lesson:id,title,public_key,course_id', 'children', 'lesson.course:id,slug,title', 'user.media'])
            ->where('user_id', auth()->id())
            ->where('parent_id', null)
            ->whereHas('lesson.course') // ✅ ADD THIS
            ->latest()
            ->take(5)
            ->get();

        $user = auth()->user();
        $wishlistCourses = $user->wishlists()
            ->with(['course.media'])
            ->whereHas(
                'course',
                fn($q) =>
                $q->where('status', CourseStatus::preview)
            )
            ->latest()
            ->get();


        $totals = [
            'in_progress' => $enrollments->where('progress', '<', 100)->count(),
            'completed' => $enrollments->where('progress', 100)->count(),
            'saved' => $wishlistCourses->count(),
            'certificates' => $certificates->count(),
        ];

        

        return view('app.home', [
            'lesson' => $nextLesson,
            'progress' => $progress,
            'enrollments' =>  $enrollments,
            'certificates' =>  $certificates,
            'wishlistCourses' => $wishlistCourses,
            'comments' => $comments,
            'user' => $user,
            'totals' => $totals,
            'ratings' => $ratings
        ]);
    }

    public function removeWishCourse(Course $course)
    {
        Wishlist::query()
            ->where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->delete();

        return redirect()->back()->with('success', __('course.removed_from_wishlist'));
    }
}
