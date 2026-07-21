@extends('layouts.app')

@section('content')
<section class="hero-card text-center">
    <div class="hero-content">
        <div class="hero-badge mx-auto">
            Custom PC Builder + Compatibility Checker
        </div>

        <h1 class="hero-title">
            Build Your <br>
            <span>Dream PC</span>
        </h1>

        <p class="hero-subtitle">
            Pick components, check compatibility, calculate estimated wattage, compare builds,
            and prepare your order from one clean workspace.
        </p>

        <div class="mt-4">
            <a href="/products" class="btn btn-fern btn-lg me-md-2">Browse Components</a>
            <a href="/builder" class="btn btn-soft btn-lg">Start PC Build</a>
        </div>
    </div>
</section>

<section class="mt-5">
    <div class="text-center mb-4">
        <h2 class="section-title">What BuildMyPC helps with</h2>
        <p class="section-subtitle">A simple workflow for choosing parts and avoiding incompatible builds.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('products.index') }}" class="feature-card-link">
                <div class="glass-card h-100">
                    <div class="icon-box green">⌁</div>
                    <h5>BROWSE COMPONENTS</h5>
                    <p>
                        Explore CPUs, GPUs, RAM, motherboards, storage, PSU, and casing in one catalog.
                    </p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('builder.index') }}" class="feature-card-link">
                <div class="glass-card h-100">
                    <div class="icon-box pink">✦</div>
                    <h5>CHECK COMPATIBILITY</h5>
                    <p>
                        Detect socket mismatch, RAM type issues, PSU limitations, and casing clearance problems.
                    </p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="/cart" class="feature-card-link">
                <div class="glass-card h-100">
                    <div class="icon-box blue">◈</div>
                    <h5>SAVE AND ORDER</h5>
                    <p>
                        Save builds, compare options, add selected components to cart, and track orders.
                    </p>
                </div>
            </a>
        </div>
    </div>
</section>
@endsection