@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Shopping Cart</h2>
        <p class="section-subtitle mb-0">
            Review your selected components and proceed to checkout.
        </p>
    </div>

    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('builder.index') }}" class="btn btn-soft">💻 PC Builder</a>
        @if(isset($cart) && count($cart) > 0)
            <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Clear all items from your cart?');">
                @csrf
                <button type="submit" class="btn btn-soft border-danger text-danger">Clear Cart</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="notice-card mb-4 text-success border-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="notice-card mb-4 text-danger border-danger">
        {{ session('error') }}
    </div>
@endif

@if(isset($cart) && count($cart) > 0)
    <div class="row g-4">
        <!-- CART ITEMS LIST -->
        <div class="col-lg-8">
            <div class="glass-card">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $id => $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if(!empty($item['image']))
                                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="bg-dark rounded d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;">
                                                    🖥️
                                                </div>
                                            @endif
                                            <div>
                                                <strong class="d-block text-white">{{ $item['name'] }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="product-price">৳{{ number_format($item['price'], 0) }}</td>
                                    <td style="width: 140px;">
                                        <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex align-items-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm bg-dark text-white border-secondary text-center">
                                            <button type="submit" class="btn btn-soft btn-sm" title="Update Quantity">🔄</button>
                                        </form>
                                    </td>
                                    <td class="product-price fw-bold">৳{{ number_format($item['price'] * $item['quantity'], 0) }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('cart.remove', $id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-soft border-danger text-danger btn-sm" title="Remove Item">
                                                🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="col-lg-4">
            <div class="glass-card">
                <h4 class="fw-bold text-white mb-3">Order Summary</h4>
                
                <div class="d-flex justify-content-between mb-2 text-muted">
                    <span>Items Count:</span>
                    <strong class="text-white">{{ array_sum(array_column($cart, 'quantity')) }}</strong>
                </div>

                <div class="d-flex justify-content-between mb-3 text-muted">
                    <span>Subtotal:</span>
                    <strong class="product-price">৳{{ number_format($subtotal, 0) }}</strong>
                </div>

                <hr class="border-secondary my-3">

                <div class="d-flex justify-content-between mb-4 fs-5 fw-bold text-white">
                    <span>Total Amount:</span>
                    <span class="product-price">৳{{ number_format($subtotal, 0) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-fern w-100 py-2 fw-bold">
                    Proceed to Checkout ➔
                </a>
            </div>
        </div>
    </div>
@else
    <div class="glass-card text-center py-5">
        <div class="icon-box pink mx-auto mb-3">🛒</div>
        <h4 class="fw-bold text-white">Your cart is empty</h4>
        <p class="empty-text mb-3">
            Add components from the PC Builder or Saved Gallery to get started!
        </p>
        <a href="{{ route('builder.index') }}" class="btn btn-fern">💻 Build a PC Now</a>
    </div>
@endif
@endsection