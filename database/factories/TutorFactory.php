<?php

namespace Database\Factories;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tutor>
 */
class TutorFactory extends Factory
{
    protected $model = Tutor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specialization' => fake()->words(3, true),
            'experience_years' => fake()->numberBetween(1, 20),
            'hourly_rate' => fake()->randomFloat(2, 20, 200),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
