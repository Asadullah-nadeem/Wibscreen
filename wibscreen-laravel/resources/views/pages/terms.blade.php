@extends('layouts.app')
@section('title', 'Terms of Service — Wibscreen')

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container text-center">
    <span class="wb-section-label">Legal</span>
    <h1 class="wb-hero-title">Terms of Service</h1>
    <p class="wb-hero-sub">The rules and guidelines for using the Wibscreen platform.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width: 800px;">
    <div class="wb-card p-4 p-md-5">
      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">1. Acceptance of Terms</h2>
        <p class="text-secondary">By accessing and using Wibscreen, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the service.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">2. Use of Service</h2>
        <p class="text-secondary">Wibscreen provides a browser workspace tool. You are responsible for any content you access or organize through our platform. You must use the service in compliance with all applicable laws.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">3. Subscriptions</h2>
        <p class="text-secondary">Some features require a paid subscription. All payments are processed securely through Razorpay. You can cancel your subscription at any time.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">4. Limitation of Liability</h2>
        <p class="text-secondary">Wibscreen is provided "as is" without any warranties. We are not liable for any data loss or damages resulting from the use of our service.</p>
      </div>

      <div class="mt-5 pt-4 border-top border-emphasis-subtle text-secondary small text-center">
        Last Updated: May 05, 2026
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
