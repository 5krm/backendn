<?php

namespace App\Models;

use App\Models\Courses\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the localized name based on current locale
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ar' && $this->name_ar) {
            return $this->name_ar;
        }

        return $this->name;
    }

    /**
     * Scope to filter categories by locale
     * Arabic locale: only categories with name_ar
     * English locale: only categories with name (all have name)
     */
    public function scopeForLocale(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale === 'ar') {
            return $query->whereNotNull('name_ar')->where('name_ar', '!=', '');
        }

        return $query;
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            $name = $category->name;
            if (Category::where('slug', Str::slug($name))->exists()) {
                $name .= '-'.Str::random(5);
            }

            $category->slug = Str::slug($name);
        });
    }
}
