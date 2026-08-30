<?php

namespace App\Listeners;

use App\Enums\PreferenceKey;
use App\Events\NewCommentPosted;
use App\Mail\Comments\NewComment;
use App\Models\Courses\Course;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendNewCommentEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewCommentPosted $event): void
    {
        $course = Course::whereHas('lessons', fn($q) => $q->where('id', $event->comment->lesson_id))
            ->first();

        $user = User::query()
            ->where('id', '!=', $event->comment->user_id)
            ->where('id', $course->tutor_id)
            ->first();

        if (isset($user)) {
            Mail::to($user)->send(new NewComment($user, $event->comment));
        }
    }
}
