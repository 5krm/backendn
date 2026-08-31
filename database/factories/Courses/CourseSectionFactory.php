<?php

namespace Database\Factories\Courses;

use App\Models\Courses\CourseSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseSection>
 */
class CourseSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order' => fake()->numberBetween(1, 10),
            'title' => fake()->name(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(200, 1000),
        ];
    }
}
