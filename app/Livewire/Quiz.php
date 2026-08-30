<?php

namespace App\Livewire;

use App\Events\LessonTrackingEvent;
use App\Models\Lessons\Lesson;
use App\Models\Quizzes\Quiz as ModelsQuiz;
use App\ViewModels\Quizzes\QuizView;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Quiz extends Component
{
    #[Locked]
    public $lesson;

    public $answers = [];

    public $show = false;

    public $is_completed = false;

    public $correct_answers;

    public $questions;

    public function mount()
    {

        if (old('answers')) {
            $this->answers = old('answers');
        }

        $data = ModelsQuiz::with('quizOptions')->whereHas('lesson', fn ($q) => $q->where('public_key', $this->lesson['public_key']))->get();

        foreach ($data as $question) {
            $this->correct_answers[$question->id] = $question->quizOptions->where('is_correct', true)->first()?->id;
        }

        $this->questions = $data->map(function ($q) {
            $quizView = new QuizView(id: $q->id, question: $q->question, quizOptions: $q->quizOptions, is_correct: null);

            return $quizView;
        });
    }

    public function render()
    {
        return view('livewire.quiz');
    }

    public function rules()
    {
        return [
            'answers.*' => ['required', 'exists:quiz_options,id'],
        ];
    }

    public function save()
    {
        $this->validate();
        $score = 0;
        foreach ($this->answers as $key => $answer) {
            $question = $this->questions->where('id', $key)->first();
            if ((int) $answer == $this->correct_answers[$key]) {
                $score++;
                $question->is_correct = true;
            } else {

                $question->is_correct = false;
            }
        }
        if ($score == $this->questions->count()) {
            event(new LessonTrackingEvent(Lesson::where('public_key', $this->lesson['public_key'])->first()));
            $this->is_completed = true;

            if (! isset($this->lesson['next'])) {
                $this->dispatch('congratulate-user', ['message' => 'You have successfully completed the quiz!']);
            }
        }

        session()->flash('score', $score);
    }
}
