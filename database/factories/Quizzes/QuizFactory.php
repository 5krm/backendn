<?php

namespace Database\Factories\Quizzes;

use App\Models\Quizzes\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(5),
            'order' => $this->faker->numberBetween(1, 50),
        ];
    }
}
