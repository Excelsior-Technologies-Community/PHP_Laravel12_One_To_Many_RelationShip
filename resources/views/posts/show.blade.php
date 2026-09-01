@extends('layouts.app')

@section('title', $post->name)

@section('content')

<div class="container">


{{-- =========================================================
     POST HEADER
========================================================== --}}

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h1 class="mb-1">
            {{ $post->name }}
        </h1>

        <div class="text-muted">

            By
            <strong>
                {{ $post->user->name ?? 'Unknown User' }}
            </strong>

            ·

            {{ $post->created_at->format('d M Y, h:i A') }}

        </div>

    </div>


    {{-- Back Button --}}

    <a
        href="{{ route('posts.index') }}"
        class="btn btn-outline-secondary"
    >
        ← Back
    </a>

</div>





{{-- =========================================================
     POST CARD
========================================================== --}}

<div class="card mb-4">

    {{-- Post Image --}}

    @if($post->image)

        <div class="text-center p-3">

            <img
                src="{{ asset('storage/' . $post->image) }}"
                alt="{{ $post->name }}"
                class="img-fluid rounded"
                style="max-height: 450px;"
            >

        </div>

    @endif


    <div class="card-body">

        {{-- Status --}}

        <div class="mb-3">

            @if($post->status === 'published')

                <span class="badge bg-success">
                    Published
                </span>

            @else

                <span class="badge bg-warning text-dark">
                    Draft
                </span>

            @endif

        </div>


        {{-- Post Body --}}

        <div class="mb-4">

            {!! nl2br(e($post->body)) !!}

        </div>


        {{-- =================================================
             POST STATISTICS
        ================================================== --}}

        <div class="d-flex flex-wrap gap-2 mb-4">

            {{-- Likes --}}

            <span class="badge bg-danger fs-6">

                ❤️
                {{ $post->likes_count ?? $post->likes->count() }}
                Likes

            </span>


            {{-- Comments --}}

            <span class="badge bg-info fs-6">

                💬
                {{ $post->comments_count ?? $post->comments->count() }}
                Comments

            </span>


            {{-- Bookmarks --}}

            <span class="badge bg-warning text-dark fs-6">

                🔖
                {{ $post->bookmarks_count ?? $post->bookmarks->count() }}
                Bookmarks

            </span>


            {{-- Views --}}

            <span class="badge bg-dark fs-6">

                👁️
                {{ $post->views_count ?? $post->views->count() }}
                Views

            </span>

        </div>


        {{-- =================================================
             POST ACTIONS
        ================================================== --}}

        <div class="d-flex flex-wrap gap-2">

            @auth

                {{-- Like --}}

                <form
                    method="POST"
                    action="{{ route('posts.like', $post) }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-outline-danger"
                    >
                        ❤️ Like
                    </button>

                </form>


                {{-- Bookmark --}}

                <form
                    method="POST"
                    action="{{ route('posts.bookmark', $post) }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-outline-warning"
                    >
                        🔖 Bookmark
                    </button>

                </form>


                {{-- Edit Post --}}

                @if(auth()->id() === $post->user_id)

                    <a
                        href="{{ route('posts.edit', $post) }}"
                        class="btn btn-warning"
                    >
                        ✏️ Edit
                    </a>


                    {{-- Delete Post --}}

                    <form
                        method="POST"
                        action="{{ route('posts.destroy', $post) }}"
                        onsubmit="return confirm('Are you sure you want to delete this post?')"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            🗑️ Delete
                        </button>

                    </form>

                @endif

            @else

                <a
                    href="{{ route('login') }}"
                    class="btn btn-outline-primary"
                >
                    Login to Like or Bookmark
                </a>

            @endauth

        </div>

    </div>

</div>


{{-- =========================================================
     COMMENTS SECTION
========================================================== --}}

