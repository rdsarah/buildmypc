@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Admin Dashboard</h2>
        <p class="section-subtitle mb-0">
            Manage products, stock levels, pricing, and customer orders.
        </p>
    </div>

    <span class="status-pill mt-3 mt-md-0">Admin Area</span>
</div>

@if(session('success'))
    <div class="notice-card mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h5>Total Products</h5>
            <p class="number mb-0">{{ $productsCount }}</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h5>Total Orders</h5>
            <p class="number mb-0">{{ $ordersCount }}</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h5>Total Revenue</h5>
            <p class="number mb-0" style="font-size: 1.6rem; margin-top: 0.4rem;">৳{{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h5>Users</h5>
            <p class="number mb-0">{{ $usersCount }}</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Inventory Management Panel -->
    <div class="col-12">
        <div class="glass-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Inventory Management</h5>
                <span class="empty-text">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle" style="background: rgba(13, 26, 47, 0.45); border-radius: 12px; overflow: hidden;">
                    <thead>
                        <tr>
                            <th class="ps-3">Product Name & Category</th>
                            <th>Current Price</th>
                            <th>Stock Level</th>
                            <th class="pe-3 text-end" style="width: 250px;">Update Specs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-white">{{ $product->name }}</div>
                                    <div class="small text-muted">{{ $product->brand?->name }} • {{ $product->category?->name }}</div>
                                </td>
                                <td>
                                    <span class="product-price">৳{{ number_format($product->price, 2) }}</span>
                                </td>
                                <td>
                                    @if($product->stock > 10)
                                        <span class="badge bg-success">In Stock ({{ $product->stock }})</span>
                                    @elseif($product->stock > 0)
                                        <span class="badge bg-warning text-dark">Low Stock ({{ $product->stock }})</span>
                                    @else
                                        <span class="badge bg-danger">Out of Stock ({{ $product->stock }})</span>
                                    @endif
                                </td>
                                <td class="pe-3">
                                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="d-flex gap-2 justify-content-end">
                                        @csrf
                                        <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control form-control-sm" placeholder="Price" style="width: 90px;" required>
                                        <input type="number" name="stock" value="{{ $product->stock }}" class="form-control form-control-sm" placeholder="Stock" style="width: 70px;" required>
                                        <button type="submit" class="btn btn-fern btn-sm px-3">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Order Management Panel -->
    <div class="col-12">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Order Management</h5>
                <span class="empty-text">Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle" style="background: rgba(13, 26, 47, 0.45); border-radius: 12px; overflow: hidden;">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 100px;">Order ID</th>
                            <th>Customer & Details</th>
                            <th>Ordered Components</th>
                            <th>Total Price</th>
                            <th class="pe-3 text-end" style="width: 280px;">Order Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
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
                                <td class="pe-3">
                                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="d-flex gap-2 justify-content-end align-items-center">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm" style="width: 140px; background-color: rgba(13, 26, 47, 0.9);" required>
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-soft btn-sm px-3">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No orders placed yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection