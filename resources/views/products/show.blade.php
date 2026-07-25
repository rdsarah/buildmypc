@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('products.index') }}" class="btn btn-soft">
        ← Back to Catalog
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="glass-card">
            <img
                src="{{ $product->image_url }}"
                alt="{{ $product->name }}"
                class="product-detail-image"
            >
        </div>
    </div>

    <div class="col-lg-7">
        <div class="glass-card">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="status-pill">
                    {{ $product->category->name }}
                </span>

                <span class="status-pill">
                    {{ $product->stock_status }}
                </span>
            </div>

            <h1 class="section-title mb-3">
                {{ $product->name }}
            </h1>

            <p class="section-subtitle">
                {{ $product->description }}
            </p>

            <h3 class="product-detail-price mb-4">
                {{ $product->formatted_price }}
            </h3>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Brand</span>
                        <strong>{{ $product->brand->name }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Model</span>
                        <strong>{{ $product->model ?? 'N/A' }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Available Stock</span>
                        <strong>{{ $product->stock }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Category</span>
                        <strong>{{ $product->category->name }}</strong>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <h5 class="mb-3">Technical Specifications</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Socket Type</span>
                        <strong>{{ $product->socket_type ?? 'N/A' }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>RAM Type</span>
                        <strong>{{ $product->ram_type ?? 'N/A' }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Form Factor</span>
                        <strong>{{ $product->form_factor ?? 'N/A' }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Wattage</span>
                        <strong>{{ $product->wattage ? $product->wattage . 'W' : 'N/A' }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Storage Type</span>
                        <strong>{{ $product->storage_type ?? 'N/A' }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>GPU Length</span>
                        <strong>{{ $product->gpu_length_mm ? $product->gpu_length_mm . ' mm' : 'N/A' }}</strong>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="spec-box">
                        <span>Case GPU Clearance</span>
                        <strong>{{ $product->case_clearance_mm ? $product->case_clearance_mm . ' mm' : 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            <form action="{{ route('builder.add', $product) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-fern w-100 py-3">Add to Build</button>
            </form>
        </div>
    </div>
</div>

<!-- ================= REVIEWS SECTION ================= -->
<div class="mt-5">
    <div class="glass-card">
        <h2 class="section-title mb-4">Customer Reviews</h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Review Submission Form -->
        <form action="{{ route('reviews.store', $product) }}" method="POST" class="p-4 rounded mb-5" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
            @csrf
            <h5 class="mb-3 text-white">Leave a Review</h5>

            <div class="mb-3">
                <label for="rating" class="form-label text-muted">Rating</label>
                <select name="rating" id="rating" class="form-select bg-dark text-white border-secondary" required>
                    <option value="5">⭐⭐⭐⭐⭐ (5 / 5)</option>
                    <option value="4">⭐⭐⭐⭐ (4 / 5)</option>
                    <option value="3">⭐⭐⭐ (3 / 5)</option>
                    <option value="2">⭐⭐ (2 / 5)</option>
                    <option value="1">⭐ (1 / 5)</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="comment" class="form-label text-muted">Your Feedback</label>
                <textarea name="comment" id="comment" rows="3" class="form-control bg-dark text-white border-secondary" placeholder="What do you think of this component?" required></textarea>
            </div>

            <button type="submit" class="btn btn-fern py-2 px-4">Submit Review</button>
        </form>

        <!-- Reviews List -->
       
        <div class="reviews-list">
            @forelse($product->reviews as $review)
                <div class="p-3 mb-3 rounded" style="background: rgba(255, 255, 255, 0.03); border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong class="text-white">{{ $review->user ? $review->user->name : 'Anonymous Guest' }}</strong>
                            <span class="text-warning fw-bold fs-5 ms-2">{{ str_repeat('★', $review->rating) }}</span>
                        </div>

                        <!-- Delete Button: Only shown if the logged-in user owns this review -->
                        @if(auth()->check() && auth()->id() === $review->user_id)
                            <form action="{{ route('reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>

                    <p class="mb-1 text-light">{{ $review->comment }}</p>
                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                </div>
            @empty
                <p class="text-muted italic mb-0">No reviews yet for this product. Be the first to leave one!</p>
            @endforelse
        </div>
    </div>
</div>

@if($relatedProducts->count() > 0)
    <div class="mt-5">
        <h2 class="section-title mb-4">Related Components</h2>

        <div class="row g-4">
            @foreach($relatedProducts as $relatedProduct)
                <div class="col-md-4">
                    <div class="category-card product-card text-start h-100">
                        <img
                            src="{{ $relatedProduct->image_url }}"
                            alt="{{ $relatedProduct->name }}"
                            class="product-card-image"
                        >

                        <span class="status-pill mt-3 mb-2">
                            {{ $relatedProduct->category->name }}
                        </span>

                        <h5>{{ $relatedProduct->name }}</h5>
                        <p>{{ $relatedProduct->brand->name }}</p>

                        <strong class="product-price">
                            {{ $relatedProduct->formatted_price }}
                        </strong>

                        <a href="{{ route('products.show', $relatedProduct) }}" class="btn btn-soft w-100 mt-3">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection