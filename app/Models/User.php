<?php

namespace App\Models;

use App\Enums\FollowupEmailType;
use App\Enums\PreferenceKey;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonComment\Comment;
use App\Models\Lessons\LessonNote;
use App\Models\Lessons\LessonTracking;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasFactory, InteractsWithMedia, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'job_title',
        'job_title_en',
        'bio',
        'bio_en',
        'country_id',
        'is_tutor',
        'is_admin',
        'admin_access',
        'organization_id',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'admin_access' => 'boolean',
    ];

    public function tutorCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'tutor_id');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->using(Enrollment::class)
            ->withTimestamps()
            ->withPivot('passed_at', 'score', 'progress');
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_trackings', 'user_id', 'lesson_id')
            ->withTimestamps()
            ->withPivot('completed_at');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LessonNote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function followupEmails(): HasMany
    {
        return $this->hasMany(FollowupEmail::class);
    }

    public function tutorProfile(): HasOne
    {
        return $this->hasOne(Tutor::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(SocialLink::class);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function localizedBio(): Attribute
    {
        return Attribute::make(
            get: function () {
                $locale = app()->getLocale();

                if ($locale === 'en' && ! empty($this->bio_en)) {
                    return $this->bio_en;
                }

                return $this->bio;
            }
        );
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function localizedJobTitle(): Attribute
    {
        return Attribute::make(
            get: function () {
                $locale = app()->getLocale();

                if ($locale === 'en' && ! empty($this->job_title_en)) {
                    return $this->job_title_en;
                }

                return $this->job_title;
            }
        );
    }

    public function scopeForFollowupEmails(Builder $query, FollowupEmailType $followupEmailType): Builder
    {
        return $query
            ->with(['courses', 'followupEmails' => fn ($q) => $q->where('email_type', $followupEmailType)])
            ->whereHas('preferences', fn ($q) => $q->where('key', PreferenceKey::FollowupEmail)->where('value', true));
    }

    /**
     * @return Collection<LessonTracking>
     */
    public function completedLessons(int $course): Collection
    {
        if (! $this->relationLoaded('lessons')) {
            $this->load('lessons');
        }

        return $this
            ->lessons
            ->where('course_id', $course)
            ->sortBy('order')
            ->pluck('pivot')
            ->whereNotNull('completed_at')
            ->values();
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function isTutor(): bool
    {
        // Use direct database query to completely avoid any relationship loading issues
        return DB::table('tutors')
            ->where('user_id', $this->id)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function isVerified(): bool
    {
        return $this->email_verified_at !== null ?? false;
    }

    public function displayLang(): string
    {
        $preference = $this->getPreference(PreferenceKey::DisplayLanguage);

        return $preference?->value ?? 'ar';
    }

    public function learningLang(): string
    {
        $preference = $this->getPreference(PreferenceKey::LearningLanguage);

        return $preference?->value ?? 'ar';
    }

    private function getPreference(PreferenceKey $key): ?UserPreference
    {
        if ($this->relationLoaded('preferences')) {
            return $this->preferences->where('key', $key)->first();
        }

        $this->load('preferences');

        return $this->preferences->where('key', $key)->first();
    }

    /**
     * @return Attribute<string, never>
     */
    protected function profile(): Attribute
    {
        $file = $this->getMedia('avatars')
            ->last()
            ?->getUrl() ??
            URL::to('/').'/assets/images/default-user.png';

        return Attribute::make(
            get: fn () => $file
        );
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatarUrl = $this->getMedia('avatars')
            ->last()
            ?->getUrl();

        if (! $avatarUrl) {
            return asset('assets/images/default-user.png');
        }

        return $avatarUrl;
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function firstName(): string
    {
        return explode(' ', $this->name)[0];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isTutor();
    }

    public static function findByToken($token): User
    {
        $email = decrypt($token);

        return static::where('email', $email)->first();
    }

    public function hasCompleteProfile(): bool
    {
        return ! empty($this->country)
            && ! empty($this->phone);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistCourses()
    {
        return $this->belongsToMany(Course::class, 'wishlists');
    }

    public function followedOrganizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_followers')
            ->withTimestamps();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
