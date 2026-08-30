<?php

namespace App\Listeners;

use App\Enums\CourseEmailType;
use App\Enums\CourseStatus;
use App\Models\User;
use App\Enums\PreferenceKey;
use App\Mail\NewCourseAdded;
use App\Events\CoursePublished;
use App\Models\Courses\Course;
use App\Models\Courses\CourseMailLog;
use App\Models\OrganizationFollower;
use App\Models\Wishlist;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewCoursEmail implements ShouldQueue
{
    public int $delay = 1800;
    public function __construct() {}

    public function handle(CoursePublished $event): void
    {
        $waiters = Wishlist::where('course_id', $event->course->id)->pluck('user_id');

        if ($event->course->organization_id) {
            $followers = OrganizationFollower::whereHas('organization', function ($q) use ($event) {
                return $q->where('organization_id', $event->course->organization_id);
            })->pluck('user_id');
            $waiters = array_merge($waiters->toArray(), $followers->toArray());
        }
        /** @var Collection<int, User> $users */
        $users = User::query()
            ->whereHas('preferences', function ($query) {
                return $query
                    ->where('key', PreferenceKey::UpdateEmail)
                    ->where('value', true);
            })
            ->whereIn('id', $waiters)
            ->get();


        foreach ($users as $user) {
            $course = Course::find($event->course->id);
            // 🔑 Cancel safely BEFORE rendering email
            $alreadySent = CourseMailLog::where('course_id', $course->id)->where('type', CourseEmailType::NewCourse)->where('user_id', $user->id)->exists();
            if (!$course || $course->status !== CourseStatus::published || $alreadySent) {
                // just stop rendering 
                Log::info('Course not found or not published or already sent to user');
                return;
            }
            Mail::to($user)->send(new NewCourseAdded($user, $event->course));
        }
    }
}
