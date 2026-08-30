<?php

namespace App\Jobs;

use App\Enums\PreferenceKey;
use App\Mail\CourseAutoEmail;
use App\Models\Courses\CourseMail;
use App\Models\Courses\CourseMailLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendCourseEmailJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 0;

    public $tries = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $student, public CourseMail $mail)
    {
        $this->onQueue('low');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $alreadySent = CourseMailLog::where('mail_id', $this->mail->id)->where('user_id', $this->student->id)->exists();
        $allowed = $this->student->preferences()->where('key', PreferenceKey::FollowupEmail)->where('value', true)->exists();
        if ($alreadySent || ! $allowed) {
            return;
        }
        Mail::to($this->student->email)->send(new CourseAutoEmail($this->student, $this->mail));

        CourseMailLog::create([
            'mail_id' => $this->mail->id,
            'course_id' => $this->mail->course_id,
            'user_id' => $this->student->id,
        ]);
    }
}
