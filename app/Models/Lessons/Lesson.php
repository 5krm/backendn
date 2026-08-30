<?php

namespace App\Models\Lessons;

use App\Enums\CourseStatus;
use App\Traits\HasDuration;
use App\Models\Quizzes\Quiz;
use App\Models\Courses\Course;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Courses\CourseSection;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Lessons\LessonComment\Comment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Lesson extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes, HasDuration;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => CourseStatus::class,
        'attachments' => 'array',
        'is_ready' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->public_key)) {
                $lesson->public_key = (string) Str::uuid();
            }
            if (empty($lesson->lesson_order)) {
                $lesson->lesson_order = Lesson::where('section_id', $lesson->section_id)->max('lesson_order') + 1;
            }
            if (empty($lesson->section_order)) {
                $lesson->section_order = CourseSection::find($lesson->section_id)?->order;
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(LessonTracking::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('order');;
    }

    public function comments(): HasMany
    {
        return $this->HasMany(Comment::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(LessonResource::class);
    }

    public function completed(int $userId): bool
    {
        $tracking = $this->trackings()->where('user_id', $userId)->first();
        if (!$tracking)
            return false;

        return $tracking->completed_at != null;
    }

    public function next(): ?Lesson
    {
        // First try to find next lesson in same section
        $nextInSection = Lesson::query()
            ->where('course_id', $this->course_id)
            ->where('section_id', $this->section_id)
            ->where('status', CourseStatus::published)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
        if ($nextInSection) {
            return $nextInSection;
        }

        // If no next in section, find first lesson in next section
        $currentSection = $this->courseSection;
        if (!$currentSection) {
            return null;
        }

        $nextSection = CourseSection::query()
            ->where('course_id', $this->course_id)
            ->whereHas('publishedLessons')
            ->where('order', '>', $currentSection->order)
            ->orderBy('order')
            ->first();
        if (!$nextSection) {
            return null;
        }
        return Lesson::query()
            ->where('course_id', $this->course_id)
            ->where('section_id', $nextSection->id)
            ->where('status', CourseStatus::published)
            ->orderBy('order')
            ->first();
    }

    public function previous(): ?Lesson
    {
        // First try to find previous lesson in same section
        $prevInSection = Lesson::query()
            ->where('course_id', $this->course_id)
            ->where('section_id', $this->section_id)
            ->where('status', CourseStatus::published)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($prevInSection) {
            return $prevInSection;
        }
        // If no previous in section, find last lesson in previous section
        $currentSection = $this->courseSection;
        if (!$currentSection) {
            return null;
        }

        $prevSection = CourseSection::query()
            ->where('course_id', $this->course_id)
            ->whereHas('publishedLessons')
            ->where('order', '<', $currentSection->order)
            ->orderBy('order', 'desc')
            ->first();

        if (!$prevSection) {
            return null;
        }

        return Lesson::query()
            ->where('course_id', $this->course_id)
            ->where('section_id', $prevSection->id)
            ->where('status', CourseStatus::published)
            ->orderBy('order', 'desc')
            ->first();
    }

    public function getVideoId(): ?string
    {
        // Extract YouTube video ID from various formats
        $videoId = $this->video_id;

        // Extract from URL
        $url = $this->video_id ?? $this->video_url;

        // Handle different YouTube URL formats
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/youtube\.com\/embed\/([^?]+)/', $url, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/youtu\.be\/([^?]+)/', $url, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/youtube\.com\/v\/([^?]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        return $videoId;
    }
}
