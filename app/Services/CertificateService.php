<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Models\User;
use App\Notifications\CertificateIssuedNotification;
use Illuminate\Support\Facades\DB;

class CertificateService
{
    private const VERIFICATION_CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private const VERIFICATION_CODE_LENGTH = 12;

    public function issueCertificate(User $student, Course $course, float $score): Certificate
    {
        return DB::transaction(function () use ($student, $course, $score) {
            $existingCertificate = Certificate::query()
                ->where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingCertificate) {
                return $existingCertificate;
            }

            $certificate = Certificate::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'tutor_id' => $course->tutor_id,
                'certificate_number' => $this->generateCertificateNumber(),
                'verification_code' => $this->generateVerificationCode(),
                'status' => Certificate::STATUS_VALID,
                'issued_at' => now(),
                'completed_at' => now(),
                'score' => $score,
                'template_data' => [
                    'course_title' => $course->title,
                    'student_name' => $student->name,
                    'completion_date' => now()->format('Y-m-d'),
                    'score' => $score,
                ],
            ]);

            if (isset($course->tutor)) {
                $course->tutor->notify(
                    new CertificateIssuedNotification($certificate, $course, $student)
                );
            }

            return $certificate;
        });
    }

    private function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(uniqid());
        } while (Certificate::query()->where('certificate_number', $number)->exists());

        return $number;
    }

    private function generateVerificationCode(): string
    {
        do {
            $code = '';

            for ($i = 0; $i < self::VERIFICATION_CODE_LENGTH; $i++) {
                $code .= self::VERIFICATION_CODE_ALPHABET[random_int(0, strlen(self::VERIFICATION_CODE_ALPHABET) - 1)];
            }
        } while (Certificate::query()->where('verification_code', $code)->exists());

        return $code;
    }
}
