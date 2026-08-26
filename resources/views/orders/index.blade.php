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
    <div class="notice-card mb-4 text-success border-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="notice-card mb-4 text-danger border-danger">
        {{ session('error') }}
    </div>
@endif

<!-- TRACKING LOOKUP CARD -->
<div class="glass-card mb-4">
    <h5 class="fw-bold mb-3 text-white">🔍 Track Order by Number</h5>
    <form action="{{ route('orders.index') }}" method="GET" class="row g-2">
        <div class="col-md-9">
            <input type="text" name="order_number" class="form-control bg-dark text-white border-secondary" placeholder="Enter Order Number (e.g., ORD-100234)" value="{{ request('order_number') }}" required>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-fern w-100">Track Order</button>
        </div>
    </form>
</div>

<!-- TRACKED ORDER RESULT (IF SEARCHED) -->
@if(isset($trackedOrder) && $trackedOrder)
    <div class="glass-card mb-4 border-info">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <span class="badge bg-info text-dark mb-1">Tracked Result</span>
                <h4 class="fw-bold text-white mb-0">Order #{{ $trackedOrder->order_number }}</h4>
            </div>
            @php
                $badgeClass = match($trackedOrder->status) {
                    'pending'    => 'bg-warning text-dark',
                    'processing' => 'bg-info text-dark',
                    'shipped'    => 'bg-primary',
                    'delivered'  => 'bg-success',
                    'cancelled'  => 'bg-danger',
                    default      => 'bg-secondary',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($trackedOrder->status) }}</span>
        </div>

        <p class="text-white-50 small">Placed on: {{ $trackedOrder->created_at ? $trackedOrder->created_at->format('M d, Y h:i A') : 'N/A' }}</p>

        <!-- Actions for Tracked Result -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('orders.invoice', $trackedOrder->id) }}" class="btn btn-soft border-info text-info btn-sm">
                📄 Download Invoice
            </a>

            @if(in_array($trackedOrder->status, ['pending', 'processing']))
                <form action="{{ route('orders.cancel', $trackedOrder->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    <button type="submit" class="btn btn-soft border-danger text-danger btn-sm">
                        ❌ Cancel Order
                    </button>
                </form>
            @endif
        </div>

        @if($trackedOrder->status === 'cancelled')
            <div class="notice-card mb-3 text-muted">
                <strong>Cancelled by:</strong> {{ ucfirst($trackedOrder->cancelled_by ?? 'System') }} | 
                <strong>Reason:</strong> {{ $trackedOrder->cancellation_reason ?? 'N/A' }}
            </div>
        @endif

        <h6 class="fw-bold text-white mb-2">Order Items</h6>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle rounded overflow-hidden">
                <thead>
                    <tr class="text-muted small">
                        <th>Product</th>
                        <th>Qty</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trackedOrder->items as $item)
                        <tr>
                            <td class="text-white">{{ $item->product->name ?? 'Component' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-end product-price">৳{{ number_format($item->price, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-end border-top border-secondary pt-2">
            <strong class="fs-5 product-price">Total: ৳{{ number_format($trackedOrder->total_amount ?? $trackedOrder->total_price ?? 0, 0) }}</strong>
        </div>
    </div>
@endif

<!-- USER ORDER HISTORY LIST -->
<h4 class="fw-bold text-white mb-3">Order History</h4>

@if(isset($orders) && $orders->count() > 0)
    <div class="row g-3">
        @foreach($orders as $order)
            @php
                $statusBadge = match($order->status) {
                    'pending'    => 'bg-warning text-dark',
                    'processing' => 'bg-info text-dark',
                    'shipped'    => 'bg-primary',
                    'delivered'  => 'bg-success',
                    'cancelled'  => 'bg-danger',
                    default      => 'bg-secondary',
                };
            @endphp
            <div class="col-12">
                <div class="glass-card">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold mb-0 text-white">#{{ $order->order_number }}</h5>
                                <span class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span>
                            </div>
                            <small class="text-white-50">
                                Placed on {{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }} • 
                                {{ $order->items ? $order->items->count() : 0 }} Items
                            </small>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                            <strong class="product-price me-2">৳{{ number_format($order->total_amount ?? $order->total_price ?? 0, 0) }}</strong>

                            <!-- Direct Invoice Download Button -->
                            <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-soft border-info text-info btn-sm" title="Download PDF Invoice">
                                📄 Invoice
                            </a>

                            <!-- View Items Modal Trigger -->
                            <button type="button" class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                👁️ Details
                            </button>

                            <!-- Cancel Button (Only if Pending or Processing) -->
                            @if(in_array($order->status, ['pending', 'processing']))
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel order #{{ $order->order_number }}?');" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-soft border-danger text-danger btn-sm">
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($order->status === 'cancelled')
                        <div class="mt-2 pt-2 border-top border-secondary text-muted small">
                            ⚠️ Cancelled by {{ ucfirst($order->cancelled_by ?? 'System') }} — Reason: {{ $order->cancellation_reason ?? 'N/A' }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal for Order Details -->
            <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content glass-card text-white">
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title fw-bold">Order #{{ $order->order_number }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Status:</span>
                                <span class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span>
                            </div>
                            <h6 class="fw-bold mb-2">Purchased Items</h6>
                            <ul class="list-group list-group-flush rounded bg-dark mb-3">
                                @foreach($order->items as $item)
                                    <li class="list-group-item bg-transparent text-white border-secondary d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="d-block">{{ $item->product->name ?? 'Component' }}</strong>
                                            <small class="text-muted">Qty: {{ $item->quantity }} × ৳{{ number_format($item->price, 0) }}</small>
                                        </div>
                                        <span class="product-price">৳{{ number_format($item->quantity * $item->price, 0) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total Amount:</span>
                                <span class="product-price">৳{{ number_format($order->total_amount ?? $order->total_price ?? 0, 0) }}</span>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary justify-content-between">
                            <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-fern btn-sm">
                                📄 Download Invoice PDF
                            </a>
                            <button type="button" class="btn btn-soft btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@else
    <div class="glass-card text-center py-5">
        <div class="icon-box blue mx-auto mb-3">📦</div>
        <h4 class="fw-bold text-white">No Orders Found</h4>
        <p class="empty-text mb-3">
            You haven't placed any orders yet. Build a custom PC or browse parts to get started!
        </p>
        <a href="{{ route('builder.index') }}" class="btn btn-fern">💻 Open PC Builder</a>
    </div>
@endif
@endsection