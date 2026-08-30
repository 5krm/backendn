<?php

namespace App\Listeners;

use App\Enums\CourseStatus;
use App\Events\CourseLessonsUpdatedEvent;
use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;

class UpdateCourseDuration
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
    public function handle(CourseLessonsUpdatedEvent $event): void
    {
        $totalDuration = Lesson::query()
            ->where('course_id', $event->course_id)
            ->where('status', CourseStatus::published)
            ->sum('duration');

        Course::where('id', $event->course_id)
            ->update(['duration' => $totalDuration]);
    }
}
