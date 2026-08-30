<?php

namespace App\Livewire;

use App\Events\NewReplyPosted;
use App\Models\Lessons\LessonComment\Comment as CommentModel;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Comment extends Component
{
    public CommentModel $comment;

    public $isReplying = false;

    public $hasReplies = false;

    public $isEditing = false;

    public $readonly = false;

    #[Rule('required')]
    public string $reply;

    public function render()
    {
        if (! $this->comment->relationLoaded('lesson')) {
            $this->comment->load('lesson');
        }
        if (! $this->comment->relationLoaded('user')) {
            $this->comment->load('user');
        }

        return view('livewire.comment');
    }

    #[On('refresh')]
    public function postReply(): void
    {
        if (! $this->comment->isParent()) {
            return;
        }

        $this->validateOnly('reply');

        $reply = $this->comment->children()->make();
        $reply->content = $this->reply;
        $reply->user()->associate(auth()->user());
        $reply->lesson_id = $this->comment->lesson_id;
        $reply->save();
        $this->reset('reply');
        $this->isReplying = false;
        $this->comment->load('children');
        $this->hasReplies = true;
        $this->dispatch('comment-form-reset');
        event(new NewReplyPosted($reply));
    }

    public function updatedIsEditing($isEditing): void
    {
        if (! $isEditing) {
            return;
        }
        $this->reply = $this->comment->content;
    }

    #[On('refresh')]
    public function editComment(): void
    {
        $this->authorize('update', $this->comment);
        $this->validateOnly('reply');
        $this->comment->content = $this->reply;
        $this->comment->save();

        $this->isEditing = false;
        $this->reset('reply');
        $this->dispatch('comment-form-reset');
        $this->dispatch('refresh')->self();
    }

    public function deleteComment($id): void
    {
        $comment = CommentModel::with('user')->find($id);
        $this->authorize('destroy', $comment);
        $comment->delete();
    }
}
