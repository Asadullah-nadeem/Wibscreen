@extends('layouts.app')
@section('title', 'Support & Feedback — Wibscreen')

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container">
    <span class="wb-section-label">Support</span>
    <h1 class="wb-hero-title mb-3">How can we help?</h1>
    <p class="wb-hero-sub text-body-secondary">Send us a message and our team will get back to you within 24 hours.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width:900px;">
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="wb-page-card shadow-lg border-0 h-100">
          <h2 class="h4 fw-bold mb-4">Send a Message</h2>
          
          @if(session('success'))
            <div class="alert alert-success border-0 py-3 small mb-4" style="background:rgba(34,197,94,0.1); color:#22c55e;">
              <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
          @endif

          <form action="{{ url('/support') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label small fw-bold">Category</label>
              <select name="category" class="form-select border-secondary-subtle py-2 rounded-3 shadow-none" required>
                <option value="">Select a category…</option>
                <option>General Question</option>
                <option>Bug Report</option>
                <option>Feature Request</option>
                <option>Billing</option>
                <option>Business Inquiry</option>
              </select>
            </div>
            
            <div class="row g-3 mb-3">
              <div class="col-sm-6">
                <label class="form-label small fw-bold">Your Name</label>
                <input type="text" name="name" class="form-control border-secondary-subtle py-2 rounded-3" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Name" required>
              </div>
              <div class="col-sm-6">
                <label class="form-label small fw-bold">Email</label>
                <input type="email" name="email" class="form-control border-secondary-subtle py-2 rounded-3" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="you@example.com" required>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label small fw-bold">Message</label>
              <textarea name="message" class="form-control border-secondary-subtle rounded-3" rows="5" placeholder="Describe your issue or question…" required>{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="wb-btn-primary">
              <i class="fas fa-paper-plane me-2"></i> Send Message
            </button>
          </form>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="d-flex flex-column gap-4 h-100">
          <div class="wb-page-card p-4 shadow-sm border-0">
            <h3 class="h5 fw-bold mb-3"><i class="fas fa-phone-volume text-primary me-2"></i>Direct Help</h3>
            <p class="small text-body-secondary">Need immediate assistance? Connect with us via WhatsApp or Phone.</p>
            <div class="d-flex flex-column gap-2">
               <a href="tel:+919119028555" class="btn btn-outline-secondary btn-sm text-start py-2 rounded-3"><i class="fas fa-phone me-2 opacity-50"></i> +91 91190 28555</a>
               <a href="https://wa.me/919119028555" class="btn btn-outline-success btn-sm text-start py-2 rounded-3 text-success border-success-subtle" style="background:rgba(25,135,84,0.05);"><i class="fab fa-whatsapp me-2"></i> Chat on WhatsApp</a>
            </div>
          </div>

          <div class="wb-page-card p-4 shadow-sm border-0 flex-grow-1">
            <h3 class="h5 fw-bold mb-3">FAQ</h3>
            <div class="accordion accordion-flush" id="faq-acc">
              @php $faqs=[['What is Wibscreen?','Wibscreen is a personal browser workspace — you save and organize websites into collections.'],['Is it free?','Yes! The Free plan is completely free with basic limits.'],['Is my data safe?','Your data is stored in your browser\'s localStorage. No remote storage — your privacy is key.']]; @endphp
              @foreach($faqs as $i=>$faq)
              <div class="accordion-item bg-transparent border-bottom" style="border-color: var(--wb-border) !important;">
                <h2 class="accordion-header"><button class="accordion-button collapsed bg-transparent shadow-none px-0 py-3 small fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $i }}">{{ $faq[0] }}</button></h2>
                <div id="faq-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#faq-acc">
                  <div class="accordion-body px-0 pt-0 pb-3 text-body-secondary x-small">{{ $faq[1] }}</div>
                </div>
              </div>
              @endforeach
            </div>
            <a href="#" class="small text-primary fw-bold text-decoration-none d-inline-block mt-3">View all FAQs →</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
