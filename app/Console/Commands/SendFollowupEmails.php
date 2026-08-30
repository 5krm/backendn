<?php

namespace App\Console\Commands;

use App\Enums\FollowupEmailType;
use App\Mail\CourseFollowUp;
use App\Models\User;
use App\Traits\HasFollowupEmailCheck;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendFollowupEmails extends Command
{
    use HasFollowupEmailCheck;

    protected $signature = 'app:send-followup-emails';

    protected $description = 'Sends followup emails to users';

    protected FollowupEmailType $followupEmailType = FollowupEmailType::LessonTracking;

    public function handle()
    {
        $users = $this->getUsers();
        foreach ($users as [$user, $course, $lastLesson, $lastEmail, $sentEmailCount]) {
            $lastLessonDate = $lastLesson->pivot->updated_at;
            if (! $this->isTimeToSend($lastLessonDate, $lastEmail, $sentEmailCount)) {
                continue;
            }

            $this->info("Sending followup email to {$user->name} about current lesson");
            $msg = Mail::send(new CourseFollowUp($user, $course, $lastLesson));
            if (! $msg) {
                $this->error('Failed to send followup email');

                continue;
            }

            $this->info('Followup email sent');
            $this->info('Storing followup email');

            $user->followupEmails()->create([
                'course_id' => $course->id,
                'user_id' => $user->id,
                'sent_at' => now(),
                'email_type' => FollowupEmailType::LessonTracking,
            ]);
        }
    }

    private function getUsers(): Collection
    {
        return User::forFollowupEmails($this->followupEmailType)
            ->with(['lessons', 'courses'])
            ->whereHas('courses')
            ->get()
            ->flatMap(function (User $user) {
                return $user->courses->map(function ($course) use ($user) {
                    $lastLesson = $user->lessons
                        ->where('course_id', $course->id)
                        ->sortByDesc('pivot.created_at')
                        ->first();
                    if (! isset($lastLesson) || $course->pivot->progress < 100) {
                        return null;
                    }

                    $lastEmail = $user->followupEmails
                        ->where('course_id', $course->id)
                        ->sortByDesc('sent_at')
                        ->first();

                    return [
                        $user,
                        $course,
                        $lastLesson,
                        $lastEmail,
                        $user->followupEmails
                            ->where('course_id', $course->id)
                            ->count(),
                    ];
                })->filter();
            });
    }
}
