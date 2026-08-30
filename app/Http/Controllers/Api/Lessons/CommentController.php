<?php

namespace App\Http\Controllers\Api\Lessons;

use App\Http\Controllers\Controller;
use App\Models\Lessons\LessonComment\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(int $lessonId)
    {
        $comments = Comment::query()
            ->with(['user', 'children'])
            ->whereNull('parent_id')
            ->where('lesson_id', $lessonId)
            ->get()
            ->map(function ($comment) {
                return [
                    ...$this->mapComment($comment),
                    'replies' => $comment->children->map(fn ($c) => $this->mapComment($c)),
                ];
            });

        return response()->json([
            'data' => $comments,
        ]);
    }

    public function reply(int $lessonId, Comment $comment, Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $data['lesson_id'] = $lessonId;
        $reply = $comment->children()->create($data);

        return response()->json([
            'data' => $this->mapComment($reply),
        ]);
    }

    public function delete(int $lessonId, Comment $comment)
    {
        $comment->children()->delete();
        $comment->delete();

        return response()->noContent();
    }

    private function mapComment(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'lesson_id' => $comment->lesson_id,
            'content' => $comment->content,
            'avatar' => $comment->user?->profile,
            'user' => $comment->user?->name ?? 'Admin',
            'created_at' => $comment->created_at->format('M d, Y'),
        ];
    }
}
