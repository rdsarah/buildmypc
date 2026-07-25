@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="row justify-content-center my-5">
    <div class="col-md-6 col-lg-5">
        <div class="glass-card p-4">
            <h2 class="section-title text-center mb-4">Create an Account</h2>

            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3 bg-danger text-white border-0 py-2 px-3 rounded small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label text-light">Full Name</label>
                    <input type="text" name="name" class="form-control bg-dark text-white border-secondary" value="{{ old('name') }}" placeholder="John Doe" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light">Email Address</label>
                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" value="{{ old('email') }}" placeholder="user@example.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light">Password</label>
                    <input type="password" name="password" class="form-control bg-dark text-white border-secondary" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-fern w-100 py-2 mt-3">Create Account</button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-secondary">
                <a href="{{ route('login') }}" class="btn btn-soft py-2 w-100">
                    Already have an account? <span class="text-white fw-bold">Sign In</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection