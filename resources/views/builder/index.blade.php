@extends('layouts.app')

@section('title', 'PC Builder - BuildMyPC')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
        <div>
            <p class="eyebrow mb-2">Custom Build Workspace</p>
            <h1 class="section-title mb-2">Build Compatibility Checker</h1>
            <p class="text-secondary mb-0">
                Select one component from each category. The system will check basic compatibility using stored product specifications.
            </p>
        </div>

        <form action="{{ route('builder.reset') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-soft">
                Reset Build
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="glass-card p-4 mb-4">
                <h2 class="h4 mb-3">Select Components</h2>

                <div class="row g-3">
                    @foreach ($categories as $category)
                        @php
                            $selectedItem = $build->items->firstWhere('category_id', $category->id);
                            $selectedProduct = $selectedItem?->product;
                        @endphp

                        <div class="col-md-6">
                            <div class="builder-slot h-100">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                    <div>
                                        <h3 class="h5 mb-1">{{ $category->name }}</h3>
                                        <p class="text-secondary small mb-0">
                                            Choose a {{ strtolower($category->name) }} for your build.
                                        </p>
                                    </div>

                                    @if ($selectedProduct)
                                        <span class="stock-pill in-stock">Selected</span>
                                    @else
                                        <span class="stock-pill out-stock">Missing</span>
                                    @endif
                                </div>

                                @if ($selectedProduct)
                                    <div class="builder-selected mb-3">
                                        <strong>{{ $selectedProduct->name }}</strong>
                                        <span>{{ $selectedProduct->brand->name ?? 'Unknown Brand' }}</span>
                                        <span>Tk {{ number_format($selectedProduct->price, 0) }}</span>

                                        <form action="{{ route('builder.items.destroy', $selectedItem) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <form action="{{ route('builder.items.store') }}" method="POST">
                                    @csrf

                                    <input type="hidden" name="category_id" value="{{ $category->id }}">

                                    <label class="form-label small text-secondary">
                                        {{ $selectedProduct ? 'Replace selection' : 'Select product' }}
                                    </label>

                                    <select name="product_id" class="form-select product-select" required>
                                        <option value="">Choose {{ $category->name }}</option>

                                        @foreach ($category->products as $product)
                                            <option value="{{ $product->id }}" @selected($selectedProduct?->id === $product->id)>
                                                {{ $product->name }} — Tk {{ number_format($product->price, 0) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="btn btn-fern w-100 mt-3">
                                        {{ $selectedProduct ? 'Update Component' : 'Add Component' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="builder-sidebar">

                <div class="glass-card p-4">
                    <h2 class="h4 mb-3">Build Summary</h2>

                    <div class="summary-line">
                        <span>Selected Components</span>
                        <strong>{{ $analysis['selectedCount'] }} / {{ $analysis['requiredCount'] }}</strong>
                    </div>

                    <div class="summary-line">
                        <span>Total Price</span>
                        <strong>Tk {{ number_format($analysis['totalPrice'], 0) }}</strong>
                    </div>

                    <div class="summary-line">
                        <span>Estimated Wattage</span>
                        <strong>{{ $analysis['estimatedWattage'] }}W</strong>
                    </div>

                    <div class="compat-overall compat-{{ $analysis['overallStatus'] }} mt-4">
                        <strong>{{ ucfirst(str_replace('_', ' ', $analysis['overallStatus'])) }}</strong>
                        <p>{{ $analysis['overallMessage'] }}</p>
                    </div>

                    @if (count($analysis['missingCategories']) > 0)
                        <div class="mt-4">
                            <h3 class="h6">Missing Components</h3>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($analysis['missingCategories'] as $missingCategory)
                                    <span class="missing-pill">{{ $missingCategory }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="glass-card p-4">
                    <h2 class="h4 mb-3">Compatibility Checks</h2>

                    @if (count($analysis['checks']) === 0)
                        <p class="text-secondary mb-0">
                            Select related components to begin compatibility checking.
                        </p>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach ($analysis['checks'] as $check)
                                <div class="compat-card compat-card-{{ $check['status'] }}">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <strong>{{ $check['title'] }}</strong>
                                        <span>{{ ucfirst($check['status']) }}</span>
                                    </div>
                                    <p>{{ $check['message'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection