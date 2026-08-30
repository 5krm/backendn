<?php

namespace App\Console\Commands;

use App\Enums\FollowupEmailType;
use App\Mail\CourseSuggestion;
use App\Models\Courses\Course;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

#[Signature('app:send-course-suggestion')]
#[Description('Command description')]
class SendCourseSuggestion extends Command
{
    protected FollowupEmailType $followupEmailType = FollowupEmailType::CourseSuggestion;

    public function handle()
    {
        $users = $this->getUnrolledTrainers();
        $course = $this->getSuggestedCourse();
        foreach ($users as $user) {

            $this->info("Sending followup email to {$user->name} to suggest the course {$course->title}");
            try {
                Mail::send(new CourseSuggestion($user, $course));
            } catch (\Exception $e) {
                $this->error("Failed to send followup email: {$e->getMessage()}");

                continue;
            }

            $this->info('Followup email sent');
            $this->info('Storing followup email');

            $user->followupEmails()->create([
                'course_id' => $course->id,
                'user_id' => $user->id,
                'sent_at' => now(),
                'email_type' => FollowupEmailType::CourseSuggestion,
            ]);
        }
    }

    private function getUnrolledTrainers(): Collection
    {
        return User::forFollowupEmails($this->followupEmailType)
            ->where('created_at', '<=', now()->subDays(2))
            ->whereDoesntHave('courses')
            ->whereDoesntHave('followupEmails', function ($query) {
                $query->where('email_type', $this->followupEmailType->value);
            })
            ->get();
    }

    private function getSuggestedCourse(): Course
    {
        $course = Course::where('title', 'كتابة مقترحات المشاريع الانسانية')->first();
        if (! $course) {
            $course = Course::where('is_free', true)->inRandomOrder()->first();
        }

        return $course;
    }
}
