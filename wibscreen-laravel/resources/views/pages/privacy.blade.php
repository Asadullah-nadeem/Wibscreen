@extends('layouts.app')
@section('title', 'Privacy Policy — Wibscreen')

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container text-center">
    <span class="wb-section-label">Legal</span>
    <h1 class="wb-hero-title">Privacy Policy</h1>
    <p class="wb-hero-sub">Your privacy is our priority. Learn how we handle your data with care and transparency.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width: 800px;">
    <div class="wb-card p-4 p-md-5">
      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">1. Information We Collect</h2>
        <p class="text-secondary">Wibscreen is designed to be privacy-first. We do not store your collection data, tabs, or browsing history on our servers. All workspace data is stored locally in your browser's localStorage.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">2. Account Data</h2>
        <p class="text-secondary">When you create an account, we store your email address and name to manage your subscription and provide access to premium features. This data is stored securely using industry-standard encryption.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">3. Cookies</h2>
        <p class="text-secondary">We use essential cookies to maintain your session and remember your preferences (like theme settings). We do not use tracking cookies for advertising purposes.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">4. Data Security</h2>
        <p class="text-secondary">We implement a variety of security measures to maintain the safety of your personal information. However, no method of transmission over the Internet is 100% secure.</p>
      </div>

      <div class="mt-5 pt-4 border-top border-emphasis-subtle text-secondary small text-center">
        Last Updated: May 05, 2026
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
