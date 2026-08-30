<?php

namespace App\Models;

 use App\Models\Lessons\Lesson;
use App\Models\Quizzes\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\URL;
use App\Models\Courses\Course;
 
class Tutor extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'name_en',
        'specialization',
        'specialization_en',
        'experience_years',
        'hourly_rate',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'experience_years' => 'integer',
        'hourly_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all courses taught by this tutor.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(
            Course::class,
            'tutor_id', // foreign key on courses table
            'user_id'   // local key on tutors table
        );
    }
 
    /**
     * Get the tutor's specialization in the current locale.
     * Falls back to the Arabic specialization if no English one is set.
     */
    protected function localizedSpecialization(): Attribute
    {
        return Attribute::make(
            get: function () {
                $locale = app()->getLocale();

                if ($locale === 'en' && !empty($this->specialization_en)) {
                    return $this->specialization_en;
                }

                return $this->specialization;
            }
        );
    }

    /**
     * Get the tutor's name in the current locale.
     * Falls back to the user's name if no English name is set.
     */
    protected function localizedName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $locale = app()->getLocale();

                if ($locale === 'en' && !empty($this->name_en)) {
                    return $this->name_en;
                }

                return $this->user?->name;
            }
        );
    }

    /**
     * Get the tutor's profile image
     */
    protected function profileImage(): Attribute
    {
        return Attribute::make(
            get: function () {
                $media = $this->getMedia('profile')->last();

                if ($media) {
                    return $media->getUrl();
                }

                if ($this->user) {
                    return $this->user->profile;
                }

                return URL::to('/') . '/assets/images/default-user.png';
            }
        );
    }

    /**
     * Get total students enrolled in tutor's courses
     */
    public function getTotalStudentsAttribute(): int
    {
        return $this->courses()
            ->withCount('students')
            ->get()
            ->sum('students_count');
    }

    /**
     * Get total revenue from tutor's courses
     */
    public function getTotalRevenueAttribute(): float
    {
        return $this->courses()
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->join('invoices', function ($join) {
                $join->on('invoices.invoiceable_id', '=', 'courses.id')
                    ->where('invoices.invoiceable_type', '=', Course::class);
            })
            ->sum('invoices.amount');
    }

    /**
     * Get completion rate for tutor's courses
     */
    public function getCompletionRateAttribute(): float
    {
        $totalEnrollments = $this->courses()
            ->withCount('students')
            ->get()
            ->sum('students_count');

        if ($totalEnrollments === 0) {
            return 0;
        }

        $completedEnrollments = $this->courses()
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->whereNotNull('enrollments.passed_at')
            ->count();

        return ($completedEnrollments / $totalEnrollments) * 100;
    }
}
