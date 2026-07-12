@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Custom PC Builder</h2>
        <p class="section-subtitle mb-0">
            Your future workspace for selecting parts and checking compatibility.
        </p>
    </div>

    <a href="/products" class="btn btn-soft mt-3 mt-md-0">Choose Components</a>
</div>

<div class="warning-card mb-4">
    Custom build management, summary calculation, and compatibility checking will be implemented in later sprints.
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="glass-card">
            <h5 class="fw-bold mb-3">Build Components</h5>

            @php
                $components = [
                    'CPU' => 'Processor not selected',
                    'Motherboard' => 'Main board not selected',
                    'RAM' => 'Memory not selected',
                    'GPU' => 'Graphics card not selected',
                    'Storage' => 'Storage device not selected',
                    'PSU' => 'Power supply not selected',
                    'Casing' => 'Case not selected',
                ];
            @endphp

            @foreach ($components as $type => $message)
                <div class="builder-row-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $type }}</h6>
                        <p class="empty-text mb-0">{{ $message }}</p>
                    </div>

                    <span class="status-pill">Pending</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-lg-4">
        <div class="glass-card mb-4">
            <h5 class="fw-bold">Build Summary</h5>
            <p class="empty-text mb-2">Total Price: ৳0</p>
            <p class="empty-text mb-2">Estimated Wattage: 0W</p>
            <p class="empty-text mb-0">Compatibility: Pending</p>
        </div>

        <div class="glass-card">
            <h5 class="fw-bold">Missing Components</h5>
            <p class="empty-text mb-0">
                Required components will be checked after the builder logic is added.
            </p>
        </div>
    </div>
</div>
@endsection