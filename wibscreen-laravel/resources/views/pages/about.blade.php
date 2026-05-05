@extends('layouts.app')
@section('title', 'About Us — Wibscreen')

@section('content')
<!-- Hero Section -->
<section class="wb-hero pb-5">
  <div class="container text-center">
    <span class="wb-section-label">Our Story</span>
    <h1 class="wb-hero-title">Focused browsing, <span class="text-gradient">uncomplicated.</span></h1>
    <p class="wb-hero-sub">Wibscreen was born from a simple frustration: the modern browser isn't built for organization. We built a tool that is.</p>
  </div>
</section>

<!-- Mission Section -->
<section class="py-5 bg-body-tertiary border-top border-bottom border-emphasis-subtle">
  <div class="container py-5">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="wb-card p-0 overflow-hidden border-0 shadow-lg">
          <img src="https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?auto=format&fit=crop&w=1000&q=80" class="img-fluid" alt="Workspace">
        </div>
      </div>
      <div class="col-lg-6">
        <h2 class="display-6 fw-bold mb-4">Our Mission</h2>
        <p class="text-secondary fs-5 mb-4">Wibscreen exists to help people manage their digital workspace with clarity. We believe your browser should work <em>for</em> you — not against you.</p>
        <p class="text-secondary mb-5">With smart collections, in-app browsing, and a clean UI, we've built a tool that puts you back in control of your online life. Whether you're a developer, researcher, or creator, Wibscreen helps you stay in the flow.</p>
        
        <div class="row g-4">
           <div class="col-6">
             <div class="h2 fw-bold text-primary mb-1">100%</div>
             <div class="small text-secondary fw-bold text-uppercase opacity-75" style="letter-spacing:1px;">Client-side</div>
           </div>
           <div class="col-6">
             <div class="h2 fw-bold text-primary mb-1">Privacy</div>
             <div class="small text-secondary fw-bold text-uppercase opacity-75" style="letter-spacing:1px;">Guaranteed</div>
           </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Audience Section -->
<section class="py-5 mt-5">
  <div class="container py-5 text-center">
    <span class="wb-section-label">Who we serve</span>
    <h2 class="display-5 fw-bold mb-5">Built for everyone</h2>

    <div class="row g-4 text-start">
      @php 
        $audiences = [
          ['bi bi-briefcase-fill','#6366f1','Professionals','Keep client sites, dashboards, and tools organized by project.'],
          ['bi bi-mortarboard-fill','#22c55e','Students','Group research links, course portals, and resources by subject.'],
          ['bi bi-code-slash','#f59e0b','Developers','Store docs, APIs, and dev tools in instant-access collections.'],
          ['bi bi-pencil-square','#ec4899','Creators','Organize inspiration, platforms, and tools in one place.']
        ]; 
      @endphp
      @foreach($audiences as $a)
      <div class="col-sm-6 col-lg-3">
        <div class="wb-feature-card h-100 p-4">
          <div class="mb-4 d-flex align-items-center justify-content-center rounded-3" style="width:52px; height:52px; background:{{ $a[1] }}15; color:{{ $a[1] }}; font-size:1.5rem;">
            <i class="{{ $a[0] }}"></i>
          </div>
          <h4 class="fw-bold mb-3 h5">{{ $a[2] }}</h4>
          <p class="text-secondary small mb-0">{{ $a[3] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endsection
