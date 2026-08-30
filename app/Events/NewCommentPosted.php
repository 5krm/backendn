<?php

namespace App\Events;

use App\Models\Lessons\LessonComment\Comment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCommentPosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Comment $comment)
    {
        if (! $comment->relationLoaded('lesson.course')) {
            $comment->load('lesson.course');
        }
    }

}
