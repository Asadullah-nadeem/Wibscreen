@extends('layouts.app')
@section('title', 'Support & Feedback — Wibscreen')

@section('content')
@include('partials.navbar')

<!-- Hero Section -->
<section class="wb-hero pb-4">
  <div class="container">
    <span class="wb-section-label">Support</span>
    <h1 class="wb-hero-title">How can we help?</h1>
    <p class="wb-hero-sub">Send us a message and our team will get back to you within 24 hours. We're here to help you succeed.</p>
  </div>
</section>

<!-- Main Content -->
<section class="py-5">
  <div class="container" style="max-width:1000px;">
    <div class="row g-5">
      <!-- Contact Form -->
      <div class="col-lg-7">
        <div class="wb-card shadow-lg border-0">
          <h2 class="h3 fw-bold mb-4">Send a Message</h2>
          
          @if(session('success'))
            <div class="alert alert-success border-0 py-3 mb-4 rounded-3 d-flex align-items-center" style="background:rgba(34,197,94,0.1); color:#22c55e;">
              <i class="bi bi-check-circle-fill me-3 fs-4"></i> <div>{{ session('success') }}</div>
            </div>
          @endif

          <form action="{{ url('/support') }}" method="POST">
            @csrf
            <div class="mb-4">
              <label class="form-label small fw-bold text-uppercase opacity-75">Category</label>
              <select name="category" class="form-select bg-transparent border-emphasis-subtle py-3 rounded-3 shadow-none" required>
                <option value="">Select a category…</option>
                <option>General Question</option>
                <option>Bug Report</option>
                <option>Feature Request</option>
                <option>Billing</option>
                <option>Business Inquiry</option>
              </select>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-sm-6">
                <label class="form-label small fw-bold text-uppercase opacity-75">Your Name</label>
                <input type="text" name="name" class="form-control bg-transparent border-emphasis-subtle py-3 rounded-3 shadow-none" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Enter your name" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label small fw-bold text-uppercase opacity-75">Email Address</label>
                <input type="email" name="email" class="form-control bg-transparent border-emphasis-subtle py-3 rounded-3 shadow-none" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="you@example.com" required>
              </div>
            </div>

            <div class="mb-5">
              <label class="form-label small fw-bold text-uppercase opacity-75">Message</label>
              <textarea name="message" class="form-control bg-transparent border-emphasis-subtle rounded-3 shadow-none" rows="5" placeholder="How can we help you today?" required>{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary-wb w-100 py-3 fs-5">
              <i class="bi bi-send-fill me-2"></i> Send Message
            </button>
          </form>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-5">
        <div class="d-flex flex-column gap-4">
          <!-- Direct Help -->
          <div class="wb-card p-4 border-0 shadow-sm bg-body-tertiary">
            <h3 class="h4 fw-bold mb-3"><i class="bi bi-headset text-primary me-2"></i> Direct Help</h3>
            <p class="text-secondary small mb-4">Need immediate assistance? Connect with our team directly.</p>
            <div class="d-flex flex-column gap-2">
               <a href="tel:+919119028555" class="btn btn-outline-wb text-start py-3 px-3 w-100 border-emphasis-subtle"><i class="bi bi-telephone-fill me-3 opacity-50"></i> +91 91190 28555</a>
               <a href="https://wa.me/919119028555" class="btn btn-outline-success text-start py-3 px-3 w-100 border-success-subtle bg-success-subtle text-success fw-bold"><i class="bi bi-whatsapp me-3"></i> Chat on WhatsApp</a>
            </div>
          </div>

          <!-- FAQ Summary -->
          <div class="wb-card p-4 border-0 shadow-sm">
            <h3 class="h4 fw-bold mb-4">Quick FAQ</h3>
            <div class="accordion accordion-flush" id="faq-acc">
              @php 
                $faqs=[
                  ['What is Wibscreen?', 'Wibscreen is a personal browser workspace that allows you to organize websites into collections and open them in a unified interface.'],
                  ['Is my data private?', 'Yes. Wibscreen is built with a privacy-first approach. Your workspace data is stored locally on your device.'],
                  ['Can I use it on mobile?', 'Absolutely. Wibscreen is fully responsive and works perfectly on smartphones, tablets, and desktops.']
                ]; 
              @endphp
              @foreach($faqs as $i=>$faq)
              <div class="accordion-item bg-transparent border-bottom border-emphasis-subtle">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed bg-transparent shadow-none px-0 py-3 fw-bold fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $i }}">
                    {{ $faq[0] }}
                  </button>
                </h2>
                <div id="faq-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#faq-acc">
                  <div class="accordion-body px-0 pt-0 pb-3 text-secondary small">
                    {{ $faq[1] }}
                  </div>
                </div>
              </div>
              @endforeach
            </div>
            <a href="#" class="btn btn-link text-primary fw-bold text-decoration-none px-0 mt-3">View all FAQs <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
