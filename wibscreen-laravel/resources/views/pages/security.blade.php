@extends('layouts.app')
@section('title', 'Security — Wibscreen')

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container text-center">
    <span class="wb-section-label">Legal</span>
    <h1 class="wb-hero-title">Security</h1>
    <p class="wb-hero-sub">How we protect your workspace and account data.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width: 800px;">
    <div class="wb-card p-4 p-md-5">
      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">1. Data Encryption</h2>
        <p class="text-secondary">All communication between your browser and our servers is encrypted using industry-standard TLS/SSL encryption. Your account credentials are hashed and stored securely.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">2. Local-First Architecture</h2>
        <p class="text-secondary">Unlike traditional browser managers, Wibscreen stores your actual workspace data (collections, tabs, notes) locally in your browser. This means your sensitive browsing organization never touches our servers.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">3. Payment Security</h2>
        <p class="text-secondary">We do not store credit card information. All payments are handled by Razorpay, a PCI-DSS compliant payment gateway.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">4. Regular Audits</h2>
        <p class="text-secondary">We regularly review our codebase and infrastructure for security vulnerabilities to ensure your data remains safe.</p>
      </div>

      <div class="mt-5 pt-4 border-top border-emphasis-subtle text-secondary small text-center">
        Last Updated: May 05, 2026
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
