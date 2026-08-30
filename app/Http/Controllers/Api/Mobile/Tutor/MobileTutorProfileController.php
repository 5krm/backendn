<?php

namespace App\Http\Controllers\Api\Mobile\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class MobileTutorProfileController extends Controller
{
    use ApiResponse;

    public function show(int $tutorId): JsonResponse
    {
        $tutor = User::with('tutorProfile')
            ->whereHas('tutorProfile', function ($query) {
                $query->where('is_active', true);
            })
            ->findOrFail($tutorId);

        $courses = Course::query()
            ->where('tutor_id', $tutor->id)
            ->where('status', 'published')
            ->with(['category:id,name', 'activePrice'])
            ->withCount(['students', 'lessons'])
            ->get();

        return $this->success([
            'id' => $tutor->id,
            'name' => $tutor->name,
            'bio' => $tutor->bio ?? null,
            'avatar' => $tutor->profile ?? null,
            'job_title' => $tutor->job_title ?? null,
            'courses' => $courses->map(fn ($course) => [
                'id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->title,
                'cover_image' => $course->cover_image,
                'level' => $course->level?->value ?? $course->level,
                'is_free' => (bool) $course->is_free,
                'price' => $course->activePrice?->amount ?? 0,
                'duration_minutes' => $course->duration ?? 0,
                'students_count' => $course->students_count ?? 0,
                'lessons_count' => $course->lessons_count ?? 0,
                'average_rating' => (float) ($course->average_rating ?? 0),
                'category' => $course->category ? [
                    'id' => $course->category->id,
                    'name' => $course->category->name,
                ] : null,
            ]),
        ]);
    }
}
