@extends('layouts.app')

@section('title', 'Comparison History')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="section-title mb-2">Comparison History</h1>

        <p class="section-subtitle mb-0">
            View and manage your saved component comparisons.
        </p>
    </div>

    <a href="{{ route('comparison.index') }}" class="btn btn-soft">
        New Comparison
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($comparisons->count() > 0)
    <div class="row g-4">
        @foreach($comparisons as $comparison)
            <div class="col-md-6 col-xl-4">
                <div class="glass-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <span class="status-pill">
                                {{ $comparison->category->name }}
                            </span>

                            <h5 class="mt-3 mb-1">
                                Saved Comparison
                            </h5>

                            <p class="empty-text mb-0">
                                {{ $comparison->created_at->format('d M Y, h:i A') }}
                            </p>
                        </div>
                    </div>

                    <p class="mb-2">
                        <strong>Priority:</strong>
                        {{ ucwords(str_replace('_', ' ', $comparison->priority)) }}
                    </p>

                    <p class="mb-4">
                        <strong>Products compared:</strong>
                        {{ count($comparison->product_ids) }}
                    </p>

                    <div class="d-flex gap-2">
                        <a
                            href="{{ route('comparison.index', [
                                'category' => $comparison->category->slug,
                                'products' => $comparison->product_ids,
                                'priority' => $comparison->priority
                            ]) }}"
                            class="btn btn-fern flex-grow-1"
                        >
                            View Again
                        </a>

                        <form
                            method="POST"
                            action="{{ route('comparison.destroy', $comparison) }}"
                            onsubmit="return confirm('Delete this comparison?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="glass-card text-center py-5">
        <h4>No saved comparisons yet</h4>

        <p class="empty-text mb-4">
            Compare some components and save the result to see it here.
        </p>

        <a href="{{ route('comparison.index') }}" class="btn btn-fern">
            Start Comparing
        </a>
    </div>
@endif

@endsection