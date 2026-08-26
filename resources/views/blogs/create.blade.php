@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="mb-4">
            <h2 class="section-title mb-1">Write a Blog</h2>
            <p class="section-subtitle mb-0">
                Share your PC setup, ideas, experiences, or anything related to the community.
            </p>
        </div>

        <div class="glass-card">

            <form action="{{ route('blogs.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">
                        Blog Title
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}"
                        placeholder="Enter your blog title"
                    >

                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">
                        Blog Content
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        rows="10"
                        class="form-control @error('content') is-invalid @enderror"
                        placeholder="Write your blog here..."
                    >{{ old('content') }}</textarea>

                    @error('content')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label">
                        Cover Image <span class="text-muted">(Optional)</span>
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="form-control @error('image') is-invalid @enderror"
                    >

                    <small class="text-muted">
                        Maximum 2 MB. JPG, JPEG, PNG, or WEBP.
                    </small>

                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-fern">
                        Publish Blog
                    </button>

                    <a href="{{ route('blogs.index') }}" class="btn btn-soft">
                        Cancel
                    </a>
                </div>

            </form>

        </div>

    </div>
</div>

@endsection