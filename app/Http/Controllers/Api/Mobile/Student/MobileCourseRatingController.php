<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Courses\CourseRating;
use App\Models\Courses\Enrollment;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Allows enrolled students to rate a course (create or update).
 */
class MobileCourseRatingController extends Controller
{
    use ApiResponse;

    /**
     * Get ratings, reviews and breakdown summary for a course.
     */
    public function index(Request $request, Course $course): JsonResponse
    {
        $ratings = CourseRating::query()
            ->with(['user'])
            ->where('course_id', $course->id)
            ->latest()
            ->get();

        $totalReviews = $ratings->count();
        $averageRating = $totalReviews > 0
            ? round((float) $ratings->avg('rating'), 1)
            : (float) ($course->average_rating ?? 5.0);

        $distribution = [
            5 => $ratings->where('rating', 5)->count(),
            4 => $ratings->where('rating', 4)->count(),
            3 => $ratings->where('rating', 3)->count(),
            2 => $ratings->where('rating', 2)->count(),
            1 => $ratings->where('rating', 1)->count(),
        ];

        $reviews = $ratings->map(function (CourseRating $r) {
            $user = $r->user;
            $avatar = $user?->profile;

            return [
                'id'                  => $r->id,
                'rating'              => (int) $r->rating,
                'instructor_rating'   => (int) $r->rating,
                'content_rating'      => (int) $r->rating,
                'review'              => $r->review ?? '',
                'comment'             => $r->review ?? '',
                'created_at'          => $r->created_at?->toISOString(),
                'is_verified_student' => true,
                'likes_count'         => 0,
                'user' => $user ? [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'avatar' => $avatar,
                ] : null,
                'user_name'   => $user?->name ?? 'Student',
                'user_avatar' => $avatar,
            ];
        })->values();

        return $this->success([
            'average_rating'      => $averageRating,
            'total_reviews'       => $totalReviews,
            'rating_distribution' => $distribution,
            'distribution'        => $distribution,
            'instructor_average'  => $averageRating,
            'content_average'     => $averageRating,
            'reviews'             => $reviews,
        ]);
    }

    public function store(Request $request, Course $course): JsonResponse
    {
        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'review'  => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        // Must be enrolled
        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (! $enrolled) {
            return $this->forbidden('You must be enrolled to rate this course.');
        }

        // Upsert rating
        $rating = CourseRating::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'rating' => $request->rating,
                'review' => $request->review ?? null,
            ]
        );

        return $this->success([
            'id'         => $rating->id,
            'rating'     => $rating->rating,
            'review'     => $rating->review,
            'created_at' => $rating->created_at->toISOString(),
        ], 'Rating submitted');
    }
}
