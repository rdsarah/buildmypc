@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">

        <div class="mb-4">
            <a href="{{ route('blogs.index') }}" class="btn btn-soft">
                ← Back to Blogs
            </a>
        </div>

        {{-- Blog --}}
        <article class="glass-card">

            @if($blog->image)
                <img
                    src="{{ asset('storage/' . $blog->image) }}"
                    alt="{{ $blog->title }}"
                    class="img-fluid rounded mb-4"
                    style="width: 100%; max-height: 450px; object-fit: cover;"
                >
            @endif

            <h1 class="section-title mb-3">
                {{ $blog->title }}
            </h1>

            <p class="text-muted mb-4">
                By <strong>{{ $blog->user->name }}</strong>
                · {{ $blog->created_at->format('M d, Y') }}
            </p>

            <div style="white-space: pre-line;">
                {{ $blog->content }}
            </div>

        </article>

        {{-- Comments --}}
        <div class="mt-5">

            <h4 class="section-title mb-3">
                Comments
            </h4>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add Comment --}}
            @auth
                <div class="glass-card mb-4">

                    <form action="{{ route('comments.store', $blog) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="comment-content" class="form-label">
                                Leave a Comment
                            </label>

                            <textarea
                                id="comment-content"
                                name="content"
                                rows="3"
                                class="form-control @error('content') is-invalid @enderror"
                                placeholder="Write your comment..."
                            >{{ old('content') }}</textarea>

                            @error('content')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-fern">
                            Post Comment
                        </button>
                    </form>

                </div>
            @else
                <div class="glass-card mb-4">
                    <p class="mb-0">
                        <a href="{{ route('login') }}">Log in</a>
                        to join the discussion.
                    </p>
                </div>
            @endauth


            {{-- Comment List --}}
            @if($blog->comments->count())

                @foreach($blog->comments as $comment)

                    <div class="glass-card mb-3">

                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $comment->user->name }}</strong>

                                <small class="text-muted">
                                    · {{ $comment->created_at->format('M d, Y h:i A') }}
                                </small>
                            </div>
                        </div>

                        <p class="mb-3" style="white-space: pre-line;">
                            {{ $comment->content }}
                        </p>

                        {{-- Reply Form --}}
                        @auth
                            <form
                                action="{{ route('comments.reply', $comment) }}"
                                method="POST"
                                class="mt-3"
                            >
                                @csrf

                                <div class="input-group">
                                    <input
                                        type="text"
                                        name="content"
                                        class="form-control"
                                        placeholder="Write a reply..."
                                        required
                                    >

                                    <button type="submit" class="btn btn-soft">
                                        Reply
                                    </button>
                                </div>
                            </form>
                        @endauth


                        {{-- Replies --}}
                        @if($comment->replies->count())

                            <div class="mt-3 ms-4">

                                @foreach($comment->replies as $reply)

                                    <div class="border-start ps-3 mb-3">

                                        <div class="mb-1">
                                            <strong>{{ $reply->user->name }}</strong>

                                            <small class="text-muted">
                                                · {{ $reply->created_at->format('M d, Y h:i A') }}
                                            </small>
                                        </div>

                                        <p class="mb-0" style="white-space: pre-line;">
                                            {{ $reply->content }}
                                        </p>

                                    </div>

                                @endforeach

                            </div>

                        @endif

                    </div>

                @endforeach

            @else

                <div class="glass-card text-center py-4">
                    <p class="mb-0">
                        No comments yet. Be the first to start the discussion.
                    </p>
                </div>

            @endif

        </div>

    </div>
</div>

@endsection