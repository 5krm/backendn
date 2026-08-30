<?php

namespace App\Livewire;

use Livewire\Component;
use App\Events\NewCommentPosted;
use App\Models\Lessons\LessonComment\Comment;

class Comments extends Component
{
    public int $lesson_id;
    public $content = '';

    public function mount(int $lesson_id)
    {
        $this->lesson_id = $lesson_id;
    }

    public function getCommentsProperty()
    {
        return Comment::query()
            ->where('lesson_id', $this->lesson_id)
            ->with(['user.media', 'children.user'])
            ->whereNull('parent_id')
            ->withCount('children')
            ->withCount('children')
            ->get();
    }

    public function render()
    {
        return view('livewire.comments');
    }

    public function postComment(): void
    {
        $this->validate([
            'content' => 'required|string|min:1',
        ]);

        $comment = Comment::create([
            'content' => $this->content,
            'user_id' => auth()->id(),
            'lesson_id' => $this->lesson_id
        ]);

        $this->content = '';
        $this->dispatch('comment-form-reset');
        event(new NewCommentPosted($comment));
    }

    public function deleteComment(Comment $comment): void
    {
        $this->authorize('destroy', $comment);
        $comment->delete();
    }
}
