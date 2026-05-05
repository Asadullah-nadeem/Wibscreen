@extends('layouts.app')
@section('title', 'Sign In — Wibscreen')

@push('styles')
<style>
  .auth-logo { width: 48px; height: 48px; border-radius: 14px; }
  .form-floating > .form-control:focus ~ label, .form-floating > .form-control:not(:placeholder-shown) ~ label { opacity: .65; transform: scale(.85) translateY(-.5rem) translateX(.15rem); }
</style>
@endpush

@section('content')
<div class="wb-auth-container">
  <div class="wb-auth-card">
    <div class="text-center mb-5">
      <a href="{{ route('home') }}" class="d-inline-block text-decoration-none mb-4">
        <div class="bg-primary rounded-4 d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 56px; height: 56px;">
          <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" style="width: 36px; height: 36px;">
        </div>
      </a>
      <h1 class="h2 fw-bold display-font mb-2">Welcome back</h1>
      <p class="text-secondary">Sign in to your browser workspace</p>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger border-0 small py-3 mb-4 rounded-3 d-flex align-items-center" style="background:rgba(239,68,68,0.1); color:#ef4444;">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
        <div>
          @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      </div>
    @endif

    @if (session('status'))
      <div class="alert alert-success border-0 small py-3 mb-4 rounded-3 d-flex align-items-center" style="background:rgba(34,197,94,0.1); color:#22c55e;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>{{ session('status') }}</div>
      </div>
    @endif

    <form id="login-form" method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-floating mb-3">
        <input type="email" name="email" id="login-email" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
        <label for="login-email" class="text-secondary small fw-medium">Email Address</label>
      </div>

      <div class="mb-3 position-relative">
        <div class="form-floating">
          <input type="password" name="password" id="login-pass" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none pe-5" placeholder="••••••••" required>
          <label for="login-pass" class="text-secondary small fw-medium">Password</label>
        </div>
        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 pe-3 text-secondary" id="toggle-pass" style="z-index: 10;">
          <i class="bi bi-eye-fill" id="eye-icon"></i>
        </button>
      </div>

      <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="form-check">
          <input type="checkbox" name="remember" class="form-check-input shadow-none" id="remember">
          <label class="form-check-label small text-secondary fw-medium" for="remember">Remember me</label>
        </div>
        <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none fw-bold">Forgot password?</a>
      </div>

      <button type="submit" class="btn btn-primary-wb w-100 py-3 mb-4 fs-6">Sign In</button>
    </form>

    <div class="text-center border-top border-emphasis-subtle pt-4 mt-2">
      <p class="small text-secondary mb-0">Don't have an account? <a href="{{ route('signup') }}" class="text-primary fw-bold text-decoration-none">Sign Up Now</a></p>
      <a href="{{ route('home') }}" class="small text-secondary text-decoration-none d-inline-block mt-3 opacity-75 hover-opacity-100">
        <i class="bi bi-arrow-left me-1"></i> Back to Home
      </a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('toggle-pass').addEventListener('click', function() {
  const p = document.getElementById('login-pass'), i = document.getElementById('eye-icon');
  p.type = p.type === 'password' ? 'text' : 'password';
  i.className = p.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
});
</script>
@endpush
