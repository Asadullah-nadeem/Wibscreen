@extends('layouts.app')
@section('title', 'Security — Wibscreen')

@section('content')
<section class="wb-hero pb-4">
  <div class="container text-center">
    <span class="wb-section-label">Trust & Safety</span>
    <h1 class="wb-hero-title">Security at Wibscreen</h1>
    <p class="wb-hero-sub">How we protect your workspace, your account, and your data.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width: 900px;">
    <div class="wb-card p-4 p-md-5">
      <div class="mb-5 text-center">
        <i class="bi bi-shield-check display-1 text-primary mb-3"></i>
        <h2 class="fw-bold mb-3">Your Security is Our Foundation</h2>
        <p class="text-secondary lead">We believe that a productive workspace must be a secure one. Wibscreen is built with industry-leading security practices to ensure your data remains private and protected.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3"><i class="bi bi-lock-fill text-primary me-2"></i>1. Data Encryption</h2>
        <p class="text-secondary">We use high-level encryption both in transit and at rest to safeguard your information:</p>
        <ul class="text-secondary mt-3">
          <li><strong>SSL/TLS Encryption:</strong> All data transmitted between your device and our servers is encrypted using Secure Sockets Layer (SSL) and Transport Layer Security (TLS) protocols.</li>
          <li><strong>Password Hashing:</strong> We never store plain-text passwords. We use robust hashing algorithms (like Argon2 or BCrypt) to ensure that even in the event of a breach, your credentials remain secure.</li>
          <li><strong>API Security:</strong> Our backend APIs are protected by strict authentication and authorization headers.</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3"><i class="bi bi-browser-safari text-primary me-2"></i>2. Local-First Security Architecture</h2>
        <p class="text-secondary">One of the most secure ways to protect data is to not collect it at all. Our unique architecture keeps your most sensitive information on your own machine:</p>
        <ul class="text-secondary mt-3">
          <li><strong>Sandboxed Storage:</strong> Your workspace data (collections, tabs, URLs) is stored in your browser's local storage, which is sandboxed and inaccessible to other websites.</li>
          <li><strong>Client-Side Processing:</strong> Most of Wibscreen's logic runs directly in your browser. This reduces the amount of data that needs to be sent to our servers.</li>
          <li><strong>Iframe Isolation:</strong> Websites loaded inside Wibscreen are isolated from each other and from the core Wibscreen application using standard browser security policies.</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3"><i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>3. Payment and Financial Security</h2>
        <p class="text-secondary">We take financial data security extremely seriously. Wibscreen does not store or process your credit card details directly.</p>
        <ul class="text-secondary mt-3">
          <li><strong>PCI-DSS Compliance:</strong> All payments are handled by Razorpay, which is a PCI-DSS Level 1 compliant payment gateway.</li>
          <li><strong>Secure Tokens:</strong> We only receive secure tokens and payment status from our processor; your actual card number never touches our servers.</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3"><i class="bi bi-server text-primary me-2"></i>4. Infrastructure and Operations</h2>
        <p class="text-secondary">Our servers and infrastructure are managed with a focus on reliability and security:</p>
        <ul class="text-secondary mt-3">
          <li><strong>Cloud Security:</strong> We use reputable cloud providers with world-class physical and network security.</li>
          <li><strong>Regular Audits:</strong> We periodically review our codebase and infrastructure for vulnerabilities and performance bottlenecks.</li>
          <li><strong>Access Control:</strong> Access to our production environment is strictly limited to authorized personnel only, using multi-factor authentication.</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3"><i class="bi bi-bug-fill text-primary me-2"></i>5. Responsible Disclosure</h2>
        <p class="text-secondary">If you are a security researcher and have found a vulnerability in Wibscreen, we encourage you to disclose it to us responsibly. We will work with you to understand and resolve the issue as quickly as possible.</p>
        <p class="text-secondary mt-2">Please report security issues through our <a href="{{ route('pages.support') }}" class="text-primary">Support Page</a> or email us at security@wibscreen.com.</p>
      </div>

      <div class="mt-5 pt-4 border-top border-emphasis-subtle text-secondary small text-center">
        Last Updated: May 05, 2026
      </div>
    </div>
  </div>
</section>
@endsection
