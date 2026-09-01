<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function toggle(Post $post)
    {
        $bookmark = Bookmark::where(
            'user_id',
            Auth::id()
        )
        ->where(
            'post_id',
            $post->id
        )
        ->first();

        if ($bookmark) {
            $bookmark->delete();

            return back()->with(
                'success',
                'Post removed from bookmarks.'
            );
        }

        Bookmark::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
        ]);

        return back()->with(
            'success',
            'Post bookmarked successfully.'
        );
    }
}