@extends('layouts.app')

@section('title', $post->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <h1>{{ $post->name }}</h1>
                    <p class="mb-1">By {{ $post->user->name }} on {{ $post->created_at->format('M d, Y') }}</p>

                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded mb-3" alt="{{ $post->name }}">
                @endif

                <p class="card-text">{{ $post->body }}</p>

                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>

                    @auth
                        @if(auth()->id() === $post->user_id)
                            <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        @endif
                    @endauth

                    @auth
                        <form action="{{ route('posts.like', $post) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ auth()->user()->likes()->where('post_id', $post->id)->exists() ? 'btn-danger' : 'btn-outline-danger' }}">
                                {{ auth()->user()->likes()->where('post_id', $post->id)->exists() ? 'Unlike' : 'Like' }} ({{ $post->likes->count() }})
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Comments ({{ $post->comments->count() }})</div>
            <div class="card-body">
                @auth
                    <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <textarea name="comment" class="form-control" rows="3" placeholder="Write a comment..." required minlength="3" maxlength="1000"></textarea>
                        </div>
                        <select name="parent_id" class="form-select mb-2">
                            <option value="">No parent comment</option>
                            @foreach($post->comments as $parentComment)
                                <option value="{{ $parentComment->id }}">Reply to: {{ Str::limit($parentComment->comment, 50) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Add Comment</button>
                    </form>
                @endauth

                <div class="list-group">
                    @forelse($post->comments as $comment)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $comment->user->name }}</strong>
                                <small>{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $comment->comment }}</p>

                            @if($comment->replies->count())
                                <div class="list-group mt-2">
                                    @foreach($comment->replies as $reply)
                                        <div class="list-group-item list-group-item-secondary">
                                            <strong>{{ $reply->user->name }}</strong>
                                            <small>{{ $reply->created_at->diffForHumans() }}</small>
                                            <p class="mb-0">{{ $reply->comment }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">No comments yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Post Details</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Likes: {{ $post->likes->count() }}</li>
                <li class="list-group-item">Comments: {{ $post->comments->count() }}</li>
                <li class="list-group-item">Created: {{ $post->created_at->format('Y-m-d H:i') }}</li>
                <li class="list-group-item">Updated: {{ $post->updated_at->format('Y-m-d H:i') }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
