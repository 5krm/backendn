<?php

namespace App\Providers;

use App\Events\CoursePublished;
use App\Events\LessonPublished;
use App\Events\LessonTrackingEvent;
use App\Events\NewCommentPosted;
use App\Events\NewReplyPosted;
use App\Listeners\InitializeUserPreferences;
use App\Listeners\SendNewCommentEmail;
use App\Listeners\SendNewCoursEmail;
use App\Listeners\SendNewReplyEmail;
use App\Listeners\SendPublishedLessonEmail;
use App\Listeners\SetLessonTracking;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            InitializeUserPreferences::class,
        ],
        LessonTrackingEvent::class => [SetLessonTracking::class],
        CoursePublished::class => [SendNewCoursEmail::class],
        NewCommentPosted::class => [SendNewCommentEmail::class],
        NewReplyPosted::class => [SendNewReplyEmail::class],
        LessonPublished::class => [SendPublishedLessonEmail::class],
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
