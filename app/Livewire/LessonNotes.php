<?php

namespace App\Livewire;

use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonNote;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Component;

class LessonNotes extends Component
{
    public $color = '#fde68a';

    #[Rule('required')]
    public $title = '';

    #[Rule('required')]
    public $note = '';

    public $lessonKey = 0;

    public function mount()
    {
        $this->lessonKey = Route::current()->parameter('lesson');
    }

    public function render()
    {
        return view('livewire.lesson-notes');
    }

    #[Computed]
    public function notes()
    {
        /** @var User */
        $user = auth()->user();
        $notes = $user->notes()->whereHas('lesson', fn ($q) => $q->where('public_key', $this->lessonKey))->get();

        return $notes;
    }

    public function save()
    {
        $lessonId = Lesson::where('public_key', $this->lessonKey)->first()->id;
        $this->validate();

        /** @var User */
        $user = auth()->user();
        $user->notes()->create([
            'lesson_id' => $lessonId,
            'title' => $this->title,
            'note' => $this->note,
            'color' => $this->color,
        ]);

        $this->title = '';
        $this->color = '#fde68a';
        $this->note = '';
        $this->dispatch('reset-note');
    }

    public function delete(LessonNote $note)
    {
        if ($note->user_id != auth()->id()) {
            return response(false);
        }

        $note->delete();
    }
}
