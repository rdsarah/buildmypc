@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="section-title mb-1">Component Catalog</h2>
        <p class="section-subtitle mb-0">
            Browse PC parts by category. Search and filters will be added in Sprint 2.
        </p>
    </div>

    <a href="/builder" class="btn btn-fern mt-3 mt-md-0">Start a Build</a>
</div>

<div class="notice-card mb-4">
    Product browsing, search, filtering, sorting, and product detail pages will be implemented in Sprint 2.
</div>

<div class="row g-4">
    <div class="col-md-3 col-sm-6">
        <div class="category-card">
            <div class="icon-box green mx-auto">CPU</div>
            <h5>Processors</h5>
            <p>Socket, cores, wattage</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="category-card">
            <div class="icon-box pink mx-auto">MB</div>
            <h5>Motherboards</h5>
            <p>Socket, chipset, form factor</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="category-card">
            <div class="icon-box blue mx-auto">RAM</div>
            <h5>Memory</h5>
            <p>DDR type, capacity, speed</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="category-card">
            <div class="icon-box green mx-auto">GPU</div>
            <h5>Graphics Cards</h5>
            <p>Power draw, length, model</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="category-card">
            <div class="icon-box pink mx-auto">SSD</div>
            <h5>Storage</h5>
            <p>SSD, HDD, NVMe</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="category-card">
            <div class="icon-box blue mx-auto">PSU</div>
            <h5>Power Supply</h5>
            <p>Wattage and efficiency</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="category-card">
            <div class="icon-box green mx-auto">CASE</div>
            <h5>Casing</h5>
            <p>Clearance and form factor</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="category-card">
            <div class="icon-box pink mx-auto">ALL</div>
            <h5>All Parts</h5>
            <p>Full catalog view</p>
        </div>
    </div>
</div>
@endsection