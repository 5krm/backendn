<?php

namespace App\Console\Commands;

use App\Enums\FollowupEmailType;

use App\Mail\CourseRatingMail;
use App\Models\Courses\CourseRating;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

#[Signature('app:send-course-rating-reminder')]
#[Description('Email passed students to rate their courses')]
class SendCourseRatingReminder extends Command
{
    protected FollowupEmailType $followupEmailType = FollowupEmailType::CourseRating;

    public function handle()
    {
        $users = $this->getUsers();
        $receivers = collect();
        foreach ($users as [$user, $course, $hasBeenSent]) {
            $finishingDate = $course->pivot->updated_at;

            if (CourseRating::where('course_id', $course->id)->where('user_id', $user->id)->exists() || $hasBeenSent) {
                continue;
            }
            $receivers->push([$user->id, $course->id]);
            $msg = Mail::to($user)->send(new CourseRatingMail($user, $course));
            if (! $msg) {
                $this->error('Failed to send course-rating reminder email');
                continue;
            }

            $user->followupEmails()->create([
                'course_id' => $course->id,
                'user_id' => $user->id,
                'sent_at' => now(),
                'email_type' => FollowupEmailType::CourseRating,
            ]);
        }
        // dd($receivers);
    }

    private function getUsers(): Collection
    {
        // getting students who have passed courses &  not rated their courses
        return User::forFollowupEmails($this->followupEmailType)
            ->whereHas('courses', fn($q) => $q->where('progress', 100)->whereNotNull('passed_at'))
            ->with('courses', fn($q) => $q->where('progress', 100)->whereNotNull('passed_at'))
            ->get()
            ->flatMap(function (User $user) {
                return $user->courses->map(function ($course) use ($user) {
                    $hasBeenSent = $user->followupEmails()
                        ->where('course_id', $course->id)
                        ->where('email_type', FollowupEmailType::CourseRating)
                        ->exists();
                        if($course)

                    return [
                        $user,
                        $course,
                        $hasBeenSent,
                    ];
                });
            });
    }
}
