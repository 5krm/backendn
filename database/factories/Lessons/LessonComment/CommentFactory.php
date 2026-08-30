<?php

namespace Database\Factories\Lessons\LessonComment;


use App\Models\User;
use App\Models\Lessons\LessonComment\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->text,
            'user_id' => User::factory(),
            'lesson_id' => 1,
            'parent_id' => null,
            'created_at' => now()
        ];
    }
}
