<?php

namespace Database\Factories\Lessons;

use App\Enums\CourseStatus;
use App\Models\Lessons\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_key' => Str::uuid(),
            // 'course_id' => fake()->numberBetween(1, 2),
            // 'order' => fake()->numberBetween(1, 10),
            'title' => fake()->paragraph(1),
            'content' => fake()->paragraphs(5, true),
            'duration' => fake()->numberBetween(200, 1000),
            'status' => fake()->randomElement(CourseStatus::values()),
        ];
    }
}
