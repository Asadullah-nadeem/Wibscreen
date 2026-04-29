@extends('layouts.app')
@section('title', 'About Us — Wibscreen')
@section('meta_desc', 'Learn about Wibscreen — the personal browser workspace built for focused, organized browsing.')

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container">
    <span class="wb-section-label">About Us</span>
    <h1 class="wb-hero-title mb-3">Built for focused browsing</h1>
    <p class="wb-hero-sub text-body-secondary">Wibscreen was created to solve a simple problem: too many tabs, too little organization.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width:800px;">
    <div class="wb-page-card mb-4">
      <h2 class="h4 fw-bold mb-3">Our Mission</h2>
      <p class="text-body-secondary">Wibscreen exists to help people manage their digital workspace with clarity. We believe your browser should work <em>for</em> you — not against you. With smart collections, in-app browsing, and a clean UI, we've built a tool that puts you back in control.</p>
    </div>
    <div class="wb-page-card mb-4">
      <h2 class="h4 fw-bold mb-3">What Is Wibscreen?</h2>
      <p class="text-body-secondary mb-0">Wibscreen is a personal browser workspace. You can save any website into organized collections (like Work, Social, Research), open them directly inside the app, and switch between them without losing context. Think of it as a bookmark manager that actually shows you the site.</p>
    </div>
    <div class="row g-4 mb-4">
      @php $audiences = [['fas fa-briefcase','#6366f1','Professionals','Keep client sites, dashboards, and tools organized by project.'],['fas fa-graduation-cap','#22c55e','Students','Group research links, course portals, and resources by subject.'],['fas fa-code','#f59e0b','Developers','Store docs, APIs, and dev tools in instant-access collections.'],['fas fa-pencil','#ec4899','Creators','Organize inspiration, platforms, and tools in one place.']]; @endphp
      @foreach($audiences as $a)
      <div class="col-sm-6">
        <div class="wb-page-card h-100">
          <i class="{{ $a[0] }} mb-2" style="color:{{ $a[1] }};font-size:1.4rem;"></i>
          <h5 class="fw-bold mt-2 mb-1">{{ $a[2] }}</h5>
          <p class="text-body-secondary small mb-0">{{ $a[3] }}</p>
        </div>
      </div>
      @endforeach
    </div>
    <div class="wb-page-card">
      <h2 class="h4 fw-bold mb-3">Our Values</h2>
      @php $values = [['Privacy First','We don\'t track you. Your workspace data lives in your browser.'],['Simplicity','No bloat. Every feature earns its place.'],['Performance','Fast, responsive, and works on every device.'],['Transparency','Clear pricing. Clear policies. No surprises.']]; @endphp
      @foreach($values as $v)
      <div class="d-flex gap-3 mb-3">
        <i class="fas fa-check-circle text-primary mt-1 flex-shrink-0"></i>
        <div><strong>{{ $v[0] }}</strong> — <span class="text-body-secondary">{{ $v[1] }}</span></div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section class="py-4 text-center border-top" style="border-color:var(--wb-border)!important;">
  <div class="container">
    <h3 class="fw-bold mb-2" style="font-family:'Manrope',sans-serif;">Ready to try it?</h3>
    <p class="text-body-secondary mb-3">Start for free. No credit card needed.</p>
    <a href="{{ route('signup') }}" class="btn btn-primary px-5 fw-semibold me-2">Get Started</a>
    <a href="{{ route('pricing') }}" class="btn btn-outline-secondary px-4">View Pricing</a>
  </div>
</section>

@include('partials.footer')
@endsection
