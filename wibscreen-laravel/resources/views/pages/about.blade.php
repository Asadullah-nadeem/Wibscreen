@extends('layouts.app')
@section('title', 'About Us — Wibscreen')

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container">
    <span class="wb-section-label">Our Story</span>
    <h1 class="wb-hero-title mb-4">Focused browsing, <span class="text-primary">uncomplicated.</span></h1>
    <p class="wb-hero-sub text-body-secondary">Wibscreen was born from a simple frustration: the modern browser isn't built for organization. We built a tool that is.</p>
  </div>
</section>

<section class="py-5 bg-body-tertiary border-top border-bottom" style="border-color: var(--wb-border) !important;">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="wb-page-card p-0 overflow-hidden shadow-lg border-0">
          <img src="https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?auto=format&fit=crop&w=800&q=80" class="img-fluid" alt="Workspace">
        </div>
      </div>
      <div class="col-lg-6">
        <h2 class="h2 fw-bold mb-4" style="font-family:'Manrope',sans-serif;">Our Mission</h2>
        <p class="text-body-secondary fs-5 mb-4">Wibscreen exists to help people manage their digital workspace with clarity. We believe your browser should work <em>for</em> you — not against you.</p>
        <p class="text-body-secondary">With smart collections, in-app browsing, and a clean UI, we've built a tool that puts you back in control of your online life. Whether you're a developer, researcher, or creator, Wibscreen helps you stay in the flow.</p>
        
        <div class="row g-4 mt-2">
           <div class="col-6">
             <div class="h3 fw-bold text-primary mb-0">100%</div>
             <div class="small text-body-secondary uppercase fw-bold" style="letter-spacing:1px;">Client-side</div>
           </div>
           <div class="col-6">
             <div class="h3 fw-bold text-primary mb-0">Privacy</div>
             <div class="small text-body-secondary uppercase fw-bold" style="letter-spacing:1px;">Guaranteed</div>
           </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container py-5">
    <div class="text-center mb-5">
      <span class="wb-section-label">Who we serve</span>
      <h2 class="h1 fw-bold" style="font-family:'Manrope',sans-serif;">Built for everyone</h2>
    </div>

    <div class="row g-4">
      @php 
        $audiences = [
          ['fas fa-briefcase','#6366f1','Professionals','Keep client sites, dashboards, and tools organized by project.'],
          ['fas fa-graduation-cap','#22c55e','Students','Group research links, course portals, and resources by subject.'],
          ['fas fa-code','#f59e0b','Developers','Store docs, APIs, and dev tools in instant-access collections.'],
          ['fas fa-pencil','#ec4899','Creators','Organize inspiration, platforms, and tools in one place.']
        ]; 
      @endphp
      @foreach($audiences as $a)
      <div class="col-sm-6 col-lg-3">
        <div class="wb-page-card h-100 text-center p-4">
          <div class="mx-auto mb-3" style="width:50px; height:50px; background:{{ $a[1] }}15; color:{{ $a[1] }}; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
            <i class="{{ $a[0] }}"></i>
          </div>
          <h5 class="fw-bold mb-2">{{ $a[2] }}</h5>
          <p class="text-body-secondary small mb-0">{{ $a[3] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
