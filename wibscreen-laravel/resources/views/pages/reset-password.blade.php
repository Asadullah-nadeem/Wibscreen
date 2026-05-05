@extends('layouts.app')
@section('title', 'Reset Password — Wibscreen')

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
      <h1 class="h2 fw-bold display-font mb-2">Reset Password</h1>
      <p class="text-secondary">Choose a secure new password for your account.</p>
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

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      
      <div class="form-floating mb-3">
        <input type="email" name="email" id="reset-email" class="form-control bg-body-tertiary border-emphasis-subtle rounded-3 shadow-none opacity-75" value="{{ $email ?? old('email') }}" readonly required>
        <label for="reset-email" class="text-secondary small fw-medium">Email Address</label>
      </div>

      <div class="mb-3 position-relative">
        <div class="form-floating">
          <input type="password" name="password" id="new-pass" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none pe-5" placeholder="••••••••" required autofocus>
          <label for="new-pass" class="text-secondary small fw-medium">New Password</label>
        </div>
        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 pe-3 text-secondary" onclick="togglePass('new-pass', this)" style="z-index: 10;">
          <i class="bi bi-eye-fill"></i>
        </button>
      </div>

      <div class="mb-4 position-relative">
        <div class="form-floating">
          <input type="password" name="password_confirmation" id="confirm-pass" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none pe-5" placeholder="••••••••" required>
          <label for="confirm-pass" class="text-secondary small fw-medium">Confirm Password</label>
        </div>
        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y border-0 pe-3 text-secondary" onclick="togglePass('confirm-pass', this)" style="z-index: 10;">
          <i class="bi bi-eye-fill"></i>
        </button>
      </div>

      <button type="submit" class="btn btn-primary-wb w-100 py-3 mb-4 fs-6">Reset Password</button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function togglePass(id, btn) {
  const p = document.getElementById(id), i = btn.querySelector('i');
  p.type = p.type === 'password' ? 'text' : 'password';
  i.className = p.type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
}
</script>
@endpush
