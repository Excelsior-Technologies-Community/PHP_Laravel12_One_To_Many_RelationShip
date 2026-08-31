@extends('layouts.app')

@section('title', 'Posts')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <h1>Posts</h1>

    @auth
        <a
            href="{{ route('posts.create') }}"
            class="btn btn-primary"
        >
            Create Post
        </a>
    @endauth

</div>



{{-- Search and Filters --}}
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

            {{-- Search --}}
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


            {{-- From Date --}}
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


            {{-- To Date --}}
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


            {{-- User ID --}}
            <div class="col-md-2">

                <label class="form-label">
                    User ID
                </label>

                <input
                    type="number"
                    name="user_id"
                    class="form-control"
                    placeholder="User ID"
                    value="{{ request('user_id') }}"
                    min="1"
                >

            </div>


            {{-- Sort --}}
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

                </select>

            </div>


            {{-- Filter Button --}}
            <div class="col-md-1 d-flex align-items-end">

                <button
                    type="submit"
                    class="btn btn-secondary w-100"
                >
                    Filter
                </button>

            </div>

        </form>


        {{-- Clear Filters --}}
        @if(
            request('search') ||
            request('from') ||
            request('to') ||
            request('user_id') ||
            request('sort')
        )

            <div class="mt-3">

                <a
                    href="{{ route('posts.index') }}"
                    class="btn btn-outline-danger btn-sm"
                >
                    Clear Filters
                </a>

            </div>

        @endif

    </div>

</div>


{{-- Posts --}}
<div class="list-group">

    @forelse($posts as $post)

        <div class="list-group-item mb-3 border rounded">

            <div class="d-flex w-100 justify-content-between">

                <h5 class="mb-1">

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


            <p class="mb-2 text-muted">

                {{ Str::limit($post->body, 150) }}

            </p>


            <div class="d-flex gap-2 flex-wrap align-items-center">

                {{-- Author --}}
                <span class="badge bg-primary">

                    By {{ $post->user->name }}

                </span>


                {{-- Likes --}}
                <span class="badge bg-success">

                    {{ $post->likes->count() }} Likes

                </span>


                {{-- Relationship Based Comment Count --}}
                <span class="badge bg-info">

                    {{ $post->comments_count }} Comments

                </span>


                {{-- Top-Level Discussions --}}
                <span class="badge bg-secondary">

                   {{ $post->top_level_comments_count }} Discussions

                </span>


                {{-- Image --}}
                @if($post->image)

                    <span class="badge bg-warning text-dark">

                        Has Image

                    </span>

                @endif

            </div>


            {{-- Post Actions --}}
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
                            onsubmit="return confirm('Are you sure you want to delete this post?')"
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

    @empty

        <div class="alert alert-info">

            No posts found.

        </div>

    @endforelse

</div>


{{-- Pagination --}}
<div class="mt-4">

    {{ $posts->links() }}

</div>

@endsection