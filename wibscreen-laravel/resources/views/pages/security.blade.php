@extends('layouts.app')
@section('title', 'Security — Wibscreen')
@section('content')
@include('partials.navbar')
<section class="wb-hero pb-4"><div class="container">
  <span class="wb-section-label">Security</span>
  <h1 class="wb-hero-title mb-2">Security at Wibscreen</h1>
  <p class="wb-hero-sub text-body-secondary">How we keep your workspace safe and private.</p>
</div></section>
<section class="py-5"><div class="container" style="max-width:760px;">
  @php $sections=[
    ['Client-Side Architecture','Wibscreen is a client-side application. Your workspace data (collections, URLs, settings) is stored in your browser\'s localStorage — not on any remote server. This means no data breach on our end can expose your workspace.'],
    ['Iframe Sandbox','When you open websites inside Wibscreen, they are loaded in sandboxed iframes. This prevents cross-site scripting (XSS) attacks originating from embedded pages from accessing your Wibscreen workspace.'],
    ['No Password Storage','Wibscreen does not store passwords. Authentication state is tracked only via a session flag in localStorage. For production use, we recommend connecting to a backend authentication service.'],
    ['Third-Party Content','Wibscreen allows you to embed any website. We are not responsible for the security of third-party content. Some sites (e.g. Google, Facebook) block iframe embedding by design — this is a browser security feature, not a bug.'],
    ['HTTPS Only','The hosted version of Wibscreen runs exclusively over HTTPS to protect data in transit.'],
    ['Responsible Disclosure','Found a security issue? Please report it via our Support page. We take security reports seriously and respond within 48 hours.'],
  ]; @endphp
  @foreach($sections as $i=>$s)
  <div class="wb-page-card mb-3">
    <h2 class="h5 fw-bold mb-2"><i class="fas fa-shield-halved text-primary me-2"></i>{{ $s[0] }}</h2>
    <p class="text-body-secondary mb-0">{{ $s[1] }}</p>
  </div>
  @endforeach
</div></section>
@include('partials.footer')
@endsection
