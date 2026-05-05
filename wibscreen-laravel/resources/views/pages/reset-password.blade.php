@extends('layouts.app')
@section('title', 'Reset Password — Wibscreen')
@push('styles')
<style>
  .wb-auth-wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:40px 16px; background:var(--wb-surface-2); }
  .wb-auth-card { background:var(--wb-surface); border:1px solid var(--wb-border); border-radius:24px; padding:40px 36px; width:100%; max-width:420px; }
  .wb-pass-wrap { position:relative; }
  .wb-pass-toggle { position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; opacity:.5; border:none; background:transparent; color:inherit; }
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
      <h1 class="h4 fw-bold mb-1">Reset Password</h1>
      <p class="text-body-secondary small">Choose a secure new password</p>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger small py-2 mb-4">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      
      <div class="mb-3">
        <label class="form-label small fw-semibold">Email Address</label>
        <input type="email" name="email" class="form-control bg-secondary-subtle" value="{{ $email ?? old('email') }}" readonly required>
      </div>

      <div class="mb-3">
        <label class="form-label small fw-semibold">New Password</label>
        <div class="wb-pass-wrap">
          <input type="password" name="password" id="reset-pass" class="form-control pe-5" placeholder="••••••••" required autofocus>
          <button type="button" class="wb-pass-toggle" onclick="togglePass('reset-pass', this)"><i class="fas fa-eye"></i></button>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label small fw-semibold">Confirm Password</label>
        <div class="wb-pass-wrap">
          <input type="password" name="password_confirmation" id="reset-pass-confirm" class="form-control pe-5" placeholder="••••••••" required>
          <button type="button" class="wb-pass-toggle" onclick="togglePass('reset-pass-confirm', this)"><i class="fas fa-eye"></i></button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Reset Password</button>
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
