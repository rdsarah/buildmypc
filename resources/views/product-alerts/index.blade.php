@extends('layouts.app')

@section('title', 'Product Alerts')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Product Alerts</h2>
        <p class="section-subtitle mb-0">
            Stay updated about price drops and products coming back in stock.
        </p>
    </div>

    @if($alerts->where('is_read', false)->count() > 0)
        <form action="{{ route('product-alerts.readAll') }}" method="POST" class="mt-3 mt-md-0">
            @csrf
            <button type="submit" class="btn btn-soft">
                Mark All as Read
            </button>
        </form>
    @endif
</div>

@if($alerts->count() > 0)

    <div class="row g-3">

        @foreach($alerts as $alert)

            <div class="col-12">

                <div class="glass-card {{ !$alert->is_read ? 'border border-success' : '' }}">

                    <div class="d-flex justify-content-between align-items-start gap-3">

                        <div class="d-flex gap-3">

                            <div style="font-size: 1.8rem;">
                                @if($alert->type === 'price_drop')
                                    💰
                                @elseif($alert->type === 'back_in_stock')
                                    📦
                                @else
                                    🔔
                                @endif
                            </div>

                            <div>
                                <h5 class="mb-1">
                                    {{ $alert->product->name ?? 'Product Alert' }}
                                </h5>

                                <p class="mb-2">
                                    {{ $alert->message }}
                                </p>

                                <small class="text-muted">
                                    {{ $alert->created_at->diffForHumans() }}
                                </small>
                            </div>

                        </div>

                        @if(!$alert->is_read)

                            <form action="{{ route('product-alerts.read', $alert) }}" method="POST">
                                @csrf

                                <button type="submit" class="btn btn-sm btn-fern">
                                    Mark Read
                                </button>
                            </form>

                        @else

                            <span class="status-pill">
                                Read
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        @endforeach

    </div>

    <div class="mt-4">
        {{ $alerts->links() }}
    </div>

@else

    <div class="glass-card text-center py-5">

        <div style="font-size: 3rem;" class="mb-3">
            🔔
        </div>

        <h4>No product alerts</h4>

        <p class="empty-text mb-3">
            We'll let you know when something on your wishlist
            drops in price or comes back in stock.
        </p>

        <a href="{{ route('products.index') }}" class="btn btn-fern">
            Browse Components
        </a>

    </div>

@endif

@endsection