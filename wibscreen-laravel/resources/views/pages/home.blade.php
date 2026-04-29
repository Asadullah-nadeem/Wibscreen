@extends('layouts.app')

@section('title', 'Wibscreen — Your Personal Browser Workspace')
@section('meta_desc', 'Wibscreen lets you open, organize and manage websites inside one unified workspace. Collections, tab management, and more — all in your browser.')

@push('styles')
<style>
  .hero-gradient { background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(99,102,241,0.22) 0%, transparent 70%); }
  .feature-card { background: var(--wb-surface); border: 1px solid var(--wb-border); border-radius: 20px; padding: 28px 24px; height: 100%; transition: all .3s ease; }
  .feature-card:hover { border-color: rgba(99,102,241,.45); transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.15); }
  .step-num { width: 44px; height: 44px; background: var(--wb-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.1rem; flex-shrink: 0; font-family: 'Manrope', sans-serif; }
  .stat-card { text-align: center; padding: 28px 20px; border-radius: 20px; background: var(--wb-surface); border: 1px solid var(--wb-border); }
  .stat-num { font-family: 'Manrope', sans-serif; font-size: 2.2rem; font-weight: 900; color: var(--wb-primary); }
  .browser-mock { background: var(--wb-surface); border: 1px solid var(--wb-border); border-radius: 20px; overflow: hidden; box-shadow: 0 32px 80px rgba(0,0,0,.35); }
  .browser-bar { padding: 12px 16px; background: var(--wb-surface-2); border-bottom: 1px solid var(--wb-border); display: flex; align-items: center; gap: 10px; }
  .browser-dot { width: 10px; height: 10px; border-radius: 50%; }
  .browser-url { flex: 1; background: var(--wb-surface); border: 1px solid var(--wb-border); border-radius: 20px; padding: 5px 14px; font-size: .78rem; opacity: .6; }
  .browser-body { padding: 20px; min-height: 200px; display: flex; align-items: center; justify-content: center; gap: 16px; flex-wrap: wrap; }
  .mini-card { background: var(--wb-surface-2); border: 1px solid var(--wb-border); border-radius: 12px; padding: 12px 16px; font-size: .8rem; font-weight: 600; display: flex; align-items: center; gap: 8px; min-width: 130px; }
</style>
@endpush

@section('content')

@include('partials.navbar')

{{-- Hero --}}
<section class="wb-hero hero-gradient">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="wb-section-label">Browser Workspace</span>
        <h1 class="wb-hero-title">One workspace for <span style="color:var(--wb-primary);">all your websites</span></h1>
        <p class="wb-hero-sub text-body-secondary">
          Wibscreen lets you organize websites into smart collections, open them instantly inside the app, and switch between them like tabs — without the clutter.
        </p>
        <div class="d-flex flex-wrap gap-3 mt-4">
          <a href="{{ route('signup') }}" class="btn btn-primary btn-lg fw-semibold px-5 py-3 rounded-3">
            Get Started Free <i class="fas fa-arrow-right ms-2"></i>
          </a>
          <a href="{{ route('pricing') }}" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-3">
            View Plans
          </a>
        </div>
        <div class="d-flex gap-4 mt-4">
          <div class="d-flex align-items-center gap-2"><i class="fas fa-check text-success small"></i><span class="small text-body-secondary">Free to start</span></div>
          <div class="d-flex align-items-center gap-2"><i class="fas fa-check text-success small"></i><span class="small text-body-secondary">No credit card</span></div>
          <div class="d-flex align-items-center gap-2"><i class="fas fa-check text-success small"></i><span class="small text-body-secondary">Privacy first</span></div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="browser-mock">
          <div class="browser-bar">
            <div class="browser-dot" style="background:#ef4444;"></div>
            <div class="browser-dot" style="background:#f59e0b;"></div>
            <div class="browser-dot" style="background:#22c55e;"></div>
            <div class="browser-url">wibscreen.app — Workspace</div>
          </div>
          <div class="browser-body">
            <div class="mini-card"><i class="fas fa-briefcase text-primary"></i> Work</div>
            <div class="mini-card"><i class="fas fa-hashtag" style="color:#22c55e;"></i> Social</div>
            <div class="mini-card"><i class="fas fa-flask" style="color:#f59e0b;"></i> Research</div>
            <div class="mini-card"><i class="fas fa-user-tie" style="color:#ec4899;"></i> Pro</div>
            <div class="mini-card"><i class="fas fa-music" style="color:#8b5cf6;"></i> Media</div>
            <div class="mini-card"><i class="fas fa-plus text-primary"></i> New Folder</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Stats --}}
