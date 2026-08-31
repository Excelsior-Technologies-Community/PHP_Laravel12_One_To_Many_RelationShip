<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Post Listing
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Load relationships and comment count
        |--------------------------------------------------------------------------
        */

$query = Post::query()
    ->with([
        'user',
        'likes',
        'comments',
    ])
    ->withCount([
        'comments',
        'topLevelComments',
    ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if ($from = $request->input('from')) {
            $query->filterByDate(
                $from,
                $request->input('to')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | User Filter
        |--------------------------------------------------------------------------
        */

        if ($userId = $request->input('user_id')) {
            $query->filterByUser($userId);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        |
        | latest
        | most_commented
        | least_commented
        |
        */

        $sort = $request->input(
            'sort',
            'latest'
        );

        switch ($sort) {

            case 'most_commented':

                $query->orderBy(
                    'comments_count',
                    'desc'
                );

                break;

            case 'least_commented':

                $query->orderBy(
                    'comments_count',
                    'asc'
                );

                break;

            default:

                $query->latest();

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $posts = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'posts.index',
            compact('posts')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Post Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('posts.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Store Post
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'body' => 'required|string|min:10|max:5000',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            $validated['image'] =
                $request->file('image')
                    ->store('posts', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Assign Logged-In User
        |--------------------------------------------------------------------------
        */

        $validated['user_id'] = Auth::id();

        Post::create($validated);

        return redirect()
            ->route('posts.index')
            ->with(
                'success',
                'Post created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show Post
    |--------------------------------------------------------------------------
    */

    public function show(Post $post)
    {
        /*
        |--------------------------------------------------------------------------
        | Load Post Relationships
        |--------------------------------------------------------------------------
        |
        | Only top-level comments are loaded here.
        | Their replies are then loaded underneath them.
        |
        */

        $post->load([
            'user',
            'likes.user',
            'topLevelComments.user',
            'topLevelComments.replies.user',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Count All Comments + Replies
        |--------------------------------------------------------------------------
        */

        $post->loadCount('comments');

        return view(
            'posts.show',
            compact('post')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Post
    |--------------------------------------------------------------------------
    */

    public function edit(Post $post)
    {
        $this->authorize(
            'update',
            $post
        );

        return view(
            'posts.edit',
            compact('post')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Post
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Post $post
    ) {
        $this->authorize(
            'update',
            $post
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts')
                    ->ignore($post->id),
            ],

            'body' =>
                'required|string|min:10|max:5000',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Replace Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            if ($post->image) {

                Storage::disk('public')
                    ->delete($post->image);
            }

            $validated['image'] =
                $request->file('image')
                    ->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()
            ->route(
                'posts.show',
                $post
            )
            ->with(
                'success',
                'Post updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Post
    |--------------------------------------------------------------------------
    */

    public function destroy(Post $post)
    {
        $this->authorize(
            'delete',
            $post
        );

        /*
        |--------------------------------------------------------------------------
        | Delete Image
        |--------------------------------------------------------------------------
        */

        if ($post->image) {

            Storage::disk('public')
                ->delete($post->image);
        }

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with(
                'success',
                'Post deleted successfully.'
            );
    }
}