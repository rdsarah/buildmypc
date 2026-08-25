@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Custom PC Builder</h2>
        <p class="section-subtitle mb-0">
            Select parts, check compatibility, calculate estimated wattage, and clone starter templates.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="/products" class="btn btn-soft">Choose Components</a>
        <a href="{{ route('gallery.index') }}" class="btn btn-soft">View Gallery</a>
        @if(count($selectedProducts) > 0)
            <form action="{{ route('builder.clear') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-soft text-danger border-danger">Clear Build</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="notice-card mb-4">
        {{ session('success') }}
    </div>
@endif

@if(count($selectedProducts) > 0)

    @if(!$isBuildComplete)

        <div class="warning-card mb-4">
            <h5 class="fw-bold mb-2">⚠️ Build Incomplete</h5>

            <p class="mb-2">
                Select all required components before compatibility can be checked.
            </p>

            <div class="small">
                Missing:
                <strong>{{ implode(', ', $missingComponents) }}</strong>
            </div>
        </div>

    @elseif(count($warnings) > 0)

        <div class="warning-card mb-4">
            <h5 class="fw-bold mb-2">⚠️ Compatibility Warnings Detected</h5>

            <ul class="mb-0 ps-3">
                @foreach($warnings as $warning)
                    <li class="mb-1">
                        <strong>{{ $warning['title'] }}</strong>:
                        {{ $warning['details'] }}
                    </li>
                @endforeach
            </ul>
        </div>

    @else

        <div class="notice-card mb-4 text-success border-success"
             style="background: rgba(9, 219, 199, 0.05);">
            ✅ All selected components are compatible.
        </div>

    @endif

@endif

<div class="row g-4">
    <!-- Builder Table -->
    <div class="col-lg-8">
        <div class="glass-card">
            <h5 class="fw-bold mb-3">Build Components</h5>

            @php
                $components = [
                    'CPU' => ['desc' => 'Processor not selected', 'slug' => 'cpu'],
                    'Motherboard' => ['desc' => 'Main board not selected', 'slug' => 'motherboard'],
                    'RAM' => ['desc' => 'Memory not selected', 'slug' => 'ram'],
                    'GPU' => ['desc' => 'Graphics card not selected', 'slug' => 'gpu'],
                    'Storage' => ['desc' => 'Storage device not selected', 'slug' => 'storage'],
                    'PSU' => ['desc' => 'Power supply not selected', 'slug' => 'psu'],
                    'Casing' => ['desc' => 'Case not selected', 'slug' => 'casing'],
                ];
            @endphp

            @foreach ($components as $type => $info)
                @php
                    $product = $selectedProducts->get($type);
                @endphp
                <div class="builder-row-card d-flex justify-content-between align-items-center mb-3">
                    <div class="pe-3">
                        <h6 class="fw-bold mb-1 text-uppercase">{{ $type }}</h6>
                        @if($product)
                            <div class="fw-bold text-white">{{ $product->name }}</div>
                            <div class="small text-muted">
                                {{ $product->brand?->name }} • {{ $product->model }}
                                @if($product->wattage) • {{ $product->wattage }}W @endif
                            </div>
                        @else
                            <p class="empty-text mb-0">{{ $info['desc'] }}</p>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @if($product)
                            <span class="product-price">৳{{ number_format($product->price, 0) }}</span>
                            <form action="{{ route('builder.remove', $type) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-soft text-danger border-danger py-1 px-2">Remove</button>
                            </form>
                        @else
                            <a href="/products?category={{ $info['slug'] }}" class="btn btn-sm btn-fern py-1 px-3">Choose</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Sidebar: Stats & Auto-suggestion -->
    <div class="col-lg-4">
        <!-- Summary Card -->
        <div class="glass-card mb-4">
            <h5 class="fw-bold mb-3 text-uppercase">Build Summary</h5>
            <div class="mb-2">
                <span class="empty-text">Total Price:</span> 
                <strong class="product-price fs-4">৳{{ number_format($totalPrice, 0) }}</strong>
            </div>
            <div class="mb-2">
                <span class="empty-text">Estimated Wattage:</span> 
                <strong class="text-white">{{ $totalWattage }}W</strong>
            </div>
            <div class="mb-3">
                <span class="empty-text">Compatibility:</span>

                @if(count($selectedProducts) == 0)
                    <span class="badge bg-secondary">Empty Build</span>

                @elseif(!$isBuildComplete)
                    <span class="badge bg-warning text-dark">Incomplete</span>

                @elseif(count($warnings) > 0)
                    <span class="badge bg-danger">Issues Found</span>

                @else
                    <span class="badge bg-success">Compatible</span>
                @endif
            </div>

            <!-- SAVE BUILD BUTTON -->
            @if(count($selectedProducts) > 0)
                <button type="button" class="btn btn-fern w-100" data-bs-toggle="modal" data-bs-target="#saveBuildModal">
                    💾 Save Build to Gallery
                </button>
            @endif
        </div>

        <!-- Budget-Based build suggestion -->
        <div class="glass-card">
            <h5 class="fw-bold mb-3 text-uppercase">Auto-Suggest Build</h5>
            <p class="small text-muted mb-3">Auto-populate a compatible build based on your target budget and usage focus.</p>
            
            <form action="{{ route('builder.suggest') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted small">Target Budget (৳)</label>
                    <input type="number" name="budget" value="80000" min="20000" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Category Focus</label>
                    <select name="focus" class="form-select" required>
                        <option value="gaming">Gaming (Focus GPU & CPU)</option>
                        <option value="editing">Editing/Design (Focus CPU, RAM, SSD)</option>
                        <option value="office">Office/Productivity (Balanced, No GPU)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-fern w-100">Generate Suggestion</button>
            </form>
        </div>
    </div>
</div>

<!-- Starter Templates -->
<section class="mt-5">
    <div class="text-center mb-4">
        <h2 class="section-title mb-1">Starter Build Templates</h2>
        <p class="section-subtitle mb-0">Pre-configured compatible builds curated by Admins. Clone them to start customizing.</p>
    </div>

    <div class="row g-4">
        @forelse($templates as $template)
            <div class="col-md-4">
                <div class="glass-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0 text-white">{{ $template->name }}</h5>
                            <span class="status-pill">{{ $template->target_use }}</span>
                        </div>
                        <p class="small text-muted mb-3">{{ $template->description }}</p>
                        
                        <div class="border-top border-secondary pt-2 mb-3">
                            <div class="d-flex justify-content-between mb-1 small text-white-50">
                                <span>Estimated Price:</span>
                                <strong class="product-price">৳{{ number_format($template->total_price, 0) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-white-50">
                                <span>Estimated Wattage:</span>
                                <strong>{{ $template->total_wattage }}W</strong>
                            </div>
                            
                            <ul class="list-unstyled mb-0 mt-2 small text-white-50">
                                @foreach($template->products as $prod)
                                    <li>• {{ $prod->category->name }}: {{ $prod->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <form action="{{ route('builder.clone', $template->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-soft w-100">Clone Build</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted">
                No starter templates configured yet.
            </div>
        @endforelse
    </div>
</section>

<!-- Modal for Saving Build -->
<div class="modal fade" id="saveBuildModal" tabindex="-1" aria-labelledby="saveBuildModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-card text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="saveBuildModalLabel">Save PC Build</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('builder.save') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Build Title</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. My Ultimate Gaming Rig" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief info about this build setup..."></textarea>
                    </div>

                    {{-- Pass selected product IDs --}}
                    @foreach($selectedProducts as $product)
                        <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
                    @endforeach
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-fern">Save Build</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection