<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Store Comment / Reply
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Post $post
    ) {
        $validated = $request->validate([
            'comment' =>
                'required|string|min:3|max:1000',

            'parent_id' =>
                'nullable|exists:comments,id',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate Parent Comment
        |--------------------------------------------------------------------------
        |
        | A reply must belong to the same post.
        |
        */

        if (!empty($validated['parent_id'])) {

            $parentComment = Comment::query()
                ->where(
                    'id',
                    $validated['parent_id']
                )
                ->where(
                    'post_id',
                    $post->id
                )
                ->first();

            if (!$parentComment) {

                return back()
                    ->withErrors([
                        'parent_id' =>
                            'The selected parent comment does not belong to this post.',
                    ])
                    ->withInput();
            }

            /*
            |--------------------------------------------------------------------------
            | Prevent Reply To A Reply
            |--------------------------------------------------------------------------
            |
            | This keeps the hierarchy:
            |
            | Comment
            |   └── Reply
            |
            | instead of:
            |
            | Comment
            |   └── Reply
            |        └── Reply
            |
            */

            if ($parentComment->parent_id !== null) {

                return back()
                    ->withErrors([
                        'parent_id' =>
                            'You can only reply to a top-level comment.',
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Assign Relationships
        |--------------------------------------------------------------------------
        */

        $validated['user_id'] = Auth::id();

        $validated['post_id'] = $post->id;

        /*
        |--------------------------------------------------------------------------
        | Create Comment
        |--------------------------------------------------------------------------
        */

        Comment::create($validated);

        return back()->with(
            'success',
            empty($validated['parent_id'])
                ? 'Comment added successfully.'
                : 'Reply added successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Comment
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Comment $comment
    ) {
        $this->authorize(
            'update',
            $comment
        );

        $validated = $request->validate([
            'comment' =>
                'required|string|min:3|max:1000',
        ]);

        $comment->update($validated);

        return back()->with(
            'success',
            'Comment updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Comment
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Comment $comment
    ) {
        $this->authorize(
            'delete',
            $comment
        );

        $comment->delete();

        return back()->with(
            'success',
            'Comment deleted successfully.'
        );
    }
}