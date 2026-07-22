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