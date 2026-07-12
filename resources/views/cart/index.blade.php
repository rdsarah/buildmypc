@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Cart</h2>
        <p class="section-subtitle mb-0">
            Selected build components will appear here before checkout.
        </p>
    </div>

    <a href="/builder" class="btn btn-fern mt-3 mt-md-0">Go to Builder</a>
</div>

<div class="glass-card text-center py-5">
    <div class="icon-box pink mx-auto">🛒</div>
    <h4 class="fw-bold">Your cart is empty</h4>
    <p class="empty-text mb-0">
        Cart management will be implemented in Sprint 4.
    </p>
</div>
@endsection