<?php

namespace App\Http\Controllers\App;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Courses\Enrollment;
use App\Models\Organization;
use App\Models\OrganizationFollower;
use App\Models\Tutor;
use App\ViewModels\Courses\CourseView;
use App\Models\User;
use Illuminate\View\View; 

class OrganizationController extends Controller
{
    public function index(Organization $organization): View
    {
        abort_unless($organization->is_active, 404);
        $organization->loadMissing("users");
        $courses = $organization->courses()
            ->with(['media', 'category', 'organization.media', 'tutor.tutorProfile', 'tutor.tutorProfile.media'])
            ->withCount([
                'lessons' => fn($q) => $q->where('status', CourseStatus::published),
                'students'
            ])
            ->whereIn('status', [
                CourseStatus::published->value,
                CourseStatus::preview->value,
            ])
            ->latest()
            ->get()
            ->map(function ($course) {
                $view = new CourseView($course);

                $payload = auth()->check()
                    ? $view->forUser(auth()->user())->toArray()
                    : $view->toArray();

                 $payload['organization'] = null;

                return $payload;
            });

        $courseIds = $organization->courses()->pluck('id');

        $studentsCount = $courseIds->isEmpty()
            ? 0
            : (int) Enrollment::query()
                ->whereIn('course_id', $courseIds)
                ->distinct()
                ->count('user_id');

        $instructorsCount = User::query()
            ->where("organization_id", $organization->id)
            ->whereHas('tutorCourses')
            ->count();

        $instructors = Tutor::query()
            ->with(['user', 'media'])
            ->whereRelation('user', 'organization_id', $organization->id)
            ->where('is_active', true)
            ->latest()
            ->limit(12)
            ->get();

        $rating = $courseIds->isEmpty()
            ? null
            : Enrollment::query()
            ->whereIn('course_id', $courseIds)
            ->whereNotNull('score')
            ->avg('score');

        $followersCount = OrganizationFollower::query()
            ->where('organization_id', $organization->id)
            ->count();

        return view('app.organization.index', [
            'organization' => $organization,
            'courses' => $courses,
            'instructors' => $instructors,
            'stats' => [
                'courses_count' => $courses->count(),
                'students_count' => $studentsCount,
                'rating' => $rating !== null ? number_format(min($rating / 20, 5), 1) : '0.0',
                'instructors_count' => $instructorsCount,
                'followers_count' => $followersCount,
            ],
        ]);
    }

 
}
