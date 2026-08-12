<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = \App\Models\Post::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(rand(3, 8)),
            'body' => fake()->paragraphs(rand(3, 8), true),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'image' => null,
        ];
    }
}
