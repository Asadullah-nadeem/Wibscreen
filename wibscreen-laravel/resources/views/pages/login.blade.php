@extends('layouts.app')
@section('title', 'Sign In — Wibscreen')

@push('styles')
<style>
  .auth-logo { width: 42px; height: 42px; border-radius: 12px; margin-bottom: 20px; }
  .form-floating > .form-control:focus ~ label, .form-floating > .form-control:not(:placeholder-shown) ~ label { opacity: .65; transform: scale(.85) translateY(-.5rem) translateX(.15rem); }
</style>
@endpush

@section('content')
<div class="wb-auth-wrap">
  <div class="wb-auth-card">
    <div class="text-center">
      <a href="{{ route('home') }}" class="d-inline-block text-decoration-none">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" class="auth-logo shadow-sm">
      </a>
      <h1 class="wb-auth-title">Welcome back</h1>
      <p class="wb-auth-sub">Sign in to your browser workspace</p>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger border-0 small py-2 mb-4" style="background:rgba(239,68,68,0.1); color:#ef4444;">
        @foreach ($errors->all() as $error)
          <div><i class="fas fa-circle-exclamation me-1"></i> {{ $error }}</div>
        @endforeach
      </div>
    @endif

    @if (session('status'))
      <div class="alert alert-success border-0 small py-2 mb-4" style="background:rgba(34,197,94,0.1); color:#22c55e;">
        <i class="fas fa-check-circle me-1"></i> {{ session('status') }}
      </div>
    @endif

    <form id="login-form" method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-floating mb-3">
        <input type="email" name="email" id="login-email" class="form-control bg-transparent border-secondary-subtle rounded-3" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
        <label for="login-email" class="small">Email Address</label>
      </div>

      <div class="mb-3 position-relative">
        <div class="form-floating">
          <input type="password" name="password" id="login-pass" class="form-control bg-transparent border-secondary-subtle rounded-3 pe-5" placeholder="••••••••" required>
          <label for="login-pass" class="small">Password</label>
        </div>
        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 pe-3 opacity-50" id="toggle-pass">
          <i class="fas fa-eye" id="eye-icon"></i>
        </button>
      </div>

      <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="form-check">
          <input type="checkbox" name="remember" class="form-check-input" id="remember">
          <label class="form-check-label small text-body-secondary" for="remember">Remember me</label>
        </div>
        <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none fw-semibold">froget-password</a>
      </div>

      <button type="submit" class="wb-btn-primary mb-4">Sign In</button>
    </form>

    <div class="text-center border-top pt-4" style="border-color: var(--wb-border) !important;">
      <p class="small text-body-secondary mb-0">No account? <a href="{{ route('signup') }}" class="text-primary fw-bold text-decoration-none">Sinup Now</a></p>
      <a href="{{ route('home') }}" class="small text-body-secondary text-decoration-none d-inline-block mt-3 opacity-75 hover-opacity-100">← Back to Home</a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('toggle-pass').addEventListener('click', function() {
  const p = document.getElementById('login-pass'), i = document.getElementById('eye-icon');
  p.type = p.type === 'password' ? 'text' : 'password';
  i.className = p.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
});
</script>
@endpush
