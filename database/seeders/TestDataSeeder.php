<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(15)->create();

        $posts = Post::factory(15)->create();

        $posts->each(function ($post) use ($users) {
            Comment::factory(rand(2, 8))->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
            ]);

            $randomUsers = $users->random(rand(2, 10));
            foreach ($randomUsers as $user) {
                Like::factory()->create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
            }
        });

        $posts->each(function ($post) {
            $parentComments = Comment::where('post_id', $post->id)->inRandomOrder()->take(rand(1, 3))->get();
            foreach ($parentComments as $parent) {
                Comment::factory(rand(1, 3))->create([
                    'post_id' => $post->id,
                    'parent_id' => $parent->id,
                ]);
            }
        });
    }
}
