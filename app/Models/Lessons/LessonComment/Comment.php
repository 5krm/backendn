<?php

namespace App\Models\Lessons\LessonComment;

use App\Models\User;
use App\Scopes\CommentScopes;
use App\Models\Lessons\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Lessons\LessonComment\CommentPresenter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\Lessons\LessonComment\CommentFactory;

class Comment extends Model
{
    use CommentScopes, SoftDeletes, HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'lesson_id',
        'parent_id',
    ];

    public function presenter(): CommentPresenter
    {
        return new CommentPresenter($this);
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->oldest();
    }

    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
