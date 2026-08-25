@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="glass-card text-white p-4">
                <h3 class="fw-bold mb-3">Order Checkout</h3>
                <p class="text-muted">Review your items before proceeding to Stripe Secure Checkout.</p>

                <div class="table-responsive mb-4">
                    <table class="table table-dark table-hover align-middle rounded overflow-hidden">
                        <thead>
                            <tr class="text-muted small">
                                <th>Item</th>
                                <th>Qty</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $item)
                                <tr>
                                    <td class="text-white">{{ $item['name'] ?? 'PC Component' }}</td>
                                    <td>{{ $item['quantity'] ?? 1 }}</td>
                                    <td class="text-end product-price">৳{{ number_format(($item['price'] ?? 0), 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top border-secondary pt-3 mb-4">
                    <h5 class="mb-0 fw-bold">Total Amount</h5>
                    <h4 class="mb-0 product-price fw-bold">৳{{ number_format($total, 0) }}</h4>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('cart.index') }}" class="btn btn-soft">← Back to Cart</a>
                    <button id="checkout-button" class="btn btn-fern px-4 py-2 fw-bold">
                        💳 Pay with Card (Stripe)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripeKey = "{{ config('services.stripe.key') }}";
        
        if (!stripeKey) {
            alert("Stripe Publishable Key is missing! Check your config/services.php and .env files.");
            return;
        }

        const stripe = Stripe(stripeKey);
        const checkoutButton = document.getElementById('checkout-button');

        if (checkoutButton) {
            checkoutButton.addEventListener('click', function (e) {
                e.preventDefault();
                
                checkoutButton.disabled = true;
                checkoutButton.innerText = 'Redirecting to Stripe...';

                fetch("{{ route('checkout.session') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(async (response) => {
                    const data = await response.json();
                    if (!response.ok || data.error) {
                        throw new Error(data.error || 'Server responded with error');
                    }
                    return data;
                })
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }
                    return stripe.redirectToCheckout({ sessionId: data.id });
                })
                .catch(error => {
                    console.error("Stripe Error Details:", error);
                    alert("Payment Error: " + error.message);
                    checkoutButton.disabled = false;
                    checkoutButton.innerText = '💳 Pay with Card (Stripe)';
                });
            });
        }
    });
</script>
@endsection