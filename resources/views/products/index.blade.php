@extends('layouts.app')

@section('title', 'Component Catalog')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="section-title mb-2">Component Catalog</h1>
        <p class="section-subtitle mb-0">
            Browse, search, filter, sort, and view PC components from the database.
        </p>
    </div>

    <a href="{{ route('products.index') }}" class="btn btn-soft">
        Reset Catalog
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-3">
        <div class="glass-card filter-panel">
            <h5 class="mb-3">Search & Filters</h5>

            <form method="GET" action="{{ route('products.index') }}">
                <div class="mb-3">
                    <label class="form-label">Search</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Ryzen, MSI, GPU..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Brand</label>
                    <select name="brand" class="form-select">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->slug }}" @selected(request('brand') === $brand->slug)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Min Price</label>
                        <input
                            type="number"
                            name="min_price"
                            class="form-control"
                            placeholder="0"
                            value="{{ request('min_price') }}"
                        >
                    </div>

                    <div class="col-6">
                        <label class="form-label">Max Price</label>
                        <input
                            type="number"
                            name="max_price"
                            class="form-control"
                            placeholder="100000"
                            value="{{ request('max_price') }}"
                        >
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Socket Type</label>
                    <select name="socket_type" class="form-select">
                        <option value="">Any Socket</option>
                        @foreach($socketTypes as $socketType)
                            <option value="{{ $socketType }}" @selected(request('socket_type') === $socketType)>
                                {{ $socketType }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">RAM Type</label>
                    <select name="ram_type" class="form-select">
                        <option value="">Any RAM Type</option>
                        @foreach($ramTypes as $ramType)
                            <option value="{{ $ramType }}" @selected(request('ram_type') === $ramType)>
                                {{ $ramType }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Form Factor</label>
                    <select name="form_factor" class="form-select">
                        <option value="">Any Form Factor</option>
                        @foreach($formFactors as $formFactor)
                            <option value="{{ $formFactor }}" @selected(request('form_factor') === $formFactor)>
                                {{ $formFactor }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Storage Type</label>
                    <select name="storage_type" class="form-select">
                        <option value="">Any Storage</option>
                        @foreach($storageTypes as $storageType)
                            <option value="{{ $storageType }}" @selected(request('storage_type') === $storageType)>
                                {{ $storageType }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock Status</label>
                    <select name="stock_status" class="form-select">
                        <option value="">All</option>
                        <option value="in_stock" @selected(request('stock_status') === 'in_stock')>
                            In Stock
                        </option>
                        <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>
                            Out of Stock
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="">Newest</option>
                        <option value="price_low" @selected(request('sort') === 'price_low')>
                            Price: Low to High
                        </option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>
                            Price: High to Low
                        </option>
                        <option value="brand" @selected(request('sort') === 'brand')>
                            Brand A-Z
                        </option>
                        <option value="stock" @selected(request('sort') === 'stock')>
                            Highest Stock
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-fern w-100 mb-2">
                    Apply
                </button>

                <a href="{{ route('products.index') }}" class="btn btn-soft w-100">
                    Clear
                </a>
            </form>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <p class="empty-text mb-0">
                Showing {{ $products->count() }} of {{ $products->total() }} components
            </p>

            @if(request()->query())
                <span class="status-pill">
                    Filtered Results
                </span>
            @else
                <span class="status-pill">
                    All Components
                </span>
            @endif
        </div>

        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
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

                                <span class="empty-text">
                                    {{ $product->stock_status }}
                                </span>
                            </div>

                            <h5>{{ $product->name }}</h5>

                            <p class="mb-2">
                                {{ $product->brand->name }}
                                @if($product->model)
                                    • {{ $product->model }}
                                @endif
                            </p>

                            <p class="mb-3">
                                {{ \Illuminate\Support\Str::limit($product->description, 75) }}
                            </p>

                            <div class="spec-mini-list mb-3">
                                @if($product->socket_type)
                                    <span>Socket: {{ $product->socket_type }}</span>
                                @endif

                                @if($product->ram_type)
                                    <span>RAM: {{ $product->ram_type }}</span>
                                @endif

                                @if($product->form_factor)
                                    <span>Form: {{ $product->form_factor }}</span>
                                @endif

                                @if($product->wattage)
                                    <span>Power: {{ $product->wattage }}W</span>
                                @endif

                                @if($product->storage_type)
                                    <span>Storage: {{ $product->storage_type }}</span>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <strong class="product-price">
                                    {{ $product->formatted_price }}
                                </strong>
                            </div>

                            <a href="{{ route('products.show', $product) }}" class="btn btn-soft w-100 mt-3">
                                View Details
                            </a>

                            <form action="{{ route('builder.add', $product) }}" method="POST" class="mt-2 w-100">
                                @csrf
                                <button type="submit" class="btn btn-fern w-100">Add to Build</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="glass-card text-center">
                <h5>No components found</h5>
                <p class="empty-text mb-0">
                    Try changing your search, filter, or price range.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection