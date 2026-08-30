<?php

namespace App\Models;

use App\Enums\PromotionDisplayType;
use App\Models\Courses\Course;
use App\Support\PromotionTemplateRegistry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'stripe_promotion_id',
        'discount_percent',
        'start',
        'end',
        'status',
        'display_type',
        'template',
        'banner_image',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'status' => 'boolean',
        'discount_percent' => 'integer',
        'display_type' => PromotionDisplayType::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Promotion $promotion): void {
            if ($promotion->display_type === PromotionDisplayType::Template && blank($promotion->template)) {
                $promotion->template = PromotionTemplateRegistry::defaultTemplate();
            }
        });
    }

    public function usesTemplate(): bool
    {
        return $this->display_type === PromotionDisplayType::Template;
    }

    public function usesImage(): bool
    {
        return $this->display_type === PromotionDisplayType::Image;
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function bannerImageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (blank($this->banner_image)) {
                return null;
            }

            if (str_starts_with($this->banner_image, 'http://')
                || str_starts_with($this->banner_image, 'https://')
                || str_starts_with($this->banner_image, '/')) {
                return $this->banner_image;
            }

            return Storage::disk('public')->url($this->banner_image);
        });
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', true)
            ->where('start', '<=', Carbon::now())
            ->where('end', '>=', Carbon::now());
    }

    public function daysRemaining(): int
    {
        if (! $this->end) {
            return 1;
        }

        return max(1, (int) Carbon::now()->diffInDays($this->end));
    }

    public static function current(): ?self
    {
        return static::query()
            ->active()
            ->withCount('courses')
            ->orderByDesc('discount_percent')
            ->orderBy('end')
            ->first();
    }
}
