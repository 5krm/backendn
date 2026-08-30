<?php

namespace App\Listeners;

use App\Enums\CourseEmailType;
use App\Events\LessonTrackingEvent;
use App\Jobs\SendCourseEmailJob;
use App\Models\Courses\CourseMail;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\LessonTracking;
use App\Models\User;
use Carbon\Carbon;

class SetLessonTracking
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
    public function handle(LessonTrackingEvent $event): void
    {
        /** @var LessonTracking */
        $tracking = $event->tracking;
        $tracking->load('course.publishedLessons');
        $tracking->completed_at = Carbon::now();
        $tracking->save();

        $finished_conditions = [['course_id', $tracking->course_id], ['user_id', $tracking->user_id], ['completed_at', '!=', null]];
        $with_siblings = LessonTracking::where($finished_conditions)->count();
        $enrollment = Enrollment::where('course_id', $tracking->course_id)->where('user_id', $tracking->user_id)->first();

        // Use published lessons count instead of all lessons
        $totalLessons = $tracking->course->publishedLessons->count();
        if ($totalLessons > 0) {
            $enrollment->progress = 100 * $with_siblings / $totalLessons;
            if ($enrollment->progress >= 50 && $enrollment->progress < 70) {
                $this->sendCourseHalfwayEmail($tracking->course_id, $tracking->user_id);
            }
        }
        $enrollment->save();
    }

    public function sendCourseHalfwayEmail($courseId, $userId)
    {
        $user = User::find($userId);
        $mail = CourseMail::where('course_id', $courseId)
            ->where('type', CourseEmailType::halfway)
            ->where('active', true)->first();

        if ($mail != null) {
            SendCourseEmailJob::dispatch($user, $mail);
        }
    }
}
