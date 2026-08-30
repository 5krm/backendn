<?php

namespace App\Models\Courses;

use App\Enums\CourseStatus;
use App\Traits\HasDuration;
use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseSection extends Model
{
    use HasFactory, SoftDeletes, HasDuration;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => CourseStatus::class,
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'section_id')->orderBy('order');
    }

    public function publishedLessons(): HasMany
    {
        return $this
            ->hasMany(Lesson::class, 'section_id')
            ->where('status', CourseStatus::published)
            ->orderBy('order')
            ->chaperone();
    }

    protected static function booted(): void
    {
        static::creating(function ($section) {
            if (empty($section->order)) {
                $section->order = static::where('course_id', $section->course_id)->count() + 1;
            }
        });

        static::updated(function ($section) {
            if ($section->wasChanged('order')) {
                Lesson::where('section_id', $section->id)->update(['section_order' => $section->order]);
            }
        });
    }
}
