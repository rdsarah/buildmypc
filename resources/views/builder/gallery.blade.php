@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Community & Saved Builds</h2>
        <p class="section-subtitle mb-0">
            Browse saved custom configurations and starter templates. Clone, edit, or add them directly to your cart!
        </p>
    </div>
    <div>
        <a href="{{ route('builder.index') }}" class="btn btn-fern">
            💻 Go to PC Builder
        </a>
    </div>
</div>

@if(session('success'))
    <div class="notice-card mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    @forelse($builds as $build)
        @php
            $totalPrice = $build->products->sum('price');
            $totalWattage = $build->products->sum('wattage');
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="glass-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <!-- Header & Badges -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0 text-white">{{ $build->name }}</h5>
                        <div class="d-flex align-items-center gap-1">
                            <span class="status-pill me-1">{{ $build->target_use ?? 'Custom' }}</span>
                            
                            <!-- TRASH ICON (Triggers theme modal) -->
                            <button type="button" class="btn btn-sm text-danger border-0 p-0 fs-5" data-bs-toggle="modal" data-bs-target="#deleteBuildModal{{ $build->id }}" title="Delete Build">
                                🗑️
                            </button>
                        </div>
                    </div>

                    <p class="small text-muted mb-3">
                        {{ $build->description ?? 'No description provided for this build.' }}
                    </p>

                    <!-- Summary Stats -->
                    <div class="border-top border-bottom border-secondary py-2 my-3">
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Total Price:</span>
                            <strong class="product-price">৳{{ number_format($totalPrice, 0) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span class="text-muted">Estimated Wattage:</span>
                            <strong class="text-white">{{ $totalWattage }}W</strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Total Components:</span>
                            <strong class="text-white">{{ $build->products->count() }} Parts</strong>
                        </div>
                    </div>

                    <!-- Component Preview List -->
                    <ul class="list-unstyled mb-3 small text-white-50">
                        @foreach($build->products->take(4) as $prod)
                            <li class="text-truncate">• <strong class="text-white">{{ $prod->category?->name ?? 'Part' }}:</strong> {{ $prod->name }}</li>
                        @endforeach
                        @if($build->products->count() > 4)
                            <li class="small text-muted fst-italic">+ {{ $build->products->count() - 4 }} more components...</li>
                        @endif
                    </ul>
                </div>

                <!-- Action Buttons Grid -->
                <div class="pt-2 border-top border-secondary">
                    <div class="d-flex gap-2 mb-2">
                        <!-- VIEW DETAILS BUTTON -->
                        <button type="button" class="btn btn-soft w-100" data-bs-toggle="modal" data-bs-target="#buildDetailsModal{{ $build->id }}">
                            👁️ Details
                        </button>

                        <!-- EDIT BUILD BUTTON -->
                        <a href="{{ route('builder.edit', $build->id) }}" class="btn btn-soft w-100 text-center">
                            ✏️ Edit
                        </a>
                    </div>

                    <!-- ADD TO CART BUTTON -->
                    <form action="{{ route('builder.addToCart', $build->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-fern w-100">
                            🛒 Add All to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Custom Dark Theme Confirmation Modal -->
        <div class="modal fade" id="deleteBuildModal{{ $build->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $build->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content glass-card text-white border-secondary">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel{{ $build->id }}">
                            ⚠️ Confirm Deletion
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <p class="fs-5 mb-2">Are you sure you want to delete <strong class="text-white">"{{ $build->name }}"</strong>?</p>
                        <p class="text-muted small mb-0">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer border-secondary justify-content-center gap-2">
                        <button type="button" class="btn btn-soft px-4" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('builder.destroy', $build->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-soft border-danger text-danger px-4">
                                🗑️ Delete Build
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Modal for View Details -->
        <div class="modal fade" id="buildDetailsModal{{ $build->id }}" tabindex="-1" aria-labelledby="buildModalLabel{{ $build->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content glass-card text-white">
                    <div class="modal-header border-secondary">
                        <div>
                            <h5 class="modal-title fw-bold" id="buildModalLabel{{ $build->id }}">{{ $build->name }}</h5>
                            <span class="badge bg-secondary mt-1">{{ $build->target_use ?? 'Custom Build' }}</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if($build->description)
                            <p class="text-muted small mb-3">{{ $build->description }}</p>
                        @endif

                        <h6 class="fw-bold mb-3 text-uppercase text-muted small">Included Components</h6>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle rounded overflow-hidden">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Component</th>
                                        <th>Product Name</th>
                                        <th>Wattage</th>
                                        <th class="text-end">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($build->products as $product)
                                        <tr>
                                            <td class="fw-bold text-muted small text-uppercase">{{ $product->category?->name ?? 'N/A' }}</td>
                                            <td class="text-white fw-semibold">{{ $product->name }}</td>
                                            <td>{{ $product->wattage ? $product->wattage.'W' : '-' }}</td>
                                            <td class="text-end product-price">৳{{ number_format($product->price, 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No components listed in this build.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Footer inside Modal -->
                        <div class="d-flex justify-content-between align-items-center bg-dark p-3 rounded mt-3 border border-secondary">
                            <div>
                                <span class="text-muted small d-block">Estimated Wattage</span>
                                <strong class="fs-5 text-white">{{ $totalWattage }}W</strong>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small d-block">Total Estimated Price</span>
                                <strong class="fs-4 product-price">৳{{ number_format($totalPrice, 0) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Close</button>
                        <a href="{{ route('builder.edit', $build->id) }}" class="btn btn-soft">✏️ Edit Build</a>
                        <form action="{{ route('builder.addToCart', $build->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-fern">🛒 Add All to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="glass-card py-5">
                <h4 class="text-muted mb-3">No Saved Builds Found</h4>
                <p class="text-muted mb-4">Be the first to build a custom setup and save it to the gallery!</p>
                <a href="{{ route('builder.index') }}" class="btn btn-fern">Start Building Now</a>
            </div>
        </div>
    @endforelse
</div>
@endsection