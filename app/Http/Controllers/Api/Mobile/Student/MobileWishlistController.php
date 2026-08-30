<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Wishlist;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles the student's wishlist (save-for-later courses).
 */
class MobileWishlistController extends Controller
{
    use ApiResponse;

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $items = Wishlist::where('user_id', $request->user()->id)
            ->with([
                'course' => fn ($q) => $q->with([
                    'tutor:id,name',
                    'category:id,name',
                    'activePrice',
                ])->withCount('lessons'),
            ])
            ->orderByDesc('created_at')
            ->get();

        return $this->success($items->map(fn ($w) => [
            'wishlist_id' => $w->id,
            'saved_at' => $w->created_at->toISOString(),
            'course' => $w->course ? [
                'id' => $w->course->id,
                'slug' => $w->course->slug,
                'title' => $w->course->title,
                'cover_image' => $w->course->cover_image,
                'level' => $w->course->level?->value ?? $w->course->level,
                'is_free' => (bool) $w->course->is_free,
                'price' => $w->course->activePrice?->amount ?? 0,
                'average_rating' => (float) ($w->course->average_rating ?? 0),
                'lessons_count' => $w->course->lessons_count,
                'tutor_name' => $w->course->tutor?->name ?? 'Unknown',
                'category_name' => $w->course->category?->name ?? null,
            ] : null,
        ]));
    }

    // ── Toggle ────────────────────────────────────────────────────────────────

    public function toggle(Request $request, Course $course): JsonResponse
    {
        $userId = $request->user()->id;

        $existing = Wishlist::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return $this->success(['wishlisted' => false], 'Removed from wishlist');
        }

        Wishlist::create([
            'user_id' => $userId,
            'course_id' => $course->id,
        ]);

        return $this->success(['wishlisted' => true], 'Added to wishlist');
    }
}
