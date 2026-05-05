@extends('layouts.app')
@section('title', 'Sinup — Wibscreen')

@push('styles')
<style>
  .auth-logo { width: 42px; height: 42px; border-radius: 12px; margin-bottom: 20px; }
  .form-floating > .form-control:focus ~ label, .form-floating > .form-control:not(:placeholder-shown) ~ label { opacity: .65; transform: scale(.85) translateY(-.5rem) translateX(.15rem); }
  .strength-meter { height: 6px; border-radius: 3px; background: var(--wb-border); overflow: hidden; margin-top: 8px; }
  .strength-bar { height: 100%; width: 0; transition: all 0.3s ease; }
</style>
@endpush

@section('content')
<div class="wb-auth-wrap">
  <div class="wb-auth-card">
    <div class="text-center">
      <a href="{{ route('home') }}" class="d-inline-block text-decoration-none">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" class="auth-logo shadow-sm">
      </a>
      <h1 class="wb-auth-title">Sinup</h1>
      <p class="wb-auth-sub">
        @if($plan === 'pro')
          <span class="badge px-3 py-2 rounded-pill" style="background:rgba(99,102,241,0.1); color:var(--wb-primary); border:1px solid rgba(99,102,241,0.2);">Pro Plan Selected</span>
        @elseif($plan === 'business')
          <span class="badge px-3 py-2 rounded-pill text-dark" style="background:#f59e0b; border:1px solid #d97706;">Business Plan Selected</span>
        @else
          Start for free — no credit card needed
        @endif
      </p>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger border-0 small py-2 mb-4" style="background:rgba(239,68,68,0.1); color:#ef4444;">
        @foreach ($errors->all() as $error)
          <div><i class="fas fa-circle-exclamation me-1"></i> {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form id="signup-form" method="POST" action="{{ route('signup') }}">
      @csrf
      <input type="hidden" name="plan" value="{{ $plan }}">
      
      <div class="form-floating mb-3">
        <input type="text" name="name" id="su-name" class="form-control bg-transparent border-secondary-subtle rounded-3" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
        <label for="su-name" class="small">Display Name</label>
      </div>

      <div class="form-floating mb-3">
        <input type="email" name="email" id="su-email" class="form-control bg-transparent border-secondary-subtle rounded-3" placeholder="name@example.com" value="{{ old('email') }}" required>
        <label for="su-email" class="small">Email Address</label>
      </div>

      <div class="mb-1">
        <div class="form-floating">
          <input type="password" name="password" id="su-pass" class="form-control bg-transparent border-secondary-subtle rounded-3" placeholder="••••••••" required>
          <label for="su-pass" class="small">Password</label>
        </div>
        <div class="strength-meter"><div class="strength-bar" id="strength-bar"></div></div>
        <p class="x-small text-body-secondary mt-1 mb-3">Use 8+ characters with a mix of letters & numbers.</p>
      </div>

      <div class="mb-4">
        <div class="form-check">
          <input type="checkbox" name="terms" class="form-check-input mt-1" id="su-terms" required>
          <label class="form-check-label small text-body-secondary" for="su-terms">
            I agree to the <a href="{{ route('terms') }}" class="text-primary fw-semibold text-decoration-none">Terms</a> and <a href="{{ route('privacy') }}" class="text-primary fw-semibold text-decoration-none">Privacy Policy</a>
          </label>
        </div>
      </div>

      <button type="submit" class="wb-btn-primary mb-4">Sinup</button>
    </form>

    <div class="text-center border-top pt-4" style="border-color: var(--wb-border) !important;">
      <p class="small text-body-secondary mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('su-pass').addEventListener('input', function() {
  const v = this.value, b = document.getElementById('strength-bar');
  let s = 0;
  if (v.length >= 8) s++;
  if (/[A-Z]/.test(v)) s++;
  if (/[0-9]/.test(v)) s++;
  if (/[^a-zA-Z0-9]/.test(v)) s++;
  
  const pct = [0, 25, 50, 75, 100][s];
  const cols = ['', '#ef4444', '#f59e0b', '#6366f1', '#22c55e'][s];
  b.style.width = pct + '%';
  b.style.background = cols;
});
</script>
@endpush
