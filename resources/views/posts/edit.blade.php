@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')

<div class="row justify-content-center">

    <div class="col-md-8">

        <div class="card">

            <div class="card-header">
                <h4 class="mb-0">Edit Post</h4>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('posts.update', $post) }}"
                    enctype="multipart/form-data"
                >

                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label class="form-label">
                            Post Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $post->name) }}"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Body
                        </label>

                        <textarea
                            name="body"
                            class="form-control"
                            rows="7"
                            required
                        >{{ old('body', $post->body) }}</textarea>

                    </div>


                    @if($post->image)

                        <div class="mb-3">

                            <p>Current Image:</p>

                            <img
                                src="{{ asset('storage/' . $post->image) }}"
                                class="img-thumbnail"
                                style="max-width:200px"
                            >

                        </div>

                    @endif


                    <div class="mb-3">

                        <label class="form-label">
                            Replace Image
                        </label>

                        <input
                            type="file"
                            name="image"
                            class="form-control"
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option
                                value="published"
                                {{ old('status', $post->status) === 'published' ? 'selected' : '' }}
                            >
                                Published
                            </option>

                            <option
                                value="draft"
                                {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}
                            >
                                Draft
                            </option>

                        </select>

                    </div>


                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Update Post
                        </button>

                        <a
                            href="{{ route('posts.show', $post) }}"
                            class="btn btn-secondary"
                        >
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection