@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Admin Dashboard</h2>
        <p class="section-subtitle mb-0">
            Manage products, specifications, inventory, prices, and orders.
        </p>
    </div>

    <span class="status-pill mt-3 mt-md-0">Admin Area</span>
</div>

<div class="notice-card mb-4">
    Admin product, inventory, specification, and order management will be implemented in later sprints.
</div>

<div class="row g-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h5>Products</h5>
            <p class="number mb-0">0</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h5>Orders</h5>
            <p class="number mb-0">0</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h5>Builds</h5>
            <p class="number mb-0">0</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <h5>Users</h5>
            <p class="number mb-0">0</p>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-md-4">
        <div class="glass-card">
            <div class="icon-box green">CPU</div>
            <h5 class="fw-bold">Product Management</h5>
            <p class="empty-text mb-0">
                Add, update, and delete PC components.
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card">
            <div class="icon-box pink">✦</div>
            <h5 class="fw-bold">Specification Management</h5>
            <p class="empty-text mb-0">
                Manage sockets, RAM types, wattage, and compatibility-related details.
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card">
            <div class="icon-box blue">◈</div>
            <h5 class="fw-bold">Order Management</h5>
            <p class="empty-text mb-0">
                View customer orders and update order status.
            </p>
        </div>
    </div>
</div>
@endsection