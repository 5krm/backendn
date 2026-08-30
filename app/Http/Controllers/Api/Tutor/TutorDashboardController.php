<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TutorDashboardController extends Controller
{
    /**
     * Get the tutor dashboard data.
     */
    public function index(Request $request)
    {
        // Mocking the data for the dashboard as per instructions
        return response()->json([
            'success' => true,
            'data' => [
                'total_earnings' => 12500.50,
                'total_students' => 342,
                'active_courses' => 5,
                'revenue_chart_data' => [
                    ['month' => 'Jan', 'revenue' => 1200],
                    ['month' => 'Feb', 'revenue' => 1500],
                    ['month' => 'Mar', 'revenue' => 2000],
                    ['month' => 'Apr', 'revenue' => 1800],
                    ['month' => 'May', 'revenue' => 2500],
                    ['month' => 'Jun', 'revenue' => 3500.50],
                ],
            ],
        ]);
    }
}
