<?php

namespace App\Http\Controllers;

use App\Actions\GenerateCertificate;
use App\Models\Certificate;
use App\Models\Lessons\LessonTracking;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function show(string $code): View
    {
        $certificate = Certificate::query()
            ->with([
                'user.media',
                'course.media',
                'course.tutor.organization.media',
                'course.tutor.tutorProfile',
            ])
            ->where('verification_code', $code)
            ->first();

        if (! $certificate) {
            return view('certificates.verify', [
                'certificate' => null,
                'state' => 'not_found',
                'hours' => null,
            ]);
        }

        $hours = $this->resolveHours($certificate);

        $state = match (true) {
            $certificate->isRevoked() => 'revoked',
            $certificate->isExpired() => 'expired',
            ! $certificate->isValid() => 'invalid',
            default => 'valid',
        };

        return view('certificates.verify', [
            'certificate' => $certificate,
            'state' => $state,
            'hours' => $hours,
        ]);
    }

    private function resolveHours(Certificate $certificate): ?int
    {
        if (! $certificate->course) {
            return null;
        }

        $totalDuration = LessonTracking::query()
            ->join('lessons', 'lesson_trackings.lesson_id', '=', 'lessons.id')
            ->where('lesson_trackings.user_id', $certificate->user_id)
            ->where('lesson_trackings.course_id', $certificate->course_id)
            ->sum('lessons.duration');

        if ($totalDuration > 0) {
            return (int) CarbonInterval::minutes($totalDuration)->cascade()->totalHours;
        }

        if ($certificate->course->duration > 0) {
            return (int) CarbonInterval::minutes($certificate->course->duration)->cascade()->totalHours;
        }

        return null;
    }


    public function download(Certificate $certificate)
    {
        /** @var User $user */
        $user = auth()->user();

        $course = $certificate->load('course')->course;
        return (new GenerateCertificate())->execute($user, $course);
    }
}
