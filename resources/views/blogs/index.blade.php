@extends('layouts.app')

@section('content')

<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h2 class="section-title mb-1">Community Blogs</h2>
        <p class="section-subtitle mb-0">
            Explore PC builds, ideas, experiences, and discussions from the community.
        </p>
    </div>

    @auth
        <a href="{{ route('blogs.create') }}" class="btn btn-fern">
            Write a Blog
        </a>
    @else
        <a href="{{ route('login') }}" class="btn btn-fern">
            Login to Write
        </a>
    @endauth
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($blogs->count())

    <div class="row g-4">
        @foreach($blogs as $blog)
            <div class="col-md-6 col-lg-4">
                <div class="glass-card h-100 d-flex flex-column">

                    @if($blog->image)
                        <img
                            src="{{ asset('storage/' . $blog->image) }}"
                            alt="{{ $blog->title }}"
                            class="img-fluid rounded mb-3"
                            style="width: 100%; height: 200px; object-fit: cover;"
                        >
                    @endif

                    <h5 class="mb-2">
                        {{ $blog->title }}
                    </h5>

                    <p class="text-muted small mb-2">
                        By {{ $blog->user->name }}
                        · {{ $blog->created_at->format('M d, Y') }}
                    </p>

                    <a href="{{ route('blogs.show', $blog) }}"
                       class="btn btn-soft align-self-start">
                        Read More
                    </a>

                </div>
            </div>
        @endforeach
    </div>

@else

    <div class="glass-card text-center py-5">
        <h5>No blogs yet</h5>
        <p class="mb-0">
            Be the first person to share something with the community.
        </p>
    </div>

@endif

@endsection