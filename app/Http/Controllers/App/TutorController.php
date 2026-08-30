<?php

namespace App\Http\Controllers\App;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Courses\Enrollment;
use App\Models\Tutor;
use App\ViewModels\Courses\CourseView;
use Illuminate\View\View;

class TutorController extends Controller
{
    public function index(Tutor $tutor): View
    {
        $tutor->load(['user.socialLinks', 'user.organization']);

        $lang = auth()->check()
            ? auth()->user()->displayLang()
            : app()->getLocale();

        $courses = $tutor->courses()
        ->with([
        'organization',
        'category',
        'activePrice',
        'prices',
        'sections.publishedLessons',
        'userEnrollment',
        'userWishlists',
        ])
        ->whereIn('status', [
            CourseStatus::published->value,
            CourseStatus::preview->value,
        ])
        ->where('lang', $lang)
        ->latest()
        ->get()
        ->map(function ($course) {
            return (new CourseView($course))
                ->forUser()   // optional if your app uses authenticated users
                ->toArray();
        });

        $stats = [
            'courses_count'   => $tutor->courses()->count(),
            'students_count'  => $tutor->total_students,
            'lessons_count'   => $tutor->courses()->withCount('lessons')->get()->sum('lessons_count'),
            'completion_rate' => round($tutor->completion_rate),
        ];

        return view('app.tutor.index', [
            'tutor'   => $tutor,
            'courses' => $courses,
            'stats'   => $stats,
        ]);
    }
}