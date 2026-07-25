@extends('layouts.app')

@section('title', 'Admin Portal')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-6 col-lg-5">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Admin Portal</h2>
                <span class="status-pill">Staff Only</span>
            </div>

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label text-light">Admin Email</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" placeholder="admin@buildmypc.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light">Secret Key / Password</label>
                    <input type="password" name="password" class="form-control bg-dark text-white border-secondary" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-fern w-100 py-2 mt-3">Access Admin Dashboard</button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-secondary">
                <p class="text-muted small mb-2">Not an admin?</p>
                <a href="{{ route('login') }}" class="btn btn-soft w-100 py-2">
                    User Sign In
                </a>
            </div>
        </div>
    </div>
</div>
@endsection