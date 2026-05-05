@extends('layouts.app')
@section('title', 'Reset Password — Wibscreen')

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
      <h1 class="wb-auth-title">Reset Password</h1>
      <p class="wb-auth-sub">Choose a secure new password for your account.</p>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger border-0 small py-2 mb-4" style="background:rgba(239,68,68,0.1); color:#ef4444;">
        @foreach ($errors->all() as $error)
          <div><i class="fas fa-circle-exclamation me-1"></i> {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      
      <div class="form-floating mb-3">
        <input type="email" name="email" id="reset-email" class="form-control bg-secondary-subtle border-secondary-subtle rounded-3" value="{{ $email ?? old('email') }}" readonly required>
        <label for="reset-email" class="small">Email Address</label>
      </div>

      <div class="mb-3 position-relative">
        <div class="form-floating">
          <input type="password" name="password" id="new-pass" class="form-control bg-transparent border-secondary-subtle rounded-3 pe-5" placeholder="••••••••" required autofocus>
          <label for="new-pass" class="small">New Password</label>
        </div>
        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 pe-3 opacity-50" onclick="togglePass('new-pass', this)">
          <i class="fas fa-eye"></i>
        </button>
      </div>

      <div class="mb-4 position-relative">
        <div class="form-floating">
          <input type="password" name="password_confirmation" id="confirm-pass" class="form-control bg-transparent border-secondary-subtle rounded-3 pe-5" placeholder="••••••••" required>
          <label for="confirm-pass" class="small">Confirm Password</label>
        </div>
        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 pe-3 opacity-50" onclick="togglePass('confirm-pass', this)">
          <i class="fas fa-eye"></i>
        </button>
      </div>

      <button type="submit" class="wb-btn-primary mb-4">Reset Password</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function togglePass(id, btn) {
  const p = document.getElementById(id), i = btn.querySelector('i');
  p.type = p.type === 'password' ? 'text' : 'password';
  i.className = p.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
@endpush
