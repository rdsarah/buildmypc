@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Checkout</h2>
        <p class="section-subtitle mb-0">
            Review your order summary before completing payment via Stripe.
        </p>
    </div>

    <a href="{{ route('cart.index') }}" class="btn btn-soft mt-3 mt-md-0">← Back to Cart</a>
</div>

<div class="row g-4">
    <!-- ORDER ITEMS OVERVIEW -->
    <div class="col-lg-7">
        <div class="glass-card">
            <h5 class="fw-bold text-white mb-3">Order Items</h5>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th>Component</th>
                            <th>Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $item)
                            <tr>
                                <td>
                                    <strong class="text-white d-block">{{ $item['name'] }}</strong>
                                    <small class="text-muted">৳{{ number_format($item['price'], 0) }} each</small>
                                </td>
                                <td>{{ $item['quantity'] }}</td>
                                <td class="text-end product-price fw-bold">৳{{ number_format($item['price'] * $item['quantity'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- STRIPE PAYMENT BRIDGE CARD -->
    <div class="col-lg-5">
        <div class="glass-card">
            <h5 class="fw-bold text-white mb-3">Payment Summary</h5>

            <div class="d-flex justify-content-between mb-2 text-muted">
                <span>Items Count:</span>
                <strong class="text-white">{{ array_sum(array_column($cart, 'quantity')) }}</strong>
            </div>

            <div class="d-flex justify-content-between mb-3 text-muted">
                <span>Payment Gateway:</span>
                <span class="badge bg-primary">Stripe Secure</span>
            </div>

            <hr class="border-secondary my-3">

            <div class="d-flex justify-content-between mb-4 fs-4 fw-bold text-white">
                <span>Total Payable:</span>
                <span class="product-price">৳{{ number_format($subtotal, 0) }}</span>
            </div>

          <a href="{{ route('checkout.stripe') }}" class="btn btn-fern w-100 py-3 fw-bold fs-6 text-center text-decoration-none d-block">
                💳 Proceed to Stripe Payment (৳{{ number_format($subtotal, 0) }})
            </a>

            <p class="text-muted small text-center mt-3 mb-0">
                🔒 You will be securely redirected to Stripe's payment portal.
            </p>
        </div>
    </div>
</div>
@endsection