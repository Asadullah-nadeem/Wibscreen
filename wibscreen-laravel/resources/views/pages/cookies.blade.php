@extends('layouts.app')
@section('title', 'Cookie Policy — Wibscreen')

@section('content')
<section class="wb-hero pb-4">
  <div class="container text-center">
    <span class="wb-section-label">Legal</span>
    <h1 class="wb-hero-title">Cookie Policy</h1>
    <p class="wb-hero-sub">Understanding how we use cookies and storage to improve your experience.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width: 900px;">
    <div class="wb-card p-4 p-md-5">
      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">1. What are Cookies and Local Storage?</h2>
        <p class="text-secondary">Cookies are small text files stored on your device when you visit a website. Browser Local Storage is a similar technology that allows websites to store larger amounts of data locally on your machine. Wibscreen uses both to provide a seamless and fast workspace experience.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">2. How We Use These Technologies</h2>
        <p class="text-secondary">Wibscreen uses these technologies for the following purposes:</p>
        <ul class="text-secondary mt-3">
          <li><strong>Authentication:</strong> We use essential cookies to keep you signed in as you move between different parts of the application.</li>
          <li><strong>Workspace Persistence:</strong> We use browser Local Storage to save your collections, saved URLs, and tab configurations. This ensures your workspace is exactly as you left it when you return.</li>
          <li><strong>User Preferences:</strong> We store your UI preferences, such as Dark Mode or Light Mode settings, sidebar width, and layout choices.</li>
          <li><strong>Security:</strong> Cookies help us identify and prevent security risks, protecting your account from unauthorized access.</li>
        </ul>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">3. Types of Cookies We Use</h2>
        <div class="table-responsive mt-3">
          <table class="table table-bordered text-secondary">
            <thead>
              <tr class="bg-body-tertiary">
                <th>Type</th>
                <th>Purpose</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Essential</strong></td>
                <td>Used for login, session management, and security.</td>
                <td>Session / 30 Days</td>
              </tr>
              <tr>
                <td><strong>Functional</strong></td>
                <td>Used to remember preferences like theme and language.</td>
                <td>Persistent</td>
              </tr>
              <tr>
                <td><strong>Workspace (Local Storage)</strong></td>
                <td>Stores your actual workspace data (URLs, collections).</td>
                <td>Persistent (until cleared)</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">4. Third-Party Cookies</h2>
        <p class="text-secondary">Wibscreen itself does not use third-party tracking or advertising cookies. However, because Wibscreen allows you to load third-party websites inside your workspace (via iframes), those websites may set their own cookies according to their own policies. We have no control over cookies set by external websites you choose to visit.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">5. Managing Your Preferences</h2>
        <p class="text-secondary">Most web browsers allow you to control cookies through their settings. You can:</p>
        <ul class="text-secondary mt-3">
          <li>Delete all cookies and local storage from your browser at any time.</li>
          <li>Set your browser to block all cookies (note: this will prevent Wibscreen from functioning correctly).</li>
          <li>Use "Incognito" or "Private" mode to ensure no data is saved permanently on your device.</li>
        </ul>
        <p class="text-secondary mt-3">To clear your Wibscreen workspace data specifically, you can use the "Reset Workspace" option in your settings or clear the local storage for our domain in your browser tools.</p>
      </div>

      <div class="mb-5">
        <h2 class="h4 fw-bold mb-3">6. Updates to This Policy</h2>
        <p class="text-secondary">We may update our Cookie Policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. Any changes will be posted on this page with an updated "Last Updated" date.</p>
      </div>

      <div class="mt-5 pt-4 border-top border-emphasis-subtle text-secondary small text-center">
        Last Updated: May 05, 2026
      </div>
    </div>
  </div>
</section>
@endsection