<div class="card mb-4">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                Comments
            </h4>

            <span class="badge bg-primary">
                {{ $post->comments_count ?? $post->comments->count() }}
            </span>

        </div>

    </div>


    <div class="card-body">


        {{-- =================================================
             ADD COMMENT
        ================================================== --}}

        @auth

            <div class="mb-4">

                <h5>
                    Add a Comment
                </h5>

                <form
                    method="POST"
                    action="{{ route('comments.store', $post) }}"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="parent_id"
                        value=""
                    >

                    <div class="mb-3">

                        <textarea
                            name="comment"
                            class="form-control"
                            rows="4"
                            placeholder="Write your comment..."
                            required
                            minlength="3"
                            maxlength="1000"
                        >{{ old('comment') }}</textarea>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        💬 Add Comment
                    </button>

                </form>

            </div>

            <hr>

        @else

            <div class="alert alert-info">

                Please
                <a href="{{ route('login') }}">
                    login
                </a>
                to add a comment.

            </div>

        @endauth


        {{-- =================================================
             TOP LEVEL COMMENTS
        ================================================== --}}

        @forelse($post->topLevelComments as $comment)

            <div
                class="border rounded p-3 mb-3"
                id="comment-{{ $comment->id }}"
            >

                {{-- Comment Header --}}

                <div class="d-flex justify-content-between">

                    <div>

                        <strong>
                            {{ $comment->user->name ?? 'Unknown User' }}
                        </strong>

                        <small class="text-muted ms-2">

                            {{ $comment->created_at->diffForHumans() }}

                        </small>

                    </div>

                </div>


                {{-- Comment Text --}}

                <div class="mt-2 mb-3">

                    {!! nl2br(e($comment->comment)) !!}

                </div>


                {{-- =================================================
                     COMMENT ACTIONS
                ================================================== --}}

                <div class="d-flex flex-wrap gap-2">


                    {{-- Comment Like --}}

                    @auth

                        <form
                            method="POST"
                            action="{{ route('comments.like', $comment) }}"
                            class="d-inline"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-primary"
                            >

                                👍 Like

                                ({{ $comment->likes->count() }})

                            </button>

                        </form>


                        {{-- Reply Button --}}

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            onclick="showReplyForm({{ $comment->id }})"
                        >
                            ↩️ Reply
                        </button>


                        {{-- Edit Comment --}}

                        @if(auth()->id() === $comment->user_id)

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-warning"
                                onclick="showEditComment({{ $comment->id }})"
                            >
                                ✏️ Edit
                            </button>


                            {{-- Delete Comment --}}

                            <form
                                method="POST"
                                action="{{ route('comments.destroy', $comment) }}"
                                class="d-inline"
                                onsubmit="return confirm('Delete this comment?')"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                >
                                    🗑️ Delete
                                </button>

                            </form>

                        @endif

                    @endauth

                </div>


                {{-- =================================================
                     EDIT COMMENT FORM
                ================================================== --}}

                @auth

                    @if(auth()->id() === $comment->user_id)

                        <div
                            id="edit-comment-{{ $comment->id }}"
                            class="mt-3"
                            style="display:none;"
                        >

                            <form
                                method="POST"
                                action="{{ route('comments.update', $comment) }}"
                            >

                                @csrf

                                @method('PUT')

                                <div class="mb-2">

                                    <textarea
                                        name="comment"
                                        class="form-control"
                                        rows="3"
                                        required
                                        minlength="3"
                                        maxlength="1000"
                                    >{{ $comment->comment }}</textarea>

                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-success"
                                >
                                    Update
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-secondary"
                                    onclick="hideEditComment({{ $comment->id }})"
                                >
                                    Cancel
                                </button>

                            </form>

                        </div>

                    @endif

                @endauth


                {{-- =================================================
                     REPLY FORM
                ================================================== --}}

                @auth

                    <div
                        id="reply-form-{{ $comment->id }}"
                        class="mt-3"
                        style="display:none;"
                    >

                        <form
                            method="POST"
                            action="{{ route('comments.store', $post) }}"
                        >

                            @csrf

                            <input
                                type="hidden"
                                name="parent_id"
                                value="{{ $comment->id }}"
                            >

                            <div class="mb-2">

                                <textarea
                                    name="comment"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Write a reply..."
                                    required
                                    minlength="3"
                                    maxlength="1000"
                                ></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-sm btn-primary"
                            >
                                Reply
                            </button>

                            <button
                                type="button"
                                class="btn btn-sm btn-secondary"
                                onclick="hideReplyForm({{ $comment->id }})"
                            >
                                Cancel
                            </button>

                        </form>

                    </div>

                @endauth


                {{-- =================================================
                     REPLIES
                ================================================== --}}

                @if($comment->replies->count() > 0)

                    <div class="mt-3 ms-4">

                        <h6 class="text-muted mb-3">

                            Replies
                            ({{ $comment->replies->count() }})

                        </h6>


                        @foreach($comment->replies as $reply)

                            <div
                                class="border-start border-3 ps-3 mb-3"
                                id="comment-{{ $reply->id }}"
                            >

                                <div>

                                    <strong>
                                        {{ $reply->user->name ?? 'Unknown User' }}
                                    </strong>

                                    <small class="text-muted ms-2">

                                        {{ $reply->created_at->diffForHumans() }}

                                    </small>

                                </div>


                                <div class="mt-2 mb-2">

                                    {!! nl2br(e($reply->comment)) !!}

                                </div>


                                {{-- Reply Actions --}}

                                @auth

                                    <div class="d-flex gap-2">


                                        {{-- Reply Like --}}

                                        <form
                                            method="POST"
                                            action="{{ route('comments.like', $reply) }}"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-primary"
                                            >

                                                👍 Like
                                                ({{ $reply->likes->count() }})

                                            </button>

                                        </form>


                                        {{-- Edit Reply --}}

                                        @if(auth()->id() === $reply->user_id)

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-warning"
                                                onclick="showEditComment({{ $reply->id }})"
                                            >
                                                ✏️ Edit
                                            </button>


                                            {{-- Delete Reply --}}

                                            <form
                                                method="POST"
                                                action="{{ route('comments.destroy', $reply) }}"
                                                onsubmit="return confirm('Delete this reply?')"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                >
                                                    🗑️ Delete
                                                </button>

                                            </form>

                                        @endif

                                    </div>


                                    {{-- Edit Reply Form --}}

                                    @if(auth()->id() === $reply->user_id)

                                        <div
                                            id="edit-comment-{{ $reply->id }}"
                                            class="mt-3"
                                            style="display:none;"
                                        >

                                            <form
                                                method="POST"
                                                action="{{ route('comments.update', $reply) }}"
                                            >

                                                @csrf

                                                @method('PUT')

                                                <textarea
                                                    name="comment"
                                                    class="form-control mb-2"
                                                    rows="3"
                                                    required
                                                >{{ $reply->comment }}</textarea>


                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-success"
                                                >
                                                    Update
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    onclick="hideEditComment({{ $reply->id }})"
                                                >
                                                    Cancel
                                                </button>

                                            </form>

                                        </div>

                                    @endif

                                @endauth

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        @empty

            <div class="alert alert-info">

                No comments yet.

                @auth
                    Be the first to comment!
                @else
                    Login to add the first comment.
                @endauth

            </div>

        @endforelse

    </div>

