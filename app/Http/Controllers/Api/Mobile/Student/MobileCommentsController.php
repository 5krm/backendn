<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileCommentsController extends Controller
{
    /**
     * Get public comments for a specific lesson.
     */
    public function index($lessonId)
    {
        $comments = DB::table('comments')
            ->where('lesson_id', $lessonId)
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->select('comments.*', 'users.name as user_name')
            ->orderBy('comments.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments,
        ]);
    }

    /**
     * Store a new public comment for a lesson.
     */
    public function store(Request $request, $lessonId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $userId = Auth::id();

        $commentId = DB::table('comments')->insertGetId([
            'user_id' => $userId,
            'lesson_id' => $lessonId,
            'content' => $request->input('content'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $comment = DB::table('comments')
            ->where('comments.id', $commentId)
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->select('comments.*', 'users.name as user_name')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully',
            'data' => $comment,
        ], 201);
    }
}
