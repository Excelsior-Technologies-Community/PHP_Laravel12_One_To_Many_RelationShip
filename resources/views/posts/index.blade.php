@extends('layouts.app')

@section('title', 'Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Posts</h1>
    <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Post</a>
</div>

<form method="GET" action="{{ route('posts.index') }}" class="row g-3 mb-4">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search posts..." value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
    </div>
    <div class="col-md-2">
        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
    </div>
    <div class="col-md-2">
        <input type="number" name="user_id" class="form-control" placeholder="User ID" value="{{ request('user_id') }}">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-secondary w-100">Filter</button>
    </div>
</form>

<div class="list-group">
    @forelse($posts as $post)
        <div class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">
                    <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">
                        {{ $post->name }}
                    </a>
                </h5>
                <small>{{ $post->created_at->diffForHumans() }}</small>
            </div>
            <p class="mb-1 text-muted">{{ Str::limit($post->body, 150) }}</p>
            <div class="d-flex gap-3 mt-2">
                <span class="badge bg-primary">By {{ $post->user->name }}</span>
                <span class="badge bg-success">{{ $post->likes->count() }} Likes</span>
                <span class="badge bg-info">{{ $post->comments->count() }} Comments</span>
                @if($post->image)
                    <span class="badge bg-warning text-dark">Has Image</span>
                @endif

                @auth
                    @if(auth()->id() === $post->user_id)
                        <div class="d-flex gap-1">
                            <a href="{{ route('posts.edit', $post) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <div class="alert alert-info">No posts found.</div>
    @endforelse
</div>

<div class="mt-4">
    {{ $posts->links() }}
</div>
@endsection
