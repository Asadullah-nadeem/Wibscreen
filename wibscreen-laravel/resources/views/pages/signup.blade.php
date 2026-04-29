@extends('layouts.app')
@section('title', 'Sign Up — Wibscreen')
@push('styles')
<style>
  .wb-auth-wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:40px 16px; background:var(--wb-surface-2); }
  .wb-auth-card { background:var(--wb-surface); border:1px solid var(--wb-border); border-radius:24px; padding:40px 36px; width:100%; max-width:440px; }
  .strength-bar { height:4px; border-radius:2px; background:var(--wb-border); overflow:hidden; }
  .strength-fill { height:100%; border-radius:2px; transition:width .3s,background .3s; width:0; }
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
      <h1 class="h4 fw-bold mb-1">Create your account</h1>
      <p class="text-body-secondary small">
        @if($plan === 'pro')
          You've selected the <span class="badge bg-primary">Pro Plan</span>
        @elseif($plan === 'business')
          You've selected the <span class="badge bg-warning text-dark">Business Plan</span>
        @else
          Start for free — no credit card needed
        @endif
      </p>
    </div>
    @if ($errors->any())
      <div class="alert alert-danger small py-2">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif
    <form id="signup-form" method="POST" action="{{ url('/signup') }}">
      @csrf
      <input type="hidden" name="plan" value="{{ $plan }}">
      <div class="mb-3"><label class="form-label small fw-semibold">Display Name</label><input type="text" name="name" id="su-name" class="form-control" placeholder="Your name" value="{{ old('name') }}" required></div>
      <div class="mb-3"><label class="form-label small fw-semibold">Email</label><input type="email" name="email" id="su-email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required></div>
      <div class="mb-1"><label class="form-label small fw-semibold">Password</label><input type="password" name="password" id="su-pass" class="form-control" placeholder="Min 8 characters" required></div>
      <div class="strength-bar mb-3"><div class="strength-fill" id="strength-fill"></div></div>
      <div class="mb-3 form-check"><input type="checkbox" name="terms" class="form-check-input" id="su-terms" required><label class="form-check-label small" for="su-terms">I agree to the <a href="{{ route('terms') }}" class="text-primary">Terms</a> and <a href="{{ route('privacy') }}" class="text-primary">Privacy Policy</a></label></div>
      <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Create Account</button>
    </form>
    <p class="text-center small text-body-secondary mt-4 mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-semibold">Sign In</a></p>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('su-pass').addEventListener('input',function(){
  const v=this.value,f=document.getElementById('strength-fill');
  let s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^a-zA-Z0-9]/.test(v))s++;
  const pct=[0,25,50,75,100][s];const cols=['','#ef4444','#f59e0b','#6366f1','#22c55e'][s];
  f.style.width=pct+'%';f.style.background=cols;
});
</script>
@endpush
