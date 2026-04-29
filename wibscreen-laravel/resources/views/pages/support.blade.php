@extends('layouts.app')
@section('title', 'Support & Feedback — Wibscreen')
@section('content')
@include('partials.navbar')
<section class="wb-hero pb-4"><div class="container">
  <span class="wb-section-label">Support</span>
  <h1 class="wb-hero-title mb-3">How can we help?</h1>
  <p class="wb-hero-sub text-body-secondary">Send us a message and we'll get back to you within 24 hours.</p>
</div></section>
<section class="py-5"><div class="container" style="max-width:700px;">
  <div class="wb-page-card mb-4">
    <h2 class="h5 fw-bold mb-4">Send a Message</h2>
    <div id="support-success" class="alert alert-success d-none">Message sent! We'll respond within 24 hours.</div>
    <form id="support-form" novalidate>
      <div class="mb-3"><label class="form-label small fw-semibold">Category</label>
        <select class="form-select" id="s-cat"><option value="">Select a category…</option><option>General Question</option><option>Bug Report</option><option>Feature Request</option><option>Billing</option><option>Business Inquiry</option></select>
      </div>
      <div class="row g-3 mb-3"><div class="col-sm-6"><label class="form-label small fw-semibold">Your Name</label><input type="text" id="s-name" class="form-control" placeholder="Name"></div>
      <div class="col-sm-6"><label class="form-label small fw-semibold">Email</label><input type="email" id="s-email" class="form-control" placeholder="you@example.com"></div></div>
      <div class="mb-3"><label class="form-label small fw-semibold">Message</label><textarea id="s-msg" class="form-control" rows="5" placeholder="Describe your issue or question…"></textarea></div>
      <button type="submit" class="btn btn-primary fw-semibold px-4">Send Message</button>
    </form>
  </div>
  <div class="wb-page-card">
    <h2 class="h5 fw-bold mb-3">Frequently Asked Questions</h2>
    @php $faqs=[['What is Wibscreen?','Wibscreen is a personal browser workspace — you save and organize websites into collections and open them in-app.'],['Is it free?','Yes! The Free plan is completely free with basic limits. Pro and Business plans offer more.'],['Can I use it on mobile?','Absolutely. Wibscreen is fully responsive and works on mobile, tablet, and desktop.'],['Where is my data stored?','Your workspace data is stored in your browser\'s localStorage. No account sync yet — coming soon!']]; @endphp
    <div class="accordion" id="faq-acc">
      @foreach($faqs as $i=>$faq)
      <div class="accordion-item border-0 border-bottom" style="background:transparent;border-color:var(--wb-border)!important;">
        <h3 class="accordion-header"><button class="accordion-button {{ $i>0?'collapsed':'' }} bg-transparent shadow-none fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $i }}">{{ $faq[0] }}</button></h3>
        <div id="faq-{{ $i }}" class="accordion-collapse collapse {{ $i===0?'show':'' }}" data-bs-parent="#faq-acc">
          <div class="accordion-body text-body-secondary small">{{ $faq[1] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div></section>
@include('partials.footer')
@endsection
@push('scripts')
<script>
document.getElementById('support-form').addEventListener('submit',function(e){
  e.preventDefault();
  document.getElementById('support-success').classList.remove('d-none');
  this.reset();
  setTimeout(()=>document.getElementById('support-success').classList.add('d-none'),5000);
});
</script>
@endpush
