@extends('layouts.app')
@section('title', 'Sign Up — Wibscreen')

@section('content')
<div class="auth-split-wrapper">
  <!-- Left: Form Section -->
  <div class="auth-side-form">
    <div class="auth-form-container">
      <div class="text-center mb-5">
        <a href="{{ route('home') }}" class="d-inline-block mb-4">
          <div class="bg-primary rounded-4 d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 56px; height: 56px;">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" style="width: 36px; height: 36px;">
          </div>
        </a>
        <h1 class="h2 fw-bold display-font mb-2">Create Account</h1>
        <p class="text-secondary">
          @if($plan === 'pro')
            <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary border border-primary-subtle">Pro Plan Selected</span>
          @elseif($plan === 'business')
            <span class="badge px-3 py-2 rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Business Plan Selected</span>
          @else
            Start for free — no credit card needed
          @endif
        </p>
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

      <form id="signup-form" method="POST" action="{{ route('signup') }}">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan }}">
        
        <div class="form-floating mb-3">
          <input type="text" name="name" id="su-name" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
          <label for="su-name" class="text-secondary small fw-medium">Full Name</label>
        </div>

        <div class="form-floating mb-3">
          <input type="email" name="email" id="su-email" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none" placeholder="name@example.com" value="{{ old('email') }}" required>
          <label for="su-email" class="text-secondary small fw-medium">Email Address</label>
        </div>

        <div class="mb-1">
          <div class="form-floating">
            <input type="password" name="password" id="su-pass" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none" placeholder="••••••••" required>
            <label for="su-pass" class="text-secondary small fw-medium">Password</label>
          </div>
          <div class="strength-meter"><div class="strength-bar" id="strength-bar"></div></div>
          <p class="text-secondary mt-2 mb-4" style="font-size: 0.8rem;">Use 8+ characters with a mix of letters & numbers.</p>
        </div>

        <div class="mb-4">
          <div class="form-check">
            <input type="checkbox" name="terms" class="form-check-input shadow-none mt-1" id="su-terms" required>
            <label class="form-check-label small text-secondary fw-medium" for="su-terms">
              I agree to the <a href="{{ route('terms') }}" class="text-primary fw-bold text-decoration-none">Terms</a> and <a href="{{ route('privacy') }}" class="text-primary fw-bold text-decoration-none">Privacy Policy</a>
            </label>
          </div>
        </div>

        <button type="submit" class="btn btn-primary-wb w-100 py-3 mb-4 fs-6">Sign Up</button>
      </form>

      <div class="text-center border-top border-emphasis-subtle pt-4 mt-2">
        <p class="small text-secondary mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In</a></p>
      </div>
    </div>
  </div>

  <!-- Right: Image Section -->
  <div class="auth-side-image" style="background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&w=1200&q=80');">
    <div class="auth-image-content">
      <h2 class="auth-image-title">Build your perfect <span class="text-white opacity-75">workspace.</span></h2>
      <p class="fs-5 opacity-75">Unlimited collections, instant access, and total privacy. Start your journey today.</p>
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
