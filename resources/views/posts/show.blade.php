@extends('layouts.app')

@section('title', $post->name)

@section('content')

<div class="row">

    {{-- Main Content --}}
    <div class="col-md-8">

        {{-- Post Card --}}
        <div class="card mb-4">

            <div class="card-body">

                <h1>
                    {{ $post->name }}
                </h1>

                <p class="text-muted mb-3">

                    By
                    <strong>{{ $post->user->name }}</strong>

                    on

                    {{ $post->created_at->format('M d, Y') }}

                </p>


                {{-- Image --}}
                @if($post->image)

                    <img
                        src="{{ asset('storage/' . $post->image) }}"
                        class="img-fluid rounded mb-3"
                        alt="{{ $post->name }}"
                    >

                @endif


                {{-- Body --}}
                <p class="card-text">

                    {{ $post->body }}

                </p>


                {{-- Buttons --}}
                <div class="d-flex gap-2 mt-3 flex-wrap">

                    <a
                        href="{{ route('posts.index') }}"
                        class="btn btn-secondary"
                    >
                        Back
                    </a>


                    @auth

                        @if(auth()->id() === $post->user_id)

                            <a
                                href="{{ route('posts.edit', $post) }}"
                                class="btn btn-warning"
                            >
                                Edit
                            </a>


                            <form
                                action="{{ route('posts.destroy', $post) }}"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this post?')"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    Delete
                                </button>

                            </form>

                        @endif


                        {{-- Like --}}
                        <form
                            action="{{ route('posts.like', $post) }}"
                            method="POST"
                        >

                            @csrf

                            @php
                                $alreadyLiked = auth()
                                    ->user()
                                    ->likes()
                                    ->where('post_id', $post->id)
                                    ->exists();
                            @endphp

                            <button
                                type="submit"
                                class="btn {{ $alreadyLiked ? 'btn-danger' : 'btn-outline-danger' }}"
                            >

                                {{ $alreadyLiked ? 'Unlike' : 'Like' }}

                                ({{ $post->likes->count() }})

                            </button>

                        </form>

                    @endauth

                </div>

            </div>

        </div>


        {{-- Comments --}}
        <div class="card mb-4">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <strong>
                        Comments
                    </strong>

                    <div class="d-flex gap-2">

                        <span class="badge bg-primary">

                            {{ $post->comments_count }}

                            Total Comments

                        </span>

                        <span class="badge bg-secondary">

                            {{ $post->topLevelComments->count() }}

                            Discussions

                        </span>

                    </div>

                </div>

            </div>


            <div class="card-body">

                {{-- Add Comment --}}
                @auth

                    <form
                        action="{{ route('comments.store', $post) }}"
                        method="POST"
                        class="mb-4"
                    >

                        @csrf

                        <div class="mb-3">

                            <label
                                for="comment"
                                class="form-label"
                            >
                                Add a Comment
                            </label>

                            <textarea
                                name="comment"
                                id="comment"
                                class="form-control"
                                rows="3"
                                placeholder="Write a comment..."
                                required
                                minlength="3"
                                maxlength="1000"
                            ></textarea>

                        </div>


                        <div class="mb-3">

                            <label
                                for="parent_id"
                                class="form-label"
                            >
                                Reply To
                            </label>

                            <select
                                name="parent_id"
                                id="parent_id"
                                class="form-select"
                            >

                                <option value="">
                                    New top-level comment
                                </option>

                                @foreach($post->topLevelComments as $parentComment)

                                    <option
                                        value="{{ $parentComment->id }}"
                                    >

                                        Reply to:
                                        {{ Str::limit($parentComment->comment, 60) }}

                                    </option>

                                @endforeach

                            </select>

                            <div class="form-text">

                                Select a comment if you want to reply to it.

                            </div>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Add Comment
                        </button>

                    </form>

                @else

                    <div class="alert alert-info">

                        Please
                        <a href="{{ route('login') }}">
                            login
                        </a>
                        to comment.

                    </div>

                @endauth


                {{-- Top-Level Comments --}}
                <div class="list-group">

                    @forelse($post->topLevelComments as $comment)

                        <div class="list-group-item mb-3">

                            {{-- Comment Header --}}
                            <div class="d-flex justify-content-between">

                                <strong>

                                    {{ $comment->user->name }}

                                </strong>

                                <small class="text-muted">

                                    {{ $comment->created_at->diffForHumans() }}

                                </small>

                            </div>


                            {{-- Comment Body --}}
                            <p class="mt-2 mb-2">

                                {{ $comment->comment }}

                            </p>


                            {{-- Comment Actions --}}
                            @auth

                                @if(auth()->id() === $comment->user_id)

                                    <div class="d-flex gap-2 mb-3">

                                        <form
                                            action="{{ route('comments.update', $comment) }}"
                                            method="POST"
                                            class="d-flex gap-2"
                                        >

                                            @csrf

                                            @method('PUT')

                                            <input
                                                type="text"
                                                name="comment"
                                                value="{{ $comment->comment }}"
                                                class="form-control form-control-sm"
                                                required
                                                minlength="3"
                                                maxlength="1000"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-warning"
                                            >
                                                Update
                                            </button>

                                        </form>


                                        <form
                                            action="{{ route('comments.destroy', $comment) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this comment and its replies?')"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                @endif

                            @endauth


                            {{-- Replies --}}
                            @if($comment->replies->count())

                                <div class="ms-4 mt-3">

                                    <h6 class="text-muted">

                                        Replies
                                        ({{ $comment->replies->count() }})

                                    </h6>


                                    @foreach($comment->replies as $reply)

                                        <div class="card bg-light mb-2">

                                            <div class="card-body">

                                                <div class="d-flex justify-content-between">

                                                    <strong>

                                                        {{ $reply->user->name }}

                                                    </strong>

                                                    <small class="text-muted">

                                                        {{ $reply->created_at->diffForHumans() }}

                                                    </small>

                                                </div>


                                                <p class="mt-2 mb-2">

                                                    {{ $reply->comment }}

                                                </p>


                                                {{-- Reply Actions --}}
                                                @auth

                                                    @if(auth()->id() === $reply->user_id)

                                                        <div class="d-flex gap-2">

                                                            <form
                                                                action="{{ route('comments.update', $reply) }}"
                                                                method="POST"
                                                                class="d-flex gap-2"
                                                            >

                                                                @csrf

                                                                @method('PUT')

                                                                <input
                                                                    type="text"
                                                                    name="comment"
                                                                    value="{{ $reply->comment }}"
                                                                    class="form-control form-control-sm"
                                                                    required
                                                                    minlength="3"
                                                                    maxlength="1000"
                                                                >

                                                                <button
                                                                    type="submit"
                                                                    class="btn btn-sm btn-warning"
                                                                >
                                                                    Update
                                                                </button>

                                                            </form>


                                                            <form
                                                                action="{{ route('comments.destroy', $reply) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Delete this reply?')"
                                                            >

                                                                @csrf

                                                                @method('DELETE')

                                                                <button
                                                                    type="submit"
                                                                    class="btn btn-sm btn-danger"
                                                                >
                                                                    Delete
                                                                </button>

                                                            </form>

                                                        </div>

                                                    @endif

                                                @endauth

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <small class="text-muted">

                                    No replies yet.

                                </small>

                            @endif

                        </div>

                    @empty

                        <div class="alert alert-light">

                            No comments yet.

                            Be the first to comment!

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


    {{-- Sidebar --}}
    <div class="col-md-4">

        <div class="card">

            <div class="card-header">

                Post Details

            </div>


            <ul class="list-group list-group-flush">

                <li class="list-group-item">

                    Author:
                    <strong>
                        {{ $post->user->name }}
                    </strong>

                </li>


                <li class="list-group-item">

                    Likes:
                    <strong>
                        {{ $post->likes->count() }}
                    </strong>

                </li>


                <li class="list-group-item">

                    Total Comments:
                    <strong>
                        {{ $post->comments_count }}
                    </strong>

                </li>


                <li class="list-group-item">

                    Top-Level Discussions:
                    <strong>
                        {{ $post->topLevelComments->count() }}
                    </strong>

                </li>


                <li class="list-group-item">

                    Created:
                    {{ $post->created_at->format('Y-m-d H:i') }}

                </li>


                <li class="list-group-item">

                    Updated:
                    {{ $post->updated_at->format('Y-m-d H:i') }}

                </li>

            </ul>

        </div>

    </div>

</div>

@endsection