<?php

namespace App\ViewModels\Courses;

use App\Enums\CourseStatus;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class CourseView implements Arrayable
{
    public bool $enrolled;

    public string $link;

    public ?Enrollment $enrollment = null;

    public bool $inWishlist = false;

    public function __construct(public Course $course)
    {
        $this->course->loadMissing(['userWishlists', 'userEnrollment', 'activePromotions']);

        $this->enrolled = false;
        $this->link = route('courses.details', $course->slug);
    }

    public function forUser(): self
    {
        $this->enrolled = $this->course->userEnrollment ? true : false;
        $this->enrollment = $this->course->userEnrollment;
        $this->inWishlist = $this->course->userWishlists->where('course_id', $this->course->id)->isNotEmpty();
        $this->link = route('app.courses.details', $this->course->slug);

        return $this;
    }

    public function toArray(): array
    {
        $previewLesson = null;
        if ($this->course->relationLoaded('sections')) {
            if ($this->course->sections->first()?->relationLoaded('publishedLessons')) {
                $previewLesson = $this->course->sections->first()->publishedLessons->first();
            }
        }

        return [
            'file' => $this->course->coverImage,
            'link' => $this->link,
            'data' => $this->course,
            'enrollment' => $this->enrollment,
            'enrolled' => $this->enrolled,
            'discount' => $this->calcDiscount(),
            'promo' => $this->calcPromotionDiscount(),
            'preview_lesson' => $previewLesson,
            'is_preview' => $this->isPreview(),
            'in_wishlist' => $this->inWishlist,
            'organization' => $this->course->organization ? [
                'id' => $this->course->organization->id,
                'description' => $this->course->organization->description,
                'name' => $this->course->organization->name,
                'slug' => $this->course->organization->slug,
                'logo' => $this->course->organization->logo_url,
                'followers_count' => $this->course->organization->followers_count,

            ] : null,
        ];
    }

    private function calcDiscount(): float
    {
        if ($this->course->activePromotions != null && $this->course->activePromotions->count() > 0) {
            return $this->course->activePromotions?->first()?->discount_percent ?? 0;
        }

        if ($this->course->old_price == 0) {
            return 0;
        }

        $diff = $this->course->old_price - $this->course->price;
        $discount = ($diff / $this->course->old_price) * 100;

        return ceil($discount);
    }

    public function isPreview(): bool
    {
        return $this->course->status === CourseStatus::preview;
    }

    private function calcPromotionDiscount(): ?array
    {

        $promotion = $this->course->activePromotions?->first();
        if ($promotion == null) {
            return null;
        }

        return [
            'discount' => $promotion->discount_percent,
            'days' => (Carbon::now()->diffInDays($promotion->end)),
            'price' => $this->course->price - ($this->course->price * $promotion->discount_percent / 100),
            'old_price' => $this->course->price,
        ];
    }
}
