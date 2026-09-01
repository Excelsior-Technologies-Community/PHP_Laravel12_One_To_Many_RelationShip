@extends('layouts.app')

@section('title', 'Posts')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <h1>Posts</h1>

    <div class="d-flex gap-2">

        @auth

            <a
                href="{{ route('posts.create') }}"
                class="btn btn-primary"
            >
                Create Post
            </a>

            <a
                href="{{ route('posts.export', request()->query()) }}"
                class="btn btn-success"
            >
                Export CSV
            </a>

        @endauth

    </div>

</div>


{{-- Filters --}}

<div class="card mb-4">

    <div class="card-header">
        <strong>Search & Filter Posts</strong>
    </div>

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('posts.index') }}"
            class="row g-3"
        >

            <div class="col-md-3">

                <label class="form-label">
                    Search
                </label>

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search posts..."
                    value="{{ request('search') }}"
                >

            </div>


            <div class="col-md-2">

                <label class="form-label">
                    From
                </label>

                <input
                    type="date"
                    name="from"
                    class="form-control"
                    value="{{ request('from') }}"
                >

            </div>


            <div class="col-md-2">

                <label class="form-label">
                    To
                </label>

                <input
                    type="date"
                    name="to"
                    class="form-control"
                    value="{{ request('to') }}"
                >

            </div>


            <div class="col-md-2">

                <label class="form-label">
                    User ID
                </label>

                <input
                    type="number"
                    name="user_id"
                    class="form-control"
                    value="{{ request('user_id') }}"
                    min="1"
                >

            </div>


            <div class="col-md-2">

                <label class="form-label">
                    Sort
                </label>

                <select
                    name="sort"
                    class="form-select"
                >

                    <option
                        value="latest"
                        {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}
                    >
                        Latest
                    </option>

                    <option
                        value="most_commented"
                        {{ request('sort') === 'most_commented' ? 'selected' : '' }}
                    >
                        Most Commented
                    </option>

                    <option
                        value="least_commented"
                        {{ request('sort') === 'least_commented' ? 'selected' : '' }}
                    >
                        Least Commented
                    </option>

                    <option
                        value="most_liked"
                        {{ request('sort') === 'most_liked' ? 'selected' : '' }}
                    >
                        Most Liked
                    </option>

                    <option
                        value="most_viewed"
                        {{ request('sort') === 'most_viewed' ? 'selected' : '' }}
                    >
                        Most Viewed
                    </option>

                </select>

            </div>


            @auth

                <div class="col-md-2">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            All
                        </option>

                        <option
                            value="published"
                            {{ request('status') === 'published' ? 'selected' : '' }}
                        >
                            Published
                        </option>

                        <option
                            value="draft"
                            {{ request('status') === 'draft' ? 'selected' : '' }}
                        >
                            Draft
                        </option>

                    </select>

                </div>

            @endauth


            <div class="col-12">

                <button
                    type="submit"
                    class="btn btn-secondary"
                >
                    Filter
                </button>

                <a
                    href="{{ route('posts.index') }}"
                    class="btn btn-outline-danger"
                >
                    Clear
                </a>

            </div>

        </form>

    </div>

</div>


{{-- Posts --}}

<div class="list-group">

    @forelse($posts as $post)

        <div class="list-group-item mb-3 border rounded">

            <div class="d-flex justify-content-between">

                <h5>

                    <a
                        href="{{ route('posts.show', $post) }}"
                        class="text-decoration-none"
                    >
                        {{ $post->name }}
                    </a>

                </h5>

                <small>
                    {{ $post->created_at->diffForHumans() }}
                </small>

            </div>


            <p class="text-muted">
                {{ Str::limit($post->body, 150) }}
            </p>


            <div class="d-flex gap-2 flex-wrap">

                <span class="badge bg-primary">
                    By {{ $post->user->name }}
                </span>


                <span
                    class="badge {{ $post->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }}"
                >
                    {{ ucfirst($post->status) }}
                </span>


                <span class="badge bg-danger">
                    {{ $post->likes_count }} Likes
                </span>


                <span class="badge bg-info">
                    {{ $post->comments_count }} Comments
                </span>


                <span class="badge bg-secondary">
                    {{ $post->bookmarks_count }} Bookmarks
                </span>


                <span class="badge bg-dark">
                    {{ $post->views_count }} Views
                </span>


                @if($post->image)

                    <span class="badge bg-warning text-dark">
                        Has Image
                    </span>

                @endif

            </div>


            @auth

                @if(auth()->id() === $post->user_id)

                    <div class="d-flex gap-2 mt-3">

                        <a
                            href="{{ route('posts.edit', $post) }}"
                            class="btn btn-sm btn-warning"
                        >
                            Edit
                        </a>

                        <form
                            action="{{ route('posts.destroy', $post) }}"
                            method="POST"
                            onsubmit="return confirm('Delete this post?')"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-sm btn-danger"
                            >
                                Delete
                            </button>

                        </form>

                    </div>

                @endif

            @endauth

        </div>

    @empty

        <div class="alert alert-info">
            No posts found.
        </div>

    @endforelse

</div>


{{-- Numeric Pagination --}}

<div class="mt-4">

    {{ $posts->onEachSide(1)->links('pagination::bootstrap-5') }}

</div>

@endsection