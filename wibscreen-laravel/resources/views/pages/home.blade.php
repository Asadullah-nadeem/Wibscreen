@extends('layouts.app')

@section('title', 'Wibscreen — Your Personal Browser Workspace')
@section('meta_desc', 'Wibscreen lets you open, organize and manage websites inside one unified workspace. Collections, tab management, and more.')

@push('styles')
<style>
  .hero-gradient {
    background: radial-gradient(circle at top right, rgba(99,102,241,0.15), transparent),
                radial-gradient(circle at bottom left, rgba(99,102,241,0.05), transparent);
  }
  .browser-mockup {
    border: 1px solid var(--wb-border);
    border-radius: 12px 12px 0 0;
    overflow: hidden;
    box-shadow: 0 40px 100px -20px rgba(0,0,0,0.3);
  }
  .mockup-header {
    height: 38px; background: var(--wb-surface-2);
    border-bottom: 1px solid var(--wb-border);
    display: flex; align-items: center; padding: 0 16px; gap: 8px;
  }
  .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
  .mockup-bar { height: 20px; background: var(--wb-surface); border-radius: 10px; flex-grow: 1; max-width: 400px; margin: 0 auto; border: 1px solid var(--wb-border); }
  
  .feature-icon-box {
    width: 60px; height: 60px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-bottom: 24px;
    background: linear-gradient(135deg, var(--wb-primary), #818cf8);
    color: white; box-shadow: 0 8px 16px -4px rgba(99,102,241,0.4);
  }
</style>
@endpush

@section('content')
@include('partials.navbar')

<!-- Hero -->
<section class="wb-hero hero-gradient">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-8">
        <span class="wb-section-label">All-in-one workspace</span>
        <h1 class="wb-hero-title mb-4">Your browser, <span class="text-primary">reimagined.</span></h1>
        <p class="wb-hero-sub text-body-secondary mb-5">Open, organize, and manage all your favorite websites inside one unified, lightning-fast workspace. No more tab clutter.</p>
        
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mb-5">
          <a href="{{ route('signup') }}" class="btn btn-primary btn-lg px-5 py-3 fw-bold rounded-3 shadow-lg">Get Started Free</a>
          <a href="{{ route('pricing') }}" class="btn btn-outline-secondary btn-lg px-4 py-3 fw-semibold rounded-3">View Pricing</a>
        </div>
      </div>
    </div>

    <!-- Visual Mockup -->
    <div class="row justify-content-center mt-4">
      <div class="col-lg-10">
        <div class="browser-mockup">
          <div class="mockup-header">
            <div class="mockup-dot bg-danger opacity-50"></div>
            <div class="mockup-dot bg-warning opacity-50"></div>
            <div class="mockup-dot bg-success opacity-50"></div>
            <div class="mockup-bar"></div>
          </div>
          <div class="p-2" style="background: var(--wb-surface);">
             <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80" class="img-fluid rounded-1" alt="Workspace UI Preview">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features -->
<section class="py-5">
  <div class="container py-5">
    <div class="text-center mb-5 pb-4">
      <span class="wb-section-label">Features</span>
      <h2 class="h1 fw-bold" style="font-family:'Manrope',sans-serif;">Built for productivity</h2>
    </div>
    
    <div class="row g-4">
      @php
        $feats = [
          ['fas fa-folder-tree', 'Smart Collections', 'Organize your links into folders like Work, Social, and Research for instant access.'],
          ['fas fa-window-restore', 'In-App Browsing', 'Open websites directly inside Wibscreen. No more jumping between dozens of tabs.'],
          ['fas fa-bolt', 'Lightning Fast', 'Switch between collections and websites instantly with zero reload time.'],
          ['fas fa-shield-halved', 'Privacy First', 'Your data is yours. We use local storage to keep your workspace private on your device.'],
          ['fas fa-mobile-screen', 'Responsive Design', 'Access your workspace on any device. Fully optimized for mobile and desktop.'],
          ['fas fa-palette', 'Custom Themes', 'Personalize your experience with Day and Night modes and custom accent colors.']
        ];
      @endphp

      @foreach($feats as $f)
      <div class="col-md-6 col-lg-4">
        <div class="wb-feature-card">
          <div class="feature-icon-box">
            <i class="{{ $f[0] }}"></i>
          </div>
          <h3 class="h5 fw-bold mb-3">{{ $f[1] }}</h3>
          <p class="text-body-secondary small mb-0">{{ $f[2] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-5 mb-5">
  <div class="container">
    <div class="p-5 rounded-4 text-center hero-gradient border" style="border-color: var(--wb-border) !important;">
      <h2 class="h1 fw-bold mb-3" style="font-family:'Manrope',sans-serif;">Ready to take control?</h2>
      <p class="text-body-secondary mb-5 mx-auto" style="max-width: 500px;">Join thousands of users who have organized their digital life with Wibscreen.</p>
      <a href="{{ route('signup') }}" class="btn btn-primary btn-lg px-5 fw-bold rounded-3">Get Started Free</a>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