</div>


</div>

{{-- =========================================================
JAVASCRIPT
========================================================== --}}

<script>

    /*
    |--------------------------------------------------------------------------
    | Reply Form
    |--------------------------------------------------------------------------
    */

    function showReplyForm(commentId)
    {
        const form = document.getElementById(
            'reply-form-' + commentId
        );

        if (form) {
            form.style.display = 'block';
        }
    }


    function hideReplyForm(commentId)
    {
        const form = document.getElementById(
            'reply-form-' + commentId
        );

        if (form) {
            form.style.display = 'none';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Comment
    |--------------------------------------------------------------------------
    */

    function showEditComment(commentId)
    {
        const form = document.getElementById(
            'edit-comment-' + commentId
        );

        if (form) {
            form.style.display = 'block';
        }
    }


    function hideEditComment(commentId)
    {
        const form = document.getElementById(
            'edit-comment-' + commentId
        );

        if (form) {
            form.style.display = 'none';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Record Unique Post View
    |--------------------------------------------------------------------------
    */

    @auth

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            fetch(
                "{{ route('posts.view', $post) }}",
                {
                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN':
                            '{{ csrf_token() }}',

                        'Accept':
                            'application/json',

                        'Content-Type':
                            'application/json'
                    },

                    body: JSON.stringify({})
                }
            )
            .then(response => response.json())
            .then(data => {

                if (
                    data.success &&
                    data.views_count !== undefined
                ) {

                    const viewBadges =
                        document.querySelectorAll(
                            '.badge'
                        );

                }

            })
            .catch(error => {
                console.error(
                    'View tracking error:',
                    error
                );
            });

        }
    );

    @endauth

</script>

@endsection
