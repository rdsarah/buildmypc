@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-light mb-3">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
    <h2 class="section-title mb-1">Edit Product: {{ $product->name }}</h2>
    <p class="section-subtitle mb-0">Update PC component specifications and details.</p>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="glass-card">
    <form action="{{ route('admin.products.updateFull', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <!-- Basic Information -->
            <div class="col-md-12">
                <h5 class="fw-bold mb-3 border-bottom border-secondary pb-2">Basic Information</h5>
            </div>
            
            <div class="col-md-6">
                <label class="form-label text-white-50">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label text-white-50">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="form-control">
            </div>
            
            <div class="col-md-4">
                <label class="form-label text-white-50">Category <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label text-white-50">Brand</label>
                <select name="brand_id" class="form-select">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label text-white-50">Price (৳) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="form-control" required>
            </div>
            
            <div class="col-md-2">
                <label class="form-label text-white-50">Stock <span class="text-danger">*</span></label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control" required>
            </div>

            <div class="col-md-12">
                <label class="form-label text-white-50">Image URL</label>
                <input type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" class="form-control" placeholder="https://example.com/image.jpg">
            </div>
            
            <div class="col-md-12">
                <label class="form-label text-white-50">Description</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
            </div>
            
            <!-- Technical Specifications -->
            <div class="col-md-12 mt-5">
                <h5 class="fw-bold mb-3 border-bottom border-secondary pb-2">Technical Specifications <small class="text-muted fw-normal">(Optional)</small></h5>
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-white-50">Socket Type</label>
                <input type="text" name="socket_type" value="{{ old('socket_type', $product->socket_type) }}" class="form-control" placeholder="e.g. AM5, LGA1700">
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-white-50">RAM Type</label>
                <input type="text" name="ram_type" value="{{ old('ram_type', $product->ram_type) }}" class="form-control" placeholder="e.g. DDR4, DDR5">
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-white-50">Form Factor</label>
                <input type="text" name="form_factor" value="{{ old('form_factor', $product->form_factor) }}" class="form-control" placeholder="e.g. ATX, Micro-ATX">
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-white-50">Wattage (W)</label>
                <input type="number" name="wattage" value="{{ old('wattage', $product->wattage) }}" class="form-control" placeholder="e.g. 750, 850">
            </div>
            
            <div class="col-md-4">
                <label class="form-label text-white-50">Storage Type</label>
                <input type="text" name="storage_type" value="{{ old('storage_type', $product->storage_type) }}" class="form-control" placeholder="e.g. NVMe M.2, SATA SSD">
            </div>
            
            <div class="col-md-4">
                <label class="form-label text-white-50">GPU Length (mm)</label>
                <input type="number" step="0.1" name="gpu_length_mm" value="{{ old('gpu_length_mm', $product->gpu_length_mm) }}" class="form-control" placeholder="e.g. 320">
            </div>
            
            <div class="col-md-4">
                <label class="form-label text-white-50">Case Clearance (mm)</label>
                <input type="number" step="0.1" name="case_clearance_mm" value="{{ old('case_clearance_mm', $product->case_clearance_mm) }}" class="form-control" placeholder="e.g. 340">
            </div>

            <!-- Activation -->
            <div class="col-md-12 mt-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="isActiveSwitch" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label text-white-50" for="isActiveSwitch">Product is Active (Visible to customers)</label>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="col-md-12 mt-4 text-end">
                <button type="submit" class="btn btn-fern px-4 py-2 fw-bold">Update Product</button>
            </div>
        </div>
    </form>
</div>
@endsection
