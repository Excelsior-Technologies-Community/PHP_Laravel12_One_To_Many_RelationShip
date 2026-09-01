<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Support\Facades\Auth;

class CommentLikeController extends Controller
{
    public function toggle(Comment $comment)
    {
        $existing = CommentLike::where(
            'user_id',
            Auth::id()
        )
        ->where(
            'comment_id',
            $comment->id
        )
        ->first();

        if ($existing) {
            $existing->delete();

            return back()->with(
                'success',
                'Comment unliked.'
            );
        }

        CommentLike::create([
            'user_id' => Auth::id(),
            'comment_id' => $comment->id,
        ]);

        return back()->with(
            'success',
            'Comment liked.'
        );
    }
}