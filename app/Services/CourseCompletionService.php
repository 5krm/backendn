<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Enums\CourseEmailType;
use App\Jobs\SendCourseEmailJob;
use App\Models\Courses\CourseMail;

class CourseCompletionService
{
    public function finish_course(Course $course, int|float $score): Certificate
    {
        /** @var User */
        $user = auth()->user();
        $user->courses()->updateExistingPivot($course->id, [
            'passed_at' => Carbon::now(),
            'score' => $score
        ]);

        $this->sendCourseCompletionEmail($course, $user);

        // Generate certificate automatically
        $certificateService = app(\App\Services\CertificateService::class);
        return $certificateService->issueCertificate($user, $course, $score);
    }

    function sendCourseCompletionEmail(Course $course, User $user)
    {
        $mail = CourseMail::where('course_id', $course->id)
            ->where('type', CourseEmailType::completion)
            ->where('active', true)->first();

        if (isset($mail)) {
            SendCourseEmailJob::dispatch($user, $mail);
        }
    }
}
