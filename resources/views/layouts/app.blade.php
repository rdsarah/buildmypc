<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildMyPC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">BUILDMYPC</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="/products" class="nav-link">Components</a>
                    </li>
                    <li class="nav-item">
                        <a href="/builder" class="nav-link">PC Builder</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('comparison.index') }}" class="nav-link">Compare</a>
                    </li>
                    <li class="nav-item">
                        <a href="/cart" class="nav-link">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a href="/orders" class="nav-link">Orders</a>
                    </li>

                    <!-- Authentication Action Group -->
                    @if(auth()->check())
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <span class="text-light me-2 small">Hi, {{ auth()->user()->name ?? 'User' }}</span>
                        </li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-soft btn-sm px-3 py-2">
                                    Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0 d-flex gap-2">
                            <a href="{{ route('login') }}" class="btn btn-soft btn-sm px-3 py-2 d-inline-flex align-items-center">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-fern btn-sm px-3 py-2 d-inline-flex align-items-center">
                                Sign Up
                            </a>
                            <a href="{{ route('admin.login') }}" class="btn btn-soft btn-sm px-3 py-2 d-inline-flex align-items-center border-warning text-warning">
                                Admin Portal
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 page-shell">
        @yield('content')
    </main>

    <footer class="footer-custom text-center py-3 mt-5">
        <p class="mb-0">BuildMyPC — Custom PC Builder and Compatibility Checker</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>