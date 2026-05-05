@extends('layouts.app')
@section('title', 'Security — Wibscreen')
@section('content')
@include('partials.navbar')

<section class="wb-hero pb-4">
  <div class="container">
    <span class="wb-section-label">Security</span>
    <h1 class="wb-hero-title mb-2">Security at Wibscreen</h1>
    <p class="wb-hero-sub text-body-secondary">How we keep your workspace safe and private.</p>
  </div>
</section>

<section class="py-5">
  <div class="container" style="max-width:800px;">
    <div class="wb-page-card shadow-sm border-0">
      <div class="prose">
        @php $sections=[
          ['Client-Side Architecture','Wibscreen is a client-side application. Your workspace data (collections, URLs, settings) is stored in your browser\'s localStorage — not on any remote server. This means no data breach on our end can expose your workspace.'],
          ['Iframe Sandbox','When you open websites inside Wibscreen, they are loaded in sandboxed iframes. This prevents cross-site scripting (XSS) attacks originating from embedded pages from accessing your Wibscreen workspace.'],
          ['Account Security','Authentication state is tracked securely. For production use, we utilize standard Laravel authentication protocols ensuring your account and subscription are protected.'],
          ['Third-Party Content','Wibscreen allows you to embed any website. We are not responsible for the security of third-party content. Some sites block iframe embedding by design — this is a browser security feature.'],
          ['HTTPS Only','The hosted version of Wibscreen runs exclusively over HTTPS to protect data in transit.'],
          ['Responsible Disclosure','Found a security issue? Please report it via our Support page. We take security reports seriously and respond within 48 hours.'],
        ]; @endphp
        
        @foreach($sections as $i=>$s)
        <div class="mb-5 {{ $loop->last ? 'mb-0' : '' }}">
          <h2 class="h5 fw-bold mb-3 text-body-emphasis border-start border-primary border-4 ps-3" style="font-family:'Manrope',sans-serif;">
            <i class="fas fa-shield-halved me-2 opacity-25"></i>{{ $s[0] }}
          </h2>
          <p class="text-body-secondary lh-lg mb-0">{{ $s[1] }}</p>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

@include('partials.footer')
@endsection
