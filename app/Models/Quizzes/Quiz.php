<?php

namespace App\Models\Quizzes;

use App\Models\Lessons\Lesson;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'course_id',
        'lesson_id',
        'question',
        'order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Courses\Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function quizOptions(): HasMany
    {
        return $this->hasMany(QuizOption::class)->orderBy('order', 'asc');
    }

    public function getQuestionTextAttribute(): string
    {
        return $this->question ?: $this->title ?: '';
    }

    public function getQuestionAttribute($value): string
    {
        return $value ?: $this->attributes['title'] ?: '';
    }

    public function correctOption(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->relationLoaded('quizOptions')) {
                $this->load('quizOptions');
            }

            return $this->quizOptions->where('is_correct', true)->first()->id;
        });
    }
}
