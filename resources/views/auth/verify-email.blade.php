@extends('layouts.app')
@section('title', 'Verify Your Email — Wibscreen')

@push('styles')
<style>
  .verify-wrap { 
    min-height: 100vh; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    padding: 40px 20px;
    background: var(--wb-surface-2); 
  }
  .verify-card { 
    background: var(--wb-surface); 
    border: 1px solid var(--wb-border); 
    border-radius: var(--wb-radius-lg); 
    padding: 60px 40px; 
    width: 100%; 
    max-width: 500px; 
    box-shadow: var(--wb-shadow); 
    text-align: center; 
    position: relative; 
    overflow: hidden; 
  }
  .verify-icon-box { 
    width: 80px; height: 80px; 
    background: rgba(var(--wb-primary-rgb), 0.1); 
    color: var(--wb-primary); 
    border-radius: 24px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 2rem; 
    margin: 0 auto 30px; 
    transform: rotate(-5deg); 
    transition: all 0.3s ease; 
  }
  .verify-card:hover .verify-icon-box { transform: rotate(0deg) scale(1.05); }
  
  .pop-alert {
    background: linear-gradient(135deg, var(--wb-primary), #a855f7);
    color: white;
    padding: 16px;
    border-radius: var(--wb-radius-md);
    margin-bottom: 30px;
    font-weight: 600;
    box-shadow: 0 10px 20px -5px rgba(var(--wb-primary-rgb), 0.3);
    animation: slideDown 0.5s ease-out;
  }

  @keyframes slideDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
</style>
@endpush

@section('content')
<div class="verify-wrap">
  <div class="verify-card">
    @if (session('verified_pop'))
      <div class="pop-alert">
        <i class="bi bi-stars me-2"></i> Account created successfully!
      </div>
    @endif

    <div class="verify-icon-box">
      <i class="bi bi-envelope-check-fill"></i>
    </div>
    
    <h1 class="h3 fw-bold mb-3 display-font">Verify your email</h1>
    <p class="text-secondary mb-5">
      We've sent a verification link to your email address. Please check your inbox and click the link to activate your workspace.
    </p>

    @if (session('message'))
      <div class="alert alert-success border-0 py-3 mb-4 rounded-3 d-flex align-items-center justify-content-center" style="background:rgba(34,197,94,0.1); color:#22c55e;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i> <div>{{ session('message') }}</div>
      </div>
    @endif

    <div class="d-grid gap-3">
      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary-wb w-100 py-3 fs-6">
          <i class="bi bi-send-fill me-2"></i> Resend Verification Email
        </button>
      </form>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-link text-secondary text-decoration-none small fw-bold">
          Sign out and try later
        </button>
      </form>
    </div>

    <div class="mt-5 p-4 rounded-4 bg-body-tertiary border border-emphasis-subtle text-start">
      <h6 class="fw-bold mb-2 small text-uppercase opacity-50"><i class="bi bi-info-circle me-1"></i> Didn't get the email?</h6>
      <ul class="list-unstyled small text-secondary mb-0">
        <li class="mb-2"><i class="bi bi-dot"></i> Check your <strong>Spam or Junk</strong> folder.</li>
        <li class="mb-2"><i class="bi bi-dot"></i> Ensure your email address is correct.</li>
        <li><i class="bi bi-dot"></i> Click the button above to <strong>send again</strong>.</li>
      </ul>
    </div>

    <p class="mt-4 small text-secondary">
      Still having trouble? <a href="{{ route('support') }}" class="text-primary fw-bold text-decoration-none">Contact Support</a>
    </p>
  </div>
</div>
@endsection
