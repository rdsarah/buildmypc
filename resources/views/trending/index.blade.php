@extends('layouts.app')

@section('content')

<div class="container py-5">

    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">
            🔥 Trending Dashboard
        </h1>

        <p class="text-muted">
            Discover the most popular PC components based on user activity and comparison trends.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="glass-card h-100 text-center">
                <h6>Total Comparisons</h6>
                <h2>{{ $histories->count() }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card h-100 text-center">
                <h6>Trending Products</h6>
                <h5 class="mt-3">{{ $globalTopProduct ? $globalTopProduct->name : 'N/A' }}
                </h5>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card h-100 text-center">
                <h6>Top Category</h6>
                <h2>{{ $topCategory ?? 'N/A' }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card h-100 text-center">
                <h6>Top Brand</h6>
                <h2>{{ $topBrand ?? 'N/A' }}</h2>
            </div>
        </div>

    </div>

<div class="mt-5">
    <div class="glass-card">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h3 class="mb-1">🏆 Top Trending Products</h3>
                <p class="section-subtitle mb-0">
                    Explore the most popular products across every category.
                </p>
            </div>

            <form method="GET" action="{{ route('trending.index') }}">
                <select name="category"
                        class="form-select"
                        style="max-width:220px"
                        onchange="this.form.submit()">

                    <option value="">All Categories</option>

                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach

                </select>
            </form>
        </div>

        @if ($trendingProducts->count())

            @foreach ($trendingProducts as $product)

                <div class="glass-card mb-3 p-3">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div class="d-flex align-items-center gap-4">

                            <div class="text-center">
                                <h4 class="mb-0 text-success">
                                    #{{ $loop->iteration }}
                                </h4>
                            </div>

                            <div>
                                <h5 class="mb-1">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h5>

                                <small class="text-muted">
                                    {{ $product->category->name ?? 'N/A' }}
                                    •
                                    {{ $product->brand->name ?? 'N/A' }}
                                </small>
                            </div>

                        </div>

                        <div class="text-end">

                            <h5 class="mb-1">
                                {{ $product->formatted_price }}
                            </h5>

                            <span class="badge bg-success px-3 py-2">
                                🔥 Compared {{ $product->trend_count }} time{{ $product->trend_count > 1 ? 's' : '' }}
                            </span>

                        </div>

                    </div>

                </div>

            @endforeach

        @else

            <div class="text-center py-5">
                <h5>No trending products found.</h5>
                <p class="text-muted mb-0">
                    Try selecting a different category.
                </p>
            </div>

        @endif

    </div>
</div>

</div>

@endsection