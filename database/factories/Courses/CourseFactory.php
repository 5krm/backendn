<?php

namespace Database\Factories\Courses;

use App\Enums\CourseStatus;
use App\Models\Courses\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->name();

        return [
            'slug' => Str::slug($title),
            'duration' => fake()->numberBetween(30, 500),
            'status' => fake()->randomElement(CourseStatus::values()),
            'order' => fake()->numberBetween(1, 10),
            'title' => $title,
            'description' => fake()->paragraphs(3, true),
            'objectives' => fake()->text(),
            'lang' => 'en',
            'stripe_price_id' => 'price_1Nu9SAITrunN8yW4s2JRXrIi',
            'price' => fake()->numberBetween(200, 500),
            'old_price' => fake()->numberBetween(600, 800),
        ];
    }
}
