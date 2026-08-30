<?php

namespace Database\Factories\Quizzes;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quizzes\QuizOption>
 */
class QuizOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => $this->faker->sentence(5),
            'quiz_id' => $this->faker->numberBetween(1, 3),
            'order' => $this->faker->unique()->randomNumber,
            'is_correct' => $this->faker->numberBetween(0, 1),
        ];
    }
}
