<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileLeaderboardController extends Controller
{
    /**
     * Get a ranked list of students (Mock).
     */
    public function index(Request $request)
    {
        $leaderboard = [
            ['id' => 1, 'name' => 'John Doe', 'points' => 1500, 'rank' => 1],
            ['id' => 2, 'name' => 'Jane Smith', 'points' => 1450, 'rank' => 2],
            ['id' => 3, 'name' => 'Alice Johnson', 'points' => 1300, 'rank' => 3],
            ['id' => 4, 'name' => 'Bob Brown', 'points' => 1200, 'rank' => 4],
            ['id' => 5, 'name' => 'Charlie Davis', 'points' => 1100, 'rank' => 5],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $leaderboard,
        ]);
    }
}
