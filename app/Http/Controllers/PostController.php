<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class PostController extends Controller
{
    public function index()
    {
        // 🔹 Post ID 1 fetch karo
        $post = Post::find(1);

        // 🔴 Safety check (important)
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found. Please insert post first.'
            ]);
        }

        //  Comment 1
        $comment1 = new Comment();
        $comment1->comment = "Hi Comment 1";

        //  Comment 2
        $comment2 = new Comment();
        $comment2->comment = "Hi Comment 2";

        //  Save multiple comments
        $post->comments()->saveMany([$comment1, $comment2]);

        return response()->json([
            'status' => true,
            'message' => 'Comments saved successfully',
            'post_id' => $post->id
        ]);
    }
}
