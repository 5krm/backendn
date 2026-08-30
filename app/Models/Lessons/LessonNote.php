<?php

namespace App\Models\Lessons;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'title',
        'note',
        'color',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public const Colors = [
        '#fde68a', '#d9f99d', '#fed7aa', '#ddd6fe', '#e7e5e4',
    ];
}
