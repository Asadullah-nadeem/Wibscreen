@extends('layouts.app')

@section('title', 'Wibscreen — Your Personal Browser Workspace')
@section('meta_desc', 'Wibscreen lets you open, organize and manage websites inside one unified workspace. Collections, tab management, and more.')

@push('styles')
<style>
  .hero-bg-accent {
    position: absolute; top: -100px; right: -100px; width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(var(--wb-primary-rgb), 0.15) 0%, transparent 70%);
    filter: blur(50px); z-index: -1;
  }
  .browser-mockup {
    background: var(--wb-surface);
    border: 1px solid var(--wb-border-strong);
    border-radius: 16px 16px 0 0;
    box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.4);
    overflow: hidden;
  }
  .mockup-header {
    height: 40px; background: var(--wb-surface-2);
    border-bottom: 1px solid var(--wb-border);
    display: flex; align-items: center; padding: 0 16px; gap: 8px;
  }
  .dot { width: 10px; height: 10px; border-radius: 50%; }
  .feature-icon {
    width: 56px; height: 56px; border-radius: 14px;
    background: rgba(var(--wb-primary-rgb), 0.1);
    color: var(--wb-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-bottom: 24px;
    transition: all 0.3s ease;
  }
  .wb-feature-card:hover .feature-icon {
    background: var(--wb-primary); color: white;
    transform: scale(1.1);
  }
</style>
@endpush

@section('content')
@include('partials.navbar')

<!-- Hero Section -->
<section class="wb-hero">
  <div class="hero-bg-accent"></div>
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-lg-10 col-xl-8">
        <span class="wb-section-label">Next-Gen Workspace</span>
        <h1 class="wb-hero-title">Your browser, <span class="text-gradient">reimagined.</span></h1>
        <p class="wb-hero-sub">Open, organize, and manage all your favorite websites inside one unified, lightning-fast workspace. Say goodbye to tab clutter forever.</p>
        
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mb-5">
          <a href="{{ route('signup') }}" class="btn btn-primary-wb px-5 py-3 fs-5">Get Started Free <i class="bi bi-arrow-right ms-2"></i></a>
          <a href="{{ route('pricing') }}" class="btn btn-outline-wb px-4 py-3 fs-5">View Pricing</a>
        </div>
      </div>
    </div>

    <!-- Visual Mockup -->
    <div class="row justify-content-center mt-5">
      <div class="col-lg-11">
        <div class="browser-mockup">
          <div class="mockup-header">
            <div class="dot bg-danger opacity-50"></div>
            <div class="dot bg-warning opacity-50"></div>
            <div class="dot bg-success opacity-50"></div>
            <div class="ms-3 bg-emphasis-subtle rounded-pill" style="height: 20px; width: 40%; max-width: 300px;"></div>
          </div>
          <div class="p-1">
             <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1600&q=90" class="img-fluid rounded-bottom" alt="Wibscreen Interface">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-5 my-5">
  <div class="container">
    <div class="text-center mb-5 pb-4">
      <span class="wb-section-label">Features</span>
      <h2 class="display-5 fw-bold mb-3">Built for peak productivity</h2>
      <p class="text-secondary mx-auto" style="max-width: 600px;">Everything you need to organize your digital life in one powerful interface.</p>
    </div>
    
    <div class="row g-4">
      @php
        $features = [
          ['bi bi-grid-1x2-fill', 'Smart Collections', 'Organize your links into beautiful, logical folders like Work, Social, and Research.'],
          ['bi bi-window-stack', 'In-App Browsing', 'Open websites directly inside Wibscreen. Maintain focus without jumping between apps.'],
          ['bi bi-lightning-charge-fill', 'Lightning Fast', 'Switch between collections and websites instantly with zero reload time and optimized performance.'],
          ['bi bi-shield-lock-fill', 'Privacy First', 'Your data stays with you. We use local storage to keep your workspace private on your device.'],
          ['bi bi-device-ssd-fill', 'Device Sync', 'Access your workspace on any device. Fully optimized for mobile, tablet, and desktop.'],
          ['bi bi-palette-fill', 'Custom Themes', 'Personalize your experience with gorgeous dark/light modes and custom accent colors.']
        ];
      @endphp

      @foreach($features as $f)
      <div class="col-md-6 col-lg-4">
        <div class="wb-feature-card">
          <div class="feature-icon">
            <i class="{{ $f[0] }}"></i>
          </div>
          <h3 class="h4 fw-bold mb-3">{{ $f[1] }}</h3>
          <p class="text-secondary mb-0">{{ $f[2] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="py-5 mb-5">
  <div class="container">
    <div class="wb-card p-5 text-center position-relative overflow-hidden">
      <div class="hero-bg-accent" style="bottom: -50px; left: -50px; top: auto; right: auto;"></div>
      <h2 class="display-6 fw-bold mb-3">Ready to take control?</h2>
      <p class="text-secondary mb-5 mx-auto" style="max-width: 500px;">Join thousands of users who have organized their digital life with Wibscreen. Start your journey today.</p>
      <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
        <a href="{{ route('signup') }}" class="btn btn-primary-wb px-5 py-3 fs-5">Get Started Free</a>
        <a href="{{ route('about') }}" class="btn btn-outline-wb px-5 py-3 fs-5">Learn More</a>
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
