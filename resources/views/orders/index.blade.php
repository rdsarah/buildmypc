@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Orders</h2>
        <p class="section-subtitle mb-0">
            Track placed orders and delivery progress.
        </p>
    </div>

    <a href="/cart" class="btn btn-soft mt-3 mt-md-0">View Cart</a>
</div>

<div class="glass-card text-center py-5">
    <div class="icon-box blue mx-auto">◈</div>
    <h4 class="fw-bold">No orders yet</h4>
    <p class="empty-text mb-0">
        Order placement and tracking will be implemented in Sprint 4.
    </p>
</div>
@endsection