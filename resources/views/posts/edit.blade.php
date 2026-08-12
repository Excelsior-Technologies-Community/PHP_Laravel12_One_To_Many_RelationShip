@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<h1>Edit Post</h1>

<form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label">Title</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $post->name) }}" required maxlength="255">
    </div>

    <div class="mb-3">
        <label for="body" class="form-label">Body</label>
        <textarea name="body" id="body" class="form-control" rows="6" required minlength="10" maxlength="5000">{{ old('body', $post->body) }}</textarea>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
        @if($post->image)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $post->image) }}" alt="Current image" style="max-height: 200px;">
            </div>
        @endif
        <div class="form-text">Leave blank to keep current image. Max 2MB. JPG, PNG, WebP only.</div>
    </div>

    <button type="submit" class="btn btn-primary">Update Post</button>
    <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