<section class="py-5 border-top" style="border-color:var(--wb-border)!important;">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-num">10+</div><div class="small text-body-secondary mt-1">Collections</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-num">∞</div><div class="small text-body-secondary mt-1">Pro Tabs</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-num">720h</div><div class="small text-body-secondary mt-1">Pro Monthly</div></div></div>
      <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-num">0</div><div class="small text-body-secondary mt-1">Tracking</div></div></div>
    </div>
  </div>
</section>

{{-- Features --}}
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <span class="wb-section-label">Features</span>
      <h2 class="h2 fw-bold" style="font-family:'Manrope',sans-serif;">Everything you need in one workspace</h2>
    </div>
    <div class="row g-4">
      @php
        $features = [
          ['fas fa-folder-open','#6366f1','Smart Collections','Group websites by topic — Work, Social, Research. Switch between them instantly.'],
          ['fas fa-display','#22c55e','In-App Browser','Open any website directly inside Wibscreen without leaving your workspace.'],
          ['fas fa-shield-halved','#f59e0b','Privacy First','No tracking. No ads on Pro. Your data stays in your browser.'],
          ['fas fa-moon','#8b5cf6','Dark & Light Mode','Elegant dark mode by default. Toggle to light when you need it.'],
          ['fas fa-mobile-alt','#ec4899','Fully Responsive','Works perfectly on mobile, tablet, and desktop.'],
          ['fas fa-bolt','#14b8a6','Lightning Fast','Instant navigation. No reloading. Open sites in milliseconds.'],
        ];
      @endphp
      @foreach($features as $f)
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div style="width:48px;height:48px;border-radius:14px;background:{{ $f[1] }}1a;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
            <i class="{{ $f[0] }}" style="color:{{ $f[1] }};font-size:1.2rem;"></i>
          </div>
          <h5 class="fw-bold mb-2">{{ $f[2] }}</h5>
          <p class="text-body-secondary small mb-0">{{ $f[3] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- How it works --}}
<section class="py-5 border-top" style="border-color:var(--wb-border)!important;background:var(--wb-surface-2);">
  <div class="container" style="max-width:720px;">
    <div class="text-center mb-5">
      <span class="wb-section-label">How It Works</span>
      <h2 class="h2 fw-bold" style="font-family:'Manrope',sans-serif;">Up and running in 3 steps</h2>
    </div>
    @php $steps = [['Create your account','Sign up for free. No credit card required.'],['Add collections','Create folders like Work, Social, Research.'],['Add websites','Save any URL into a collection and open it instantly.']]; @endphp
    <div class="d-flex flex-column gap-4">
      @foreach($steps as $i => $step)
      <div class="d-flex gap-4 align-items-start">
        <div class="step-num flex-shrink-0">{{ $i + 1 }}</div>
        <div>
          <h5 class="fw-bold mb-1">{{ $step[0] }}</h5>
          <p class="text-body-secondary mb-0">{{ $step[1] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="py-5 text-center">
  <div class="container">
    <h2 class="h2 fw-bold mb-2" style="font-family:'Manrope',sans-serif;">Start organizing your web today</h2>
    <p class="text-body-secondary mb-4">Free plan available. No credit card required.</p>
    <a href="{{ route('signup') }}" class="btn btn-primary btn-lg px-5 fw-semibold me-2">Get Started Free</a>
    <a href="{{ route('pricing') }}" class="btn btn-outline-secondary btn-lg px-4">See Pricing</a>
  </div>
</section>

@include('partials.footer')

@endsection
