@extends('layouts.app')
@section('title', 'froget-password — Wibscreen')

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
      <h1 class="wb-auth-title">froget-password</h1>
      <p class="wb-auth-sub">Enter your email and we'll send you a secure link to reset your password.</p>
    </div>

    @if (session('status'))
      <div class="alert alert-success border-0 small py-3 mb-4" style="background:rgba(34,197,94,0.1); color:#22c55e;">
        <i class="fas fa-paper-plane me-2"></i> {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger border-0 small py-2 mb-4" style="background:rgba(239,68,68,0.1); color:#ef4444;">
        @foreach ($errors->all() as $error)
          <div><i class="fas fa-circle-exclamation me-1"></i> {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="form-floating mb-4">
        <input type="email" name="email" id="reset-email" class="form-control bg-transparent border-secondary-subtle rounded-3" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
        <label for="reset-email" class="small">Email Address</label>
      </div>

      <button type="submit" class="wb-btn-primary mb-4">Send Reset Link</button>
    </form>

    <div class="text-center border-top pt-4" style="border-color: var(--wb-border) !important;">
      <p class="small text-body-secondary mb-0">Wait, I remember! <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
    </div>
  </div>
</div>
@endsection
