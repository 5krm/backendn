<?php

namespace App\ViewModels\Promotions;

use App\Models\Promotion;
use App\Support\PromotionTemplateRegistry;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class PromotionBannerView implements Arrayable
{
    public function __construct(
        public Promotion $promotion,
        public string $variant = 'banner',
    ) {
        $this->promotion->loadMissing(['courses.media']);
    }

    public static function current(string $variant = 'banner'): ?self
    {
        $promotion = Promotion::current();

        return $promotion ? new self($promotion, $variant) : null;
    }

    public function forVariant(string $variant): self
    {
        return new self($this->promotion, $variant);
    }

    public function storageKey(): string
    {
        // Determine the type of promotion for suffix:
        // - If the promotion uses an image-based display, use "image" as the suffix.
        // - Otherwise (template-based), use either the template name or the default template.
        $suffix = $this->promotion->usesImage()
            ? 'image'
            : ($this->promotion->template ?? PromotionTemplateRegistry::defaultTemplate());

        // The storage key uniquely identifies if this promotion/variant was dismissed by the user.
        // Format: "promo-{suffix}-dismissed-{promotion_id}"
        return 'promo-'.$suffix.'-dismissed-'.$this->promotion->id;
    }

    public function discountPercent(): int
    {
        return (int) $this->promotion->discount_percent;
    }

    public function daysRemaining(): int
    {
        return $this->promotion->daysRemaining();
    }

    public function coursesCount(): int
    {
        return (int) ($this->promotion->courses_count
            ?? $this->promotion->courses->count());
    }

    public function headline(): string
    {
        return (string) $this->promotion->title;
    }

    public function subheadline(): string
    {
        return $this->promotion->description
            ?: __('promotions.default_subheadline', ['percent' => $this->discountPercent()]);
    }

    public function promoCode(): string
    {
        return 'SAVE'.$this->discountPercent();
    }

    public function endsAtLabel(): string
    {
        if ($this->promotion->end instanceof Carbon) {
            return $this->promotion->end->translatedFormat('d M Y');
        }

        return Carbon::parse($this->promotion->end)->translatedFormat('d M Y');
    }

    public function imageUrl(): string
    {
        $course = $this->promotion->courses->first();

        if ($course?->cover_image) {
            return $course->cover_image;
        }

        return asset('assets/images/hero-img.png');
    }

    public function coursesUrl(): string
    {
        return route('promotions.show', $this->promotion);
    }

    public function toArray(): array
    {
        return [
            'promotion' => $this->promotion,
            'badge' => __('promotions.limited_offer'),
            'headline' => $this->headline(),
            'subheadline' => $this->subheadline(),
            'promo_code' => $this->promoCode(),
            'courses_count' => $this->coursesCount(),
            'days_remaining' => $this->daysRemaining(),
            'discount_percent' => $this->discountPercent(),
            'ends_at_label' => $this->endsAtLabel(),
            'storage_key' => $this->storageKey(),
            'image_url' => $this->imageUrl(),
            'courses_url' => $this->coursesUrl(),
            'support_line' => __('promotions.simple_support_line'),
            'highlights' => [
                __('promotions.highlight_flexible'),
                __('promotions.highlight_expert'),
                __('promotions.highlight_certificate'),
            ],
        ];
    }
}
