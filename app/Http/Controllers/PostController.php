<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {

        //  Post ID 1 fetch karo
        $post = Post::find(1);

        //  Safety check (important)
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found. Please insert post first.'
            ]);

        $query = Post::query()->with(['user', 'likes', 'comments']);

        if ($search = $request->input('search')) {
            $query->search($search);

        }

        if ($from = $request->input('from')) {
            $query->filterByDate($from, $request->input('to'));
        }

        if ($userId = $request->input('user_id')) {
            $query->filterByUser($userId);
        }

        $posts = $query->latest()->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'body'   => 'required|string|min:10|max:5000',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $validated['user_id'] = Auth::id();

        Post::create($validated);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $post->load(['user', 'likes.user', 'comments.user', 'comments.replies.user']);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', Rule::unique('posts')->ignore($post->id)],
            'body'   => 'required|string|min:10|max:5000',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
