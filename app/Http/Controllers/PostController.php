<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Post Listing
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
<<<<<<< HEAD
<<<<<<< HEAD

        //  Post ID 1 fetch karo
        $post = Post::find(1);

        //  Safety check (important)
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found. Please insert post first.'
            ]);

        $query = Post::query()->with(['user', 'likes', 'comments']);
=======
=======
        $query = Post::query()
            ->with([
                'user',
                'likes',
            ])
            ->withCount([
                'comments',
                'topLevelComments',
                'likes',
                'bookmarks',
                'views',
            ]);

>>>>>>> development
        /*
        |--------------------------------------------------------------------------
        | Public users only see published posts
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {
            $query->published();
        } else {
            /*
            | Logged-in users can see:
            | - published posts
            | - their own drafts
            */

            $query->where(function ($query) {
                $query->where('status', 'published')
                    ->orWhere(function ($query) {
                        $query->where('status', 'draft')
                            ->where(
                                'user_id',
                                Auth::id()
                            );
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
>>>>>>> development

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
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if (
            Auth::check() &&
            in_array(
                $request->input('status'),
                ['published', 'draft']
            )
        ) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
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

            case 'most_liked':

                $query->orderBy(
                    'likes_count',
                    'desc'
                );

                break;

            case 'most_viewed':

                $query->orderBy(
                    'views_count',
                    'desc'
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
            ->paginate(5)
            ->withQueryString();

        return view(
            'posts.index',
            compact('posts')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('posts.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'body' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'published',
                ]),
            ],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] =
                $request->file('image')
                    ->store(
                        'posts',
                        'public'
                    );
        }

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
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(Post $post)
    {
        /*
        |--------------------------------------------------------------------------
        | Draft protection
        |--------------------------------------------------------------------------
        */

        if (
            $post->status === 'draft' &&
            (!Auth::check() ||
                Auth::id() !== $post->user_id)
        ) {
            abort(404);
        }

        $post->load([
            'user',
            'likes.user',
            'topLevelComments.user',
            'topLevelComments.replies.user',
        ]);

        $post->loadCount([
            'comments',
            'likes',
            'bookmarks',
            'views',
        ]);

        return view(
            'posts.show',
            compact('post')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
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
    | Update
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

            'body' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'published',
                ]),
            ],
        ]);

        if ($request->hasFile('image')) {

            if ($post->image) {
                Storage::disk('public')
                    ->delete(
                        $post->image
                    );
            }

            $validated['image'] =
                $request->file('image')
                    ->store(
                        'posts',
                        'public'
                    );
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
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(Post $post)
    {
        $this->authorize(
            'delete',
            $post
        );

        if ($post->image) {
            Storage::disk('public')
                ->delete(
                    $post->image
                );
        }

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with(
                'success',
                'Post deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CSV Export
    |--------------------------------------------------------------------------
    */

    public function export(Request $request): StreamedResponse
    {
        $query = Post::query()
            ->withCount([
                'comments',
                'likes',
                'bookmarks',
                'views',
            ])
            ->with('user');

        if (!Auth::check()) {
            $query->published();
        } else {
            $query->where(function ($query) {
                $query->where(
                    'status',
                    'published'
                )
                ->orWhere(function ($query) {
                    $query->where(
                        'status',
                        'draft'
                    )
                    ->where(
                        'user_id',
                        Auth::id()
                    );
                });
            });
        }

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $posts = $query
            ->latest()
            ->get();

        return response()->streamDownload(
            function () use ($posts) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                fputcsv($handle, [
                    'ID',
                    'Post Name',
                    'Author',
                    'Status',
                    'Comments',
                    'Likes',
                    'Bookmarks',
                    'Views',
                    'Created At',
                ]);

                foreach ($posts as $post) {

                    fputcsv($handle, [
                        $post->id,
                        $post->name,
                        $post->user->name ?? '',
                        $post->status,
                        $post->comments_count,
                        $post->likes_count,
                        $post->bookmarks_count,
                        $post->views_count,
                        $post->created_at,
                    ]);
                }

                fclose($handle);
            },
            'posts-export.csv',
            [
                'Content-Type' =>
                    'text/csv',
            ]
        );
    }
}