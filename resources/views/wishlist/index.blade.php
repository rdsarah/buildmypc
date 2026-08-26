@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h1 class="section-title mb-2">My Wishlist</h1>
        <p class="section-subtitle mb-0">
            Save components you're interested in and keep track of their availability and prices.
        </p>
    </div>

    <a href="{{ route('products.index') }}" class="btn btn-soft mt-3 mt-md-0">
        Browse Components
    </a>
</div>

@if(session('success'))
    <div class="notice-card mb-4">
        {{ session('success') }}
    </div>
@endif

@if($wishlists->count() > 0)

    <div class="row g-4">

        @foreach($wishlists as $wishlist)

            @php
                $product = $wishlist->product;
            @endphp

            <div class="col-md-6 col-xl-4">

                <div class="category-card product-card text-start h-100">

                    <img
                        src="{{ $product->image_url }}"
                        alt="{{ $product->name }}"
                        class="product-card-image"
                    >

                    <div class="d-flex justify-content-between align-items-center mt-3 mb-2">

                        <span class="status-pill">
                            {{ $product->category->name }}
                        </span>

                        @if($product->stock > 0)
                            <span class="badge bg-success">
                                In Stock
                            </span>
                        @else
                            <span class="badge bg-danger">
                                Out of Stock
                            </span>
                        @endif

                    </div>

                    <h5>{{ $product->name }}</h5>

                    <p class="mb-2">
                        {{ $product->brand->name }}

                        @if($product->model)
                            • {{ $product->model }}
                        @endif
                    </p>

                    <strong class="product-price d-block mb-3">
                        {{ $product->formatted_price }}
                    </strong>

                    <div class="d-flex gap-2 mt-auto">

                        <a
                            href="{{ route('products.show', $product) }}"
                            class="btn btn-soft flex-grow-1"
                        >
                            View Details
                        </a>

                        <form
                            action="{{ route('wishlist.destroy', $product) }}"
                            method="POST"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                                title="Remove from wishlist"
                            >
                                ♡
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

    <div class="mt-4">
        {{ $wishlists->links() }}
    </div>

@else

    <div class="glass-card text-center py-5">

        <div style="font-size: 3rem;" class="mb-3">
            ♡
        </div>

        <h4>Your wishlist is empty</h4>

        <p class="empty-text mb-4">
            Save components here so you can keep an eye on their prices and availability.
        </p>

        <a href="{{ route('products.index') }}" class="btn btn-fern">
            Browse Components
        </a>

    </div>

@endif

@endsection