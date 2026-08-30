<?php

namespace App\Console\Commands;

use App\Enums\FollowupEmailType;
use App\Mail\ExamReminder;
use App\Models\User;
use App\Traits\HasFollowupEmailCheck;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendExamReminderEmail extends Command
{
    use HasFollowupEmailCheck;

    protected $signature = 'app:send-exam-reminder-email';

    protected $description = 'Sends exam reminder emails to users who completed a course but have not passed the exam';

    protected FollowupEmailType $followupEmailType = FollowupEmailType::ExamReminder;

    public function handle()
    {
        $users = $this->getUsers();
        foreach ($users as [$user, $course, $lastEmail, $sentEmailCount]) {
            $finishingDate = $course->pivot->updated_at;
            if (! $this->isTimeToSend($finishingDate, $lastEmail, $sentEmailCount)) {
                continue;
            }

            $this->info("Sending exam reminder email to {$user->name}");
            $msg = Mail::to($user)->send(new ExamReminder($user, $course));
            if (! $msg) {
                $this->error('Failed to send exam reminder email');

                continue;
            }

            $this->info('Exam reminder email sent');
            $this->info('Storing exam reminder email');

            $user->followupEmails()->create([
                'course_id' => $course->id,
                'user_id' => $user->id,
                'sent_at' => now(),
                'email_type' => FollowupEmailType::ExamReminder,
            ]);
        }
    }

    private function getUsers(): Collection
    {
        return User::forFollowupEmails($this->followupEmailType)
            ->whereHas('courses', fn ($q) => $q->where('progress', 100)->where('passed_at', null))
            ->get()
            ->flatMap(function (User $user) {
                return $user->courses->map(function ($course) use ($user) {
                    $lastEmail = $user->followupEmails
                        ->where('course_id', $course->id)
                        ->sortByDesc('sent_at')
                        ->first();

                    return [
                        $user,
                        $course,
                        $lastEmail,
                        $user->followupEmails
                            ->where('course_id', $course->id)
                            ->count(),
                    ];
                });
            });
    }
}
