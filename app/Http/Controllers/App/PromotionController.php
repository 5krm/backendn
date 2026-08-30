<?php

namespace App\Http\Controllers\App;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Promotion;
use App\Models\User;
use App\ViewModels\Courses\CourseView;
use App\ViewModels\Promotions\PromotionBannerView;

class PromotionController extends Controller
{
    public function show(Promotion $promotion)
    {
        abort_unless(
            $promotion->status
                && $promotion->start <= now()
                && $promotion->end >= now(),
            404
        );

        $lang = auth()->check()
            ? auth()->user()->displayLang()
            : app()->getLocale();

        $courses = Course::query()
            ->with(['media', 'tutor', 'category', 'organization.media', 'userWishlists', 'userEnrollment', 'activePromotions'])
            ->withCount([
                'lessons' => fn ($q) => $q->where('status', 'published'),
                'students',
            ])
            ->whereHas(
                'promotions',
                fn ($query) => $query->where('promotions.id', $promotion->id)
            )
            ->whereIn('status', [
                CourseStatus::published->value,
            ])
            // ->where('lang', $lang)
            ->orderByDesc('created_at')
            ->paginate(12);

        $banner = new PromotionBannerView($promotion);

        return view('app.promotions.show', [
            'promotion' => $promotion,
            'banner' => $banner,
            'courses' => $this->transformCourses($courses, auth()->user()),
        ]);
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
