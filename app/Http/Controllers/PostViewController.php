<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Support\Facades\Auth;

class PostViewController extends Controller
{
    public function store(Post $post)
    {
        PostView::firstOrCreate([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
        ]);

        return response()->json([
            'success' => true,
            'views_count' => $post->views()->count(),
        ]);
    }
}