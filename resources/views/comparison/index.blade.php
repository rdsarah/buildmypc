@extends('layouts.app')

@section('title', 'Compare Components')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="section-title mb-2">Compare Components</h1>

        <p class="section-subtitle mb-0">
            Select a category and compare two or three components side by side.
        </p>
    </div>

    <a href="{{ route('comparison.index') }}" class="btn btn-soft">
        Reset Comparison
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="glass-card mb-4">
    <h5 class="mb-3">Select Component Category</h5>

    <form method="GET" action="{{ route('comparison.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-9">
                <label class="form-label">Category</label>

                <select name="category" class="form-select" required>
                    <option value="">Choose a category</option>

                    @foreach($categories as $category)
                        <option
                            value="{{ $category->slug }}"
                            @selected(request('category') === $category->slug)
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-fern w-100">
                    Show Products
                </button>
            </div>
        </div>
    </form>
</div>

@if(request('category'))
    <div class="glass-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <h5 class="mb-1">Choose Products</h5>

                <p class="empty-text mb-0">
                    Select at least two and at most three products.
                </p>
            </div>

            <span class="status-pill">
                {{ $products->count() }} Products Available
            </span>
        </div>

        @if($products->count() > 0)
            <form method="GET" action="{{ route('comparison.index') }}">
                <input
                    type="hidden"
                    name="category"
                    value="{{ request('category') }}"
                >

                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-md-6 col-xl-4">
                            <label class="category-card product-card text-start h-100 w-100">
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

                                <strong class="product-price d-block mb-3">
                                    {{ $product->formatted_price }}
                                </strong>

                                <div class="form-check mt-auto">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="products[]"
                                        value="{{ $product->id }}"
                                        id="product-{{ $product->id }}"
                                        @checked(
                                            in_array(
                                                $product->id,
                                                array_map(
                                                    'intval',
                                                    request('products', [])
                                                )
                                            )
                                        )
                                    >

                                    <span class="form-check-label">
                                        Select for comparison
                                    </span>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>

                <hr class="border-secondary my-4">

                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">
                            Recommendation Priority
                        </label>

                        <select name="priority" class="form-select" required>
                            <option
                                value="balanced"
                                @selected(request('priority', 'balanced') === 'balanced')
                            >
                                Balanced Price and Availability
                            </option>

                            <option
                                value="lowest_price"
                                @selected(request('priority') === 'lowest_price')
                            >
                                Lowest Price
                            </option>

                            <option
                                value="availability"
                                @selected(request('priority') === 'availability')
                            >
                                Highest Availability
                            </option>

                            <option
                                value="power_efficiency"
                                @selected(request('priority') === 'power_efficiency')
                            >
                                Power Efficiency
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-fern w-100">
                            Compare Selected
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="text-center py-4">
                <h5>No products found</h5>

                <p class="empty-text mb-0">
                    There are no active products in this category.
                </p>
            </div>
        @endif
    </div>
@endif

@if($selectedProducts->count() >= 2)
    <div class="glass-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <h2 class="section-title mb-1">Comparison Result</h2>

                <p class="section-subtitle mb-0">
                    Specifications of the selected components.
                </p>
            </div>

            <span class="status-pill">
                {{ $selectedProducts->count() }} Components Compared
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-bordered align-middle text-center mb-0">
                <thead>
                    <tr>
                        <th class="text-start">Specification</th>

                        @foreach($selectedProducts as $product)
                            <th>
                                <img
                                    src="{{ $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    style="width: 100px; height: 80px; object-fit: contain;"
                                    class="mb-2"
                                >

                                <div>{{ $product->name }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <th class="text-start">Brand</th>

                        @foreach($selectedProducts as $product)
                            <td>{{ $product->brand->name }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Model</th>

                        @foreach($selectedProducts as $product)
                            <td>{{ $product->model ?? 'N/A' }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Price</th>

                        @foreach($selectedProducts as $product)
                            <td>
                                <strong class="product-price">
                                    {{ $product->formatted_price }}
                                </strong>
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Stock</th>

                        @foreach($selectedProducts as $product)
                            <td>{{ $product->stock }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Socket Type</th>

                        @foreach($selectedProducts as $product)
                            <td>{{ $product->socket_type ?? 'N/A' }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">RAM Type</th>

                        @foreach($selectedProducts as $product)
                            <td>{{ $product->ram_type ?? 'N/A' }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Form Factor</th>

                        @foreach($selectedProducts as $product)
                            <td>{{ $product->form_factor ?? 'N/A' }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Wattage</th>

                        @foreach($selectedProducts as $product)
                            <td>
                                {{ $product->wattage ? $product->wattage . 'W' : 'N/A' }}
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Storage Type</th>

                        @foreach($selectedProducts as $product)
                            <td>{{ $product->storage_type ?? 'N/A' }}</td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">GPU Length</th>

                        @foreach($selectedProducts as $product)
                            <td>
                                {{ $product->gpu_length_mm ? $product->gpu_length_mm . ' mm' : 'N/A' }}
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Case GPU Clearance</th>

                        @foreach($selectedProducts as $product)
                            <td>
                                {{ $product->case_clearance_mm ? $product->case_clearance_mm . ' mm' : 'N/A' }}
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Stock Status</th>

                        @foreach($selectedProducts as $product)
                            <td>
                                <span class="status-pill">
                                    {{ $product->stock_status }}
                                </span>
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <th class="text-start">Details</th>

                        @foreach($selectedProducts as $product)
                            <td>
                                <a
                                    href="{{ route('products.show', $product) }}"
                                    class="btn btn-soft btn-sm"
                                >
                                    View Product
                                </a>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endif

@if($recommendedProduct)
    <div class="glass-card">
        <div class="row g-4 align-items-center">
            <div class="col-md-3 text-center">
                <img
                    src="{{ $recommendedProduct->image_url }}"
                    alt="{{ $recommendedProduct->name }}"
                    class="product-detail-image"
                    style="max-height: 200px;"
                >
            </div>

            <div class="col-md-9">
                <span class="status-pill mb-3">
                    Recommended Component
                </span>

                <h2 class="section-title mt-3 mb-2">
                    {{ $recommendedProduct->name }}
                </h2>

                <p class="section-subtitle">
                    {{ $recommendationText }}
                </p>

                <h4 class="product-detail-price mb-3">
                    {{ $recommendedProduct->formatted_price }}
                </h4>

                <a
                    href="{{ route('products.show', $recommendedProduct) }}"
                    class="btn btn-fern"
                >
                    View Recommended Product
                </a>
            </div>
        </div>
    </div>
@endif
@endsection