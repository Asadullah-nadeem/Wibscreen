<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Wibscreen'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="wb-navbar">
        <div class="container d-flex align-items-center justify-content-between gap-3">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none" style="color:inherit;">
                <div class="wb-logo-badge">WS</div>
                <span class="fw-bold" style="font-family:'Manrope',sans-serif;font-size:1.05rem;">Wibscreen</span>
            </a>
            <div class="d-none d-md-flex align-items-center gap-3">
                <a href="{{ route('about') }}" class="text-body-secondary text-decoration-none small fw-medium">About</a>
                <a href="{{ route('security') }}" class="text-body-secondary text-decoration-none small fw-medium">Security</a>
                <a href="{{ route('support') }}" class="text-body-secondary text-decoration-none small fw-medium">Support</a>
                <a href="{{ route('pricing') }}" class="text-body-secondary text-decoration-none small fw-medium">Pricing</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary btn-sm px-3">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-semibold">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm px-3">Sign In</a>
                    @if (Route::has('signup'))
                        <a href="{{ route('signup') }}" class="btn btn-primary btn-sm px-3 fw-semibold">Get Started</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="wb-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="text-body-secondary">&copy; {{ date('Y') }} Wibscreen. All rights reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <a href="{{ route('privacy') }}">Privacy</a>
                        <a href="{{ route('terms') }}">Terms</a>
                        <a href="{{ route('cookies') }}">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
