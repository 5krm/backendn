<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Models\Lessons\LessonNote;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;

class NoteCard extends Component
{
    public LessonNote $note;
    #[Rule('required')]
    public $color = '#fde68a';
    #[Rule('required')]
    public  $title = '';

    public $is_collapsed = false;
    public $content = '';

    public function mount($note)
    {
        $this->note = $note;
        $this->title = $note->title;
        error_log('moutning..');
        $this->content = $note->note;
        error_log('content:' . $this->content);

        $this->color = $note->color;
    }

    public function update_note($updated_content)
    {
        $data = ['title' => $this->title, 'content' => $updated_content];
        $validator = Validator::make($data, [
            'title' => 'required',
            'content' => 'required|min:10'
        ]);

        if ($validator->fails()) {
            $this->addError('content', 'The content is too short.');
            return;
        }

        if ($this->note->user_id != auth()->id()) {
            return response(false);
        }
        $this->note->title = $this->title;
        $this->note->note = $updated_content;
        $this->note->save();
        $this->content = $updated_content;
    }

    public function update_color($color)
    {
        if ($this->note->user_id != auth()->id()) {
            return response(false);
        }
        $this->color = $color;
        $this->note->color = $color;
        $this->note->save();
    }
}
