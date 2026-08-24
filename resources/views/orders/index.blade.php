@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Orders & Order Tracking</h2>
        <p class="section-subtitle mb-0">
            Track your placed orders, view component details, and check real-time delivery status.
        </p>
    </div>

    <a href="{{ route('cart.index') ?? '/cart' }}" class="btn btn-soft mt-3 mt-md-0">🛒 View Cart</a>
</div>

@if(session('success'))
    <div class="alert alert-success bg-success-subtle text-success border border-success-subtle rounded-3 mb-4 p-3" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger bg-danger-subtle text-danger border border-danger-subtle rounded-3 mb-4 p-3" role="alert">
        {{ session('error') }}
    </div>
@endif

@if($orders->isEmpty())
    <div class="glass-card text-center py-5">
        <div class="icon-box blue mx-auto">◈</div>
        <h4 class="fw-bold text-white mt-3">No orders yet</h4>
        <p class="empty-text mb-0">
            You haven't placed any orders yet.
        </p>
    </div>
@else
    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle mb-0" style="background: rgba(13, 26, 47, 0.45); border-radius: 12px; overflow: hidden;">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 100px;">Order ID</th>
                        <th>Customer Details</th>
                        <th>Ordered Components</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th class="pe-3 text-end" style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="ps-3 fw-bold text-white">#{{ $order->id }}</td>
                            <td>
                                <div class="fw-bold text-white">{{ $order->customer_name }}</div>
                                <div class="small text-muted">{{ $order->customer_email }}</div>
                                <div class="small text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0 small text-white-50">
                                    @foreach($order->items as $item)
                                        <li>• {{ $item->product?->name ?? 'Unknown Product' }} <span class="text-muted">(x{{ $item->quantity }})</span></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <span class="product-price">৳{{ number_format($order->total_price, 2) }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeColor = match($order->status) {
                                        'pending' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-info text-dark',
                                        'processing' => 'bg-primary text-white',
                                        'shipped' => 'bg-info text-white',
                                        'delivered' => 'bg-success text-white',
                                        'cancelled' => 'bg-danger text-white',
                                        default => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} text-capitalize px-2 py-1.5" style="font-size: 0.85rem; border-radius: 6px;">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                @if(in_array($order->status, ['pending', 'confirmed']))
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel order #{{ $order->id }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm px-3" style="border-radius: 999px;">Cancel Order</button>
                                    </form>
                                @else
                                    <span class="text-muted small">No actions available</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection