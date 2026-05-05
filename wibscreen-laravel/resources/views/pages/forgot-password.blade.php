@extends('layouts.app')
@section('title', 'Forgot Password — Wibscreen')

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
      <h1 class="h2 fw-bold display-font mb-2">Forgot Password</h1>
      <p class="text-secondary">Enter your email and we'll send you a secure link to reset your password.</p>
    </div>

    @if (session('status'))
      <div class="alert alert-success border-0 small py-3 mb-4 rounded-3 d-flex align-items-center" style="background:rgba(34,197,94,0.1); color:#22c55e;">
        <i class="bi bi-send-check-fill me-2 fs-5"></i>
        <div>{{ session('status') }}</div>
      </div>
    @endif

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

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="form-floating mb-4">
        <input type="email" name="email" id="reset-email" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
        <label for="reset-email" class="text-secondary small fw-medium">Email Address</label>
      </div>

      <button type="submit" class="btn btn-primary-wb w-100 py-3 mb-4 fs-6">Send Reset Link</button>
    </form>

    <div class="text-center border-top border-emphasis-subtle pt-4 mt-2">
      <p class="small text-secondary mb-0">Wait, I remember! <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
    </div>
  </div>
</div>
@endsection
