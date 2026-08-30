<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TutorOrganizationsApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        return $this->success([
            'my_organizations' => [
                [
                    'id' => 'org-1',
                    'name' => 'NGO Digital Learning Academy',
                    'role' => 'Lead Instructor',
                    'joined_date' => 'Oct 2024',
                    'courses' => 8,
                    'students' => 840,
                    'status' => 'Active Partner',
                ],
            ],
            'available_organizations' => [
                [
                    'id' => 'org-2',
                    'name' => 'Global Tech for Good Initiative',
                    'description' => 'Supporting non-profit coding bootcamps and digital literacy worldwide.',
                    'members' => 45,
                ],
                [
                    'id' => 'org-3',
                    'name' => 'Arab Women in Tech Foundation',
                    'description' => 'Empowering youth and females with cutting-edge software engineering skills.',
                    'members' => 120,
                ],
            ],
            'co_tutors' => [
                ['id' => 'tut-1', 'name' => 'Sara Al-Mansoor', 'specialization' => 'UI/UX & Design Systems', 'courses' => 4, 'status' => 'Active'],
                ['id' => 'tut-2', 'name' => 'Tariq Ziyad', 'specialization' => 'Backend & Cloud DevOps', 'courses' => 6, 'status' => 'Active'],
                ['id' => 'tut-3', 'name' => 'Fatima Zahra', 'specialization' => 'Machine Learning & Python', 'courses' => 3, 'status' => 'Pending Invite'],
            ],
        ]);
    }

    public function join(Request $request): JsonResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'string'],
        ]);

        return $this->success(null, 'Membership request submitted successfully to the organization.');
    }

    public function invite(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['nullable', 'string'],
        ]);

        return $this->success(null, "Invitation email sent to {$data['email']}");
    }
}
