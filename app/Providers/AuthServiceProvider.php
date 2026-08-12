<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class    => \App\Policies\PostPolicy::class,
        Comment::class => \App\Policies\CommentPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
