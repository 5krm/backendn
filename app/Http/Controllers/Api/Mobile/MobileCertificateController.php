<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileCertificateController extends Controller
{
    /**
     * Generate a certificate for a course (Mock).
     */
    public function generate(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'certificate_url' => 'https://dummy.com/cert/123.pdf',
        ]);
    }

    /**
     * Verify a certificate.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'certificate_id' => 'required|string',
        ]);

        $certId = $request->input('certificate_id');

        // Mock verification
        $isValid = strlen($certId) > 5;

        return response()->json([
            'status' => 'success',
            'is_valid' => $isValid,
            'message' => $isValid ? 'Certificate is valid.' : 'Certificate is invalid or not found.',
        ]);
    }
}
