<?php

namespace Database\Factories\Courses;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Courses\Course>
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
            'status' => fake()->randomElement(\App\Enums\CourseStatus::values()),
            'order' => fake()->numberBetween(1, 10),
            'title' => $title,
            'description' => fake()->paragraphs(3, true),
            'objectives' => fake()->text(),
            'lang' => 'en',
            "stripe_price_id" => "price_1Nu9SAITrunN8yW4s2JRXrIi",
            'price' =>  fake()->numberBetween(200, 500),
            'old_price' =>  fake()->numberBetween(600, 800),
        ];
    }
}
