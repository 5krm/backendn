<?php

namespace App\Models\Courses;

use App\Enums\CourseStatus;
use App\Enums\Level;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonComment\Comment;
use App\Models\Organization;
use App\Models\Promotion;
use App\Models\Quizzes\Quiz;
use App\Models\User;
use App\Models\Wishlist;
use App\Traits\HasDuration;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Override;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Course extends Model implements HasMedia
{
    use HasDuration, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $casts = [
        'status' => CourseStatus::class,
        'level' => Level::class,
        'is_free' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (auth()->check() && auth()->user()->organization_id && empty($course->organization_id)) {
                $course->organization_id = auth()->user()->organization_id;
            }

            $title = $course->title;
            if (Course::where('slug', Str::slug($title))->exists()) {
                $title .= '-'.Str::random(5);
            }

            $course->slug = Str::slug($title);
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function invoices(): MorphMany
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function publishedLessons(): HasMany
    {
        return $this->hasMany(Lesson::class)
            ->where('status', CourseStatus::published)
            ->orderBy('order');
    }

    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, Lesson::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(CourseTestimonial::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->using(Enrollment::class)
            ->withTimestamps()
            ->withPivot('passed_at', 'score', 'progress', 'id');
    }

    public function userEnrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class)
            ->where('user_id', auth()->id());
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function userWishlists(): HasMany
    {
        if (! auth()->check()) {
            return $this->hasMany(Wishlist::class);
        }

        return $this
            ->hasMany(Wishlist::class)
            ->where('user_id', auth()->id());
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(CourseMail::class);
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(CourseSurvey::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CoursePrice::class);
    }

    public function activePrice(): HasOne
    {
        return $this->hasOne(CoursePrice::class)
            ->where('is_active', true);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class);
    }

    public function activePromotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class)
            ->where('status', true)
            ->where('start', '<=', Carbon::now())
            ->where('end', '>=', Carbon::now());
    }

    /**
     * @return Attribute<string, never>
     */
    protected function coverImage(): Attribute
    {
        return Attribute::get(function () {
            $media = $this->getMedia('covers')->last();
            if (! $media) {
                return URL::to('/').'/assets/images/default-course.png';
            }

            return $media->hasGeneratedConversion('covers')
                ? $media->getUrl('covers')
                : $media->getUrl();
        });
    }

    public function setDurationToEnglish(): void
    {
        $this->only_en = true;
    }

    #[Override]
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('covers')
            ->performOnCollections('covers')
            ->fit(Fit::Contain, 640, 360)
            ->queued();
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(CourseRating::class);
    }

    public function averageRating(): Attribute
    {
        return Attribute::get(function () {
            $avg = round((float) $this->ratings()->avg('rating'), 1);

            return sprintf('%.1f', $avg !== null ? (float) round((float) $avg, 1) : 0);

        });
    }

    public function scopeSearchCourse($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function scopeSortByOption($query, $sortBy)
    {
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('students_count', 'desc');
                break;
            case 'rating':
                $query->withAvg('ratings', 'rating')->orderBy('ratings_avg_rating', 'desc');
                break;
            case 'price_asc':
                $query->orderBy(
                    CoursePrice::select('amount')
                        ->whereColumn('course_id', 'courses.id')
                        ->where('is_active', true)
                        ->limit(1),
                    'asc'
                );
                break;
            case 'price_desc':
                $query->orderBy(
                    CoursePrice::select('amount')
                        ->whereColumn('course_id', 'courses.id')
                        ->where('is_active', true)
                        ->limit(1),
                    'desc'
                );
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }
}
