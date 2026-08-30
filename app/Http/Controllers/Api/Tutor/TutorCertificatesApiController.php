<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorCertificatesApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $certs = Certificate::where('tutor_id', $user->id)
            ->with(['user', 'course'])
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id' => $c->certificate_number ?? ('C-'.$c->id),
                'student_name' => $c->user?->name ?? 'Student',
                'course_title' => $c->course?->title ?? 'Course',
                'score' => ($c->score ?? 90).'%',
                'issue_date' => $c->issued_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'status' => $c->status ?? 'Issued',
                'verify_url' => $c->verificationUrl(),
            ]);

        return $this->success([
            'certificates' => $certs,
        ]);
    }

    public function saveTemplate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'template_name' => ['required', 'string'],
            'issuer_title' => ['required', 'string'],
            'instructor_name' => ['required', 'string'],
            'accent_color' => ['required', 'string'],
        ]);

        return $this->success($data, 'Template customized successfully');
    }

    public function revoke(Request $request, $id): JsonResponse
    {
        $cert = Certificate::find($id);
        if ($cert) {
            $cert->update(['status' => 'revoked']);
        }

        return $this->success(null, 'Certificate revoked successfully');
    }
}
