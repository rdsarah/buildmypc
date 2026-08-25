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
    <!-- Global Store Settings Panel -->
    <div class="col-12">
        <div class="glass-card d-flex flex-column flex-md-row justify-content-between align-items-md-center p-3">
            <div class="mb-3 mb-md-0">
                <h5 class="fw-bold mb-1">Global Store Settings</h5>
                <p class="text-muted small mb-0">Automatically hide products with 0 stock from customers across the catalog and PC builder.</p>
            </div>
            <form action="{{ route('admin.settings.toggleOutOfStock') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn {{ $hideOutOfStock === '1' ? 'btn-danger' : 'btn-success' }} fw-bold">
                    {{ $hideOutOfStock === '1' ? 'Disable Auto-Hide (Currently ON)' : 'Enable Auto-Hide (Currently OFF)' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Inventory Management Panel -->
    <div class="col-12">
        <div class="glass-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="fw-bold mb-0">Inventory Management</h5>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-fern">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
                <span class="empty-text">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle" style="background: rgba(13, 26, 47, 0.45); border-radius: 12px; overflow: hidden;">
                    <thead>
                        <tr>
                            <th class="ps-3">Product Name & Category</th>
                            <th>Current Price</th>
                            <th>Stock Level</th>
                            <th class="pe-3 text-end" style="width: 320px;">Quick Manage & Actions</th>
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
                                    <div class="d-flex gap-2 justify-content-end align-items-center">
                                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="d-flex gap-1 mb-0">
                                            @csrf
                                            <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control form-control-sm" placeholder="Price" style="width: 80px;" required>
                                            <input type="number" name="stock" value="{{ $product->stock }}" class="form-control form-control-sm" placeholder="Stock" style="width: 60px;" required>
                                            <button type="submit" class="btn btn-fern btn-sm px-2">Save</button>
                                        </form>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-soft text-primary border-primary px-2">Edit</a>
                                        <form action="{{ route('admin.products.delete', $product->id) }}" method="POST" class="mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft text-danger border-danger px-2" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                        </form>
                                    </div>
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

    <!-- Build Templates Management -->
    <div class="col-lg-8">
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3">Starter Build Templates</h5>
            
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle" style="background: rgba(13, 26, 47, 0.45); border-radius: 12px; overflow: hidden;">
                    <thead>
                        <tr>
                            <th class="ps-3">Template Name</th>
                            <th>Target Use</th>
                            <th>Components Included</th>
                            <th>Total Price</th>
                            <th class="pe-3 text-end" style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-white">{{ $template->name }}</div>
                                    <div class="small text-muted">{{ $template->description }}</div>
                                </td>
                                <td>
                                    <span class="status-pill">{{ $template->target_use }}</span>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0 small text-white-50">
                                        @foreach($template->products as $p)
                                            <li>• {{ $p->category->name }}: {{ $p->name }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <span class="product-price">৳{{ number_format($template->total_price, 0) }}</span>
                                </td>
                                <td class="pe-3 text-end">
                                    <form action="{{ route('admin.templates.delete', $template->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft text-danger border-danger py-1 px-2" onclick="return confirm('Are you sure you want to delete this template?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No custom templates created.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Template Form -->
    <div class="col-lg-4">
        <div class="glass-card">
            <h5 class="fw-bold mb-3">Create Starter Build</h5>
            <form action="{{ route('admin.templates.create') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted small">Template Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Extreme 4K Gaming" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Description</label>
                    <textarea name="description" rows="2" class="form-control" placeholder="e.g. Best performance-per-dollar for gaming."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Target Use Focus</label>
                    <select name="target_use" class="form-select" required>
                        <option value="gaming">Gaming</option>
                        <option value="editing">Editing / Design</option>
                        <option value="office">Office / Productivity</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Select Pre-configured Components</label>
                    @foreach(['CPU', 'Motherboard', 'RAM', 'GPU', 'Storage', 'PSU', 'Casing'] as $cat)
                        <div class="mb-2">
                            <select name="products[]" class="form-select form-select-sm">
                                <option value="">Select {{ $cat }} (Optional)</option>
                                @isset($productsByCategory[$cat])
                                    @foreach($productsByCategory[$cat] as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->name }} (৳{{ number_format($prod->price, 0) }})</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-fern w-100">Create Template</button>
            </form>
        </div>
    </div>
</div>
@endsection