<?php

namespace App\ViewModels\Quizzes;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Wireable;

class QuizView implements Wireable
{
    public function __construct(
        public int $id,
        public string $question,
        public Collection $quizOptions,
        public ?bool $is_correct,
    ) {}

    public function toLivewire()
    {
        return [
            'is_correct' => $this->is_correct,
            'question' => $this->question,
            'id' => $this->id,
            'quizOptions' => $this->quizOptions,
        ];
    }

    public static function fromLivewire($value)
    {
        return new static(
            id: $value['id'],
            question: $value['question'],
            quizOptions: $value['quizOptions'],
            is_correct: $value['is_correct']
        );
    }
}
