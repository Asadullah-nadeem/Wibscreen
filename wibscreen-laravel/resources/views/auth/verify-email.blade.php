@extends('layouts.app')
@section('title', 'Verify Your Email — Wibscreen')

@push('styles')
<style>
  .verify-wrap { min-height: 80vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at top right, rgba(99,102,241,0.05), transparent), radial-gradient(circle at bottom left, rgba(99,102,241,0.05), transparent); }
  .verify-card { background: var(--wb-surface); border: 1px solid var(--wb-border); border-radius: 32px; padding: 60px 40px; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); text-align: center; position: relative; overflow: hidden; }
  .verify-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #6366f1, #a855f7); }
  .verify-icon-wrap { width: 80px; height: 80px; background: rgba(99,102,241,0.1); color: #6366f1; border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 30px; transform: rotate(-5deg); transition: transform 0.3s ease; }
  .verify-card:hover .verify-icon-wrap { transform: rotate(0deg); }
</style>
@endpush

@section('content')
@include('partials.navbar')

<div class="verify-wrap">
  <div class="verify-card">
    <div class="verify-icon-wrap">
      <i class="fas fa-paper-plane"></i>
    </div>
    
    <h1 class="h3 fw-bold mb-3" style="font-family: 'Manrope', sans-serif;">Verify your email</h1>
    <p class="text-body-secondary mb-5 px-md-3">
      We've sent a magic link to your inbox. Please click the link to confirm your account and unlock your workspace.
    </p>

    @if (session('message'))
      <div class="alert alert-success small py-2 mb-4 border-0" style="background: rgba(34,197,94,0.1); color: #22c55e;">
        <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
      </div>
    @endif

    <div class="d-grid gap-3">
      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 rounded-3 shadow-sm">
          Resend Verification Link
        </button>
      </form>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary w-100 fw-semibold py-2 rounded-3 border-0">
          Sign out and try later
        </button>
      </form>
    </div>

    <p class="mt-5 small text-body-secondary">
      Didn't receive anything? Check your spam folder or contact <a href="mailto:support.codeaxe@gmail.com" class="text-primary text-decoration-none">Support</a>.
    </p>
  </div>
</div>

@include('partials.footer')
@endsection
