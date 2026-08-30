<?php

namespace App\Actions;

use Carbon\Carbon;
use App\Models\User;
use Carbon\CarbonInterval;
use App\Models\Certificate;
use App\Models\Courses\Course;
use Illuminate\Support\Facades\App;
use App\Models\Lessons\LessonTracking;

class GenerateCertificate
{
    public function execute(User $user, Course $course)
    {
        // get the sum of duration of the lessons from LessonTracking
        $totalDuration = LessonTracking::query()
            ->join('lessons', 'lesson_trackings.lesson_id', '=', 'lessons.id')
            ->where('lesson_trackings.user_id', $user->id)
            ->where('lesson_trackings.course_id', $course->id)
            ->sum('lessons.duration');

        $certificate = Certificate::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        $interval = CarbonInterval::minutes($totalDuration)->cascade();
        $hours = (int)$interval->totalHours;

        $currentLocale = App::getLocale();
        $currentCarbonLocale = Carbon::getLocale();

        $locale = $course->lang;
        App::setLocale($locale);
        Carbon::setLocale($locale);

        try {
            $course->loadMissing(['tutor', 'tutor.tutorProfile', 'tutor.organization']);

            $data = [
                'title' => $certificate->template_data['course_title'] ?? $course->title,
                'tutor' => [
                    'name' => $course->tutor->tutorProfile->localized_name,
                    'org_logo' => $course->tutor->organization?->logo_path,
                    'stamp' => $course->tutor->organization?->stamp_path ?? public_path('assets/images/signature.png')
                ],
                'user' => $user,
                'date' => Carbon::create($certificate->issued_at)->translatedFormat('M d, Y'),
                'credentialId' => $certificate->certificate_number,
                'verificationUrl' => $certificate->verificationUrl(),
                'hours' => $hours
            ];
            $pdf = (new GeneratePDF)->execute('app.pdf.certificate', $data, 'landscape');

        } finally {
            App::setLocale($currentLocale);
            Carbon::setLocale($currentCarbonLocale);
        }

        return $pdf->stream('certificate.pdf');
    }
}
