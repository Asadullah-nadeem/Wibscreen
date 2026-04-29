@extends('layouts.app')
@section('title', 'Login — Wibscreen')
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
      <h1 class="h4 fw-bold mb-1">Welcome back</h1>
      <p class="text-body-secondary small">Sign in to your workspace</p>
    </div>
    <div id="auth-alert" class="alert alert-danger d-none small py-2"></div>
    <form id="login-form" novalidate>
      <div class="mb-3"><label class="form-label small fw-semibold">Email</label><input type="email" id="login-email" class="form-control" placeholder="you@example.com" required></div>
      <div class="mb-3"><label class="form-label small fw-semibold">Password</label>
        <div class="wb-pass-wrap"><input type="password" id="login-pass" class="form-control pe-5" placeholder="••••••••" required>
          <button type="button" class="wb-pass-toggle" id="toggle-pass"><i class="fas fa-eye" id="eye-icon"></i></button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 fw-semibold py-2 mt-1">Sign In</button>
    </form>
    <p class="text-center small text-body-secondary mt-4 mb-0">No account? <a href="{{ route('signup') }}" class="text-primary fw-semibold">Sign Up</a></p>
    <p class="text-center small mt-2 mb-0"><a href="{{ route('home') }}" class="text-body-secondary text-decoration-none">← Back to Home</a></p>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('toggle-pass').addEventListener('click',function(){
  const p=document.getElementById('login-pass'),i=document.getElementById('eye-icon');
  p.type=p.type==='password'?'text':'password';
  i.className=p.type==='password'?'fas fa-eye':'fas fa-eye-slash';
});
document.getElementById('login-form').addEventListener('submit',function(e){
  e.preventDefault();
  const email=document.getElementById('login-email').value.trim(),pass=document.getElementById('login-pass').value,a=document.getElementById('auth-alert');
  a.classList.add('d-none');
  if(!email||!pass){a.textContent='Please fill all fields.';a.classList.remove('d-none');return;}
  localStorage.setItem('wb_logged_in','1');localStorage.setItem('wb_user_email',email);
  window.location.href='/dasboard.html';
});
</script>
@endpush
