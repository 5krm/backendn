<?php

namespace App\Services;

use App\Mail\OtpMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate, save, and send an OTP for the given email.
     */
    public function sendOtp(string $email): string
    {
        // Generate a 6-digit random code
        $otpCode = (string) random_int(100000, 999999);

        // Save it to the database (upsert or just insert)
        DB::table('otps')->updateOrInsert(
            ['email' => $email],
            [
                'otp_code' => $otpCode, // or hashed if you prefer, but requirement says otp_code
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Send it via Mailable
        Mail::to($email)->send(new OtpMail($otpCode));

        return $otpCode;
    }
}
