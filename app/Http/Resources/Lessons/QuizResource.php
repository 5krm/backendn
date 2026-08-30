<?php

namespace App\Http\Resources\Lessons;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'order' => $this->order,
            'lesson_id' => $this->lesson_id,
            'options' => $this->quizOptions ?? []
        ];
    }
}
