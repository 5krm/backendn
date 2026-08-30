<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileNotesController extends Controller
{
    /**
     * Get notes for a specific lesson.
     */
    public function index($lessonId)
    {
        $userId = Auth::id();

        $notes = DB::table('lesson_notes')
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->whereNull('deleted_at')
            ->orderBy('seconds', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'lesson_id' => (int) $note->lesson_id,
                    'user_id' => (int) $note->user_id,
                    'title' => $note->title,
                    'content' => $note->note,
                    'note' => $note->note,
                    'video_timestamp' => (int) ($note->seconds ?? 0),
                    'seconds' => (int) ($note->seconds ?? 0),
                    'color' => $note->color,
                    'created_at' => $note->created_at,
                    'updated_at' => $note->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $notes,
        ]);
    }

    /**
     * Store a new note.
     */
    public function store(Request $request, $lessonId)
    {
        $request->validate([
            'content' => 'required_without:note|nullable|string',
            'note' => 'required_without:content|nullable|string',
            'title' => 'nullable|string',
            'video_timestamp' => 'nullable|integer',
            'seconds' => 'nullable|integer',
            'color' => 'nullable|string',
        ]);

        $userId = Auth::id();
        $content = $request->input('content') ?? $request->input('note', '');
        $title = $request->input('title');
        if (empty($title)) {
            $title = mb_strlen($content) > 35 ? mb_substr($content, 0, 35).'...' : ($content ?: 'Note');
        }
        $seconds = $request->input('video_timestamp', $request->input('seconds', 0));
        $color = $request->input('color', '#00CC99');

        $noteId = DB::table('lesson_notes')->insertGetId([
            'user_id' => $userId,
            'lesson_id' => $lessonId,
            'title' => $title,
            'note' => $content,
            'color' => $color,
            'seconds' => $seconds,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $note = DB::table('lesson_notes')->where('id', $noteId)->first();

        $formatted = [
            'id' => $note->id,
            'lesson_id' => (int) $note->lesson_id,
            'user_id' => (int) $note->user_id,
            'title' => $note->title,
            'content' => $note->note,
            'note' => $note->note,
            'video_timestamp' => (int) ($note->seconds ?? 0),
            'seconds' => (int) ($note->seconds ?? 0),
            'color' => $note->color,
            'created_at' => $note->created_at,
            'updated_at' => $note->updated_at,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Note created successfully',
            'data' => $formatted,
        ], 201);
    }

    /**
     * Update an existing note.
     */
    public function update(Request $request, $lessonId, $noteId)
    {
        $request->validate([
            'content' => 'required_without:note|nullable|string',
            'note' => 'required_without:content|nullable|string',
            'title' => 'nullable|string',
            'video_timestamp' => 'nullable|integer',
            'seconds' => 'nullable|integer',
            'color' => 'nullable|string',
        ]);

        $userId = Auth::id();

        $note = DB::table('lesson_notes')
            ->where('id', $noteId)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->first();

        if (! $note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found or unauthorized',
            ], 404);
        }

        $content = $request->input('content') ?? $request->input('note', $note->note);
        $title = $request->input('title', $note->title);
        $seconds = $request->input('video_timestamp', $request->input('seconds', $note->seconds));
        $color = $request->input('color', $note->color);

        DB::table('lesson_notes')
            ->where('id', $noteId)
            ->update([
                'note' => $content,
                'title' => $title,
                'color' => $color,
                'seconds' => $seconds,
                'updated_at' => now(),
            ]);

        $updatedNote = DB::table('lesson_notes')->where('id', $noteId)->first();

        $formatted = [
            'id' => $updatedNote->id,
            'lesson_id' => (int) $updatedNote->lesson_id,
            'user_id' => (int) $updatedNote->user_id,
            'title' => $updatedNote->title,
            'content' => $updatedNote->note,
            'note' => $updatedNote->note,
            'video_timestamp' => (int) ($updatedNote->seconds ?? 0),
            'seconds' => (int) ($updatedNote->seconds ?? 0),
            'color' => $updatedNote->color,
            'created_at' => $updatedNote->created_at,
            'updated_at' => $updatedNote->updated_at,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Note updated successfully',
            'data' => $formatted,
        ]);
    }

    /**
     * Delete a note.
     */
    public function destroy($lessonId, $noteId)
    {
        $userId = Auth::id();

        $deleted = DB::table('lesson_notes')
            ->where('id', $noteId)
            ->where('user_id', $userId)
            ->delete();

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found or unauthorized',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully',
        ]);
    }
}
