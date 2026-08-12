@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
<h1>Create Post</h1>

<form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Title</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required maxlength="255">
    </div>

    <div class="mb-3">
        <label for="body" class="form-label">Body</label>
        <textarea name="body" id="body" class="form-control" rows="6" required minlength="10" maxlength="5000">{{ old('body') }}</textarea>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
        <div class="form-text">Max 2MB. JPG, PNG, WebP only.</div>
    </div>

    <button type="submit" class="btn btn-primary">Create Post</button>
    <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
