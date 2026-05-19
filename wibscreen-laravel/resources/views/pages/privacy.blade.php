@extends('layouts.app')
@section('title', 'Privacy Policy — Wibscreen')

@section('content')
<section class="wb-hero pb-4">
  <div class="container text-center">
    <span class="wb-section-label">Legal</span>
    <h1 class="wb-hero-title">Privacy Policy</h1>
    <p class="wb-hero-sub">Your privacy is our priority. Learn how we handle your data with care and transparency.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width: 900px;">
    <div class="wb-card p-4 p-md-5">
      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">1. Information We Collect</h2>
        <p class="text-secondary">Wibscreen is designed with a "Privacy-First" philosophy. Unlike many other productivity tools, we aim to collect as little personal data as possible. Our core service operates using a local-first architecture.</p>
        <ul class="text-secondary mt-3">
          <li><strong>Workspace Data:</strong> Your collections, URLs, tab arrangements, and notes are stored exclusively in your browser's local storage. We do not transmit this data to our servers.</li>
          <li><strong>Preference Data:</strong> Settings like theme selection (Dark/Light), sidebar state, and font preferences are also stored locally.</li>
          <li><strong>Technical Data:</strong> When you access our website, we may collect minimal technical data such as IP address and browser type for security and diagnostic purposes.</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">2. Account and Profile Data</h2>
        <p class="text-secondary">To provide personalized features and manage subscriptions, we collect the following information when you register:</p>
        <ul class="text-secondary mt-3">
          <li><strong>Identity Information:</strong> Your name and email address.</li>
          <li><strong>Authentication:</strong> Securely hashed passwords. We never store your password in plain text.</li>
          <li><strong>Subscription Details:</strong> Information about your current plan and payment status (managed through our payment partner, Razorpay).</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">3. How We Use Your Data</h2>
        <p class="text-secondary">Your data is used strictly for the following purposes:</p>
        <ul class="text-secondary mt-3">
          <li>To maintain and provide the core functionality of Wibscreen.</li>
          <li>To manage your account and process payments for premium services.</li>
          <li>To send essential service-related emails (e.g., password resets, security alerts).</li>
          <li>To improve the technical performance and security of our platform.</li>
        </ul>
        <p class="text-secondary mt-3"><strong>We never sell, rent, or share your personal data with third parties for marketing purposes.</strong></p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">4. Cookies and Local Storage</h2>
        <p class="text-secondary">Wibscreen uses cookies and browser local storage to enhance your experience:</p>
        <ul class="text-secondary mt-3">
          <li><strong>Essential Cookies:</strong> Necessary for authentication and session management.</li>
          <li><strong>Local Storage:</strong> Used to save your workspace data on your own device. This ensures your data remains private and fast.</li>
        </ul>
        <p class="text-secondary mt-3">For more details, please see our <a href="{{ route('cookies') }}" class="text-primary">Cookie Policy</a>.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">5. Data Security</h2>
        <p class="text-secondary">We implement robust security measures to protect your information:</p>
        <ul class="text-secondary mt-3">
          <li><strong>Encryption:</strong> All data transmitted between your browser and our servers is protected by SSL/TLS encryption.</li>
          <li><strong>Secure Storage:</strong> Account data on our servers is protected by firewalls and access controls.</li>
          <li><strong>Local Safety:</strong> Since your workspace data is stored locally, its security depends on your device's security. We recommend keeping your system and browser updated.</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">6. Your Data Rights</h2>
        <p class="text-secondary">Depending on your location, you may have rights under GDPR, CCPA, or other regulations:</p>
        <ul class="text-secondary mt-3">
          <li><strong>Right to Access:</strong> You can request a copy of the personal data we hold about you.</li>
          <li><strong>Right to Rectification:</strong> You can update your account information at any time.</li>
          <li><strong>Right to Erasure:</strong> You can request that we delete your account and all associated personal data.</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">7. Contact Us</h2>
        <p class="text-secondary">If you have any questions about this Privacy Policy or how we handle your data, please reach out to us through our <a href="{{ route('support') }}" class="text-primary">Support Page</a> or email us at privacy@wibscreen.com.</p>
      </div>

      <div class="mt-5 pt-4 border-top border-emphasis-subtle text-secondary small text-center">
        Last Updated: May 05, 2026
      </div>
    </div>
  </div>
</section>
@endsection
