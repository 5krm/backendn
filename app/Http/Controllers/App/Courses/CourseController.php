<?php

namespace App\Http\Controllers\App\Courses;

use App\Models\User;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\Certificate;
use App\Enums\CourseStatus;
use Illuminate\Http\Request;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Actions\GenerateCertificate;
use App\Http\Controllers\Controller;
use App\ViewModels\Courses\CourseView;
use App\ViewModels\Promotions\PromotionBannerView;

class CourseController extends Controller
{

    public function index(Request $request)
    {
        $lang = auth()->check()
            ? auth()->user()->displayLang()
            : app()->getLocale();

        $categories = Category::forLocale($lang)->get();
        $category = $categories->firstWhere('slug', $request->category);

        $sort = $request->get('sort');
        $freeOnly = $request->boolean('free_only');

        $courses = Course::query()
            ->with(['media', 'tutor', 'category', 'organization.media', 'userWishlists', 'userEnrollment', 'activePromotions'])
            ->withCount([
                'lessons' => fn($q) => $q->where('status', 'published'),
                'students',
            ])
            ->when($category, fn($q) => $q->where('category_id', $category->id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('title', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->when($freeOnly, fn($q) => $q->where('is_free', 1))
            ->when($sort, function ($q) use ($sort) {
                return match ($sort) {
                    'title' => $q->orderBy('title'),
                    'students' => $q->orderByDesc('students_count'),
                    'lessons' => $q->orderByDesc('lessons_count'),
                    'free' => $q->orderByDesc('is_free')->orderByDesc('created_at'),
                    default => $q->orderByDesc('created_at'),
                };
            }, fn($q) => $q->orderByDesc('created_at'))
            ->whereIn('status', [
                CourseStatus::published->value,
                CourseStatus::preview->value,
            ])
            ->where('lang', $lang)
            ->paginate(12);

        return view('app.courses.index', [
            'courses' => $courses = $this->transformCourses($courses, auth()->user()),
            'categories' => $categories,
            'sort' => $sort,
            'free_only' => $freeOnly,
            'promotionBanner' => PromotionBannerView::current(),
        ]);
    }

    public function show(string $slug)
    {
        $course = Course::query()
            ->with([
                'media',
                'testimonials',
                'userWishlists',
                'userEnrollment',
                'tutor' => function ($query) {
                    $query->withCount('tutorCourses')
                        ->with(['tutorProfile', 'socialLinks']);
                },
                'organization' => function ($query) {
                    $query->with('media')->withCount('followers');
                },
            ])
            ->with('sections.publishedLessons', function ($query) {
                $query->where('status', CourseStatus::published);
            })
            ->where('slug', $slug)
            ->withCount([
                'lessons' => fn($query) => $query->where('status', 'published'),
                'students'
            ])
            ->firstOrFail();
        $courseView = new CourseView($course);
        if (auth()->check()) {
            $courseView = $courseView->forUser();
        }

        return view('app.courses.details', [
            'course' => $courseView->toArray(),
            'sections' => $course->sections,
        ]);
    }

    // NOTE:
    // This is the old  API; it only exists, so the old certificate links do not break.
    public function fetch_certificate(Course $course)
    {
        /** @var User */
        $user = auth()->user();
        $enrollment = Enrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $certificate = Certificate::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        if (! $certificate->isValid()) {
            abort(404);
        }

        $course->setDurationToEnglish();
        $stream = (new GenerateCertificate())->execute($user, $course);
        return $stream;
    }

    
    public function certificate(Course $course)
    {
        return view('app.courses.exam.certificate', ['course' => $course]);
    }

    public function toggleWishlist(Course $course)
    {
        Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
        ]);

        return back()->with('success', __('course.added_to_wishlist'));
    }

    private function transformCourses($courses, ?User $user)
    {
        $courses->getCollection()->transform(function (Course $course) use ($user) {
            $view = new CourseView($course);

            return $user
                ? $view->forUser()->toArray()
                : $view->toArray();
        });

        return $courses;
    }
}
