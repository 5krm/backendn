<?php

namespace Database\Factories\Courses;

use App\Models\Courses\CourseTestimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseTestimonial>
 */
class CourseTestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'job_title' => fake()->name(),
            'content' => fake()->paragraph(),
        ];
    }
}
