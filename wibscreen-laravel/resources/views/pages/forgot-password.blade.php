@extends('layouts.app')
@section('title', 'Forgot Password — Wibscreen')
@push('styles')
<style>
  .wb-auth-wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:40px 16px; background:var(--wb-surface-2); }
  .wb-auth-card { background:var(--wb-surface); border:1px solid var(--wb-border); border-radius:24px; padding:40px 36px; width:100%; max-width:420px; }
</style>
@endpush
@section('content')
<div class="wb-auth-wrap">
  <div class="wb-auth-card">
    <div class="text-center mb-4">
      <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen Logo" style="width:36px;height:36px;border-radius:10px;">
        <span class="fw-bold" style="font-family:'Manrope',sans-serif;font-size:1.1rem;color:inherit;">Wibscreen</span>
      </a>
      <h1 class="h4 fw-bold mb-1">froget-password</h1>
      <p class="text-body-secondary small">No worries, we'll send you reset instructions.</p>
    </div>

    @if (session('status'))
      <div class="alert alert-success small py-2 mb-4">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger small py-2 mb-4">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="mb-4">
        <label class="form-label small fw-semibold">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
      </div>
      <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Send Reset Link</button>
    </form>

    <p class="text-center small text-body-secondary mt-4 mb-0">
      Wait, I remember! <a href="{{ route('login') }}" class="text-primary fw-semibold">Sign In</a>
    </p>
    <p class="text-center small mt-2 mb-0"><a href="{{ route('home') }}" class="text-body-secondary text-decoration-none">← Back to Home</a></p>
  </div>
</div>
@endsection
