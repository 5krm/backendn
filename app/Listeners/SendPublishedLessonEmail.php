<?php

namespace App\Listeners;

use App\Enums\CourseEmailType;
use App\Enums\CourseStatus;
use App\Enums\PreferenceKey;
use App\Events\LessonPublished;
use App\Mail\PublishedLesson;
use App\Models\Courses\CourseMailLog;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\Lesson;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPublishedLessonEmail implements ShouldQueue
{
    public int $delay = 1800;

    public function __construct() {}

    public function handle(LessonPublished $event): void
    {
        $users = Enrollment::where('course_id', $event->lesson->course_id)->pluck('user_id');

        /** @var Collection<int, User> $users */
        $users = User::query()
            ->whereHas('preferences', function ($query) {
                return $query
                    ->where('key', PreferenceKey::UpdateEmail)
                    ->where('value', true);
            })
            ->whereIn('id', $users)
            ->get();

        foreach ($users as $user) {
            $lesson = Lesson::whereHas('course')->find($event->lesson->id);
            // 🔑 Cancel safely BEFORE mailing to the user
            $alreadySent = CourseMailLog::where('course_id', $lesson->course_id)
                ->where('type', CourseEmailType::PublishedLesson)
                ->where('record_id', $lesson->id)
                ->where('user_id', $user->id)->exists();

            if (! $lesson || ($lesson->status !== CourseStatus::published) || $alreadySent) {
                Log::info('Lesson not found or not published or already sent to user');

                return;
            }
            Mail::to($user)->send(
                new PublishedLesson($user, $event->lesson)
            );
        }
    }
}
