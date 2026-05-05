@extends('layouts.app')
@section('title', 'Cookie Policy — Wibscreen')

@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container text-center">
    <span class="wb-section-label">Legal</span>
    <h1 class="wb-hero-title">Cookie Policy</h1>
    <p class="wb-hero-sub">Understanding how we use cookies to improve your experience.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width: 800px;">
    <div class="wb-card p-4 p-md-5">
      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">1. What are Cookies?</h2>
        <p class="text-secondary">Cookies are small text files stored on your device when you visit a website. They help us remember your preferences and keep you logged in.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">2. Essential Cookies</h2>
        <p class="text-secondary">We use essential cookies for core functionality, such as user authentication and security. These cannot be disabled as the service would not function correctly without them.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">3. Preference Cookies</h2>
        <p class="text-secondary">These cookies allow us to remember your settings, such as your chosen theme (Dark/Light mode) and language preferences.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">4. Managing Cookies</h2>
        <p class="text-secondary">You can manage or disable cookies through your browser settings. However, please note that disabling essential cookies may impact your ability to use Wibscreen.</p>
      </div>

      <div class="mt-5 pt-4 border-top border-emphasis-subtle text-secondary small text-center">
        Last Updated: May 05, 2026
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
