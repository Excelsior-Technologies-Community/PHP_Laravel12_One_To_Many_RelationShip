@extends('layouts.app')

@section('title', 'Create Post')

@section('content')

<div class="row justify-content-center">

    <div class="col-md-8">

        <div class="card">

            <div class="card-header">
                <h4 class="mb-0">Create Post</h4>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('posts.store') }}"
                    enctype="multipart/form-data"
                >

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Post Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
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
                        >{{ old('body') }}</textarea>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Image
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
                                {{ old('status', 'published') === 'published' ? 'selected' : '' }}
                            >
                                Published
                            </option>

                            <option
                                value="draft"
                                {{ old('status') === 'draft' ? 'selected' : '' }}
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
                            Create Post
                        </button>

                        <a
                            href="{{ route('posts.index') }}"
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