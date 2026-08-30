<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Certificate $certificate,
        public Course $course,
        public User $student
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'certificate_issued',
            'message' => "Certificate issued to {$this->student->name} for course: {$this->course->title} (Score: {$this->certificate->score}%)",
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'student_name' => $this->student->name,
            'student_id' => $this->student->id,
            'score' => $this->certificate->score,
            'icon' => 'heroicon-o-trophy',
            'color' => 'success',
        ];
    }
}
