@extends('layouts.app')
@section('title', 'Terms & Conditions — Wibscreen')
@section('content')
@include('partials.navbar')
<section class="wb-hero pb-4"><div class="container">
  <span class="wb-section-label">Legal</span>
  <h1 class="wb-hero-title mb-2">Terms &amp; Conditions</h1>
  <p class="wb-hero-sub text-body-secondary">Last updated: {{ date('F Y') }}</p>
</div></section>
<section class="py-5"><div class="container" style="max-width:760px;">
  @php $sections=[
    ['Acceptance of Terms','By accessing or using Wibscreen you agree to be bound by these Terms. If you disagree with any part, do not use the platform.'],
    ['Use of the Platform','Wibscreen is provided for personal, non-commercial use unless you hold a Business plan. You agree not to misuse the service, attempt to circumvent security measures, or use it for unlawful purposes.'],
    ['User Content','You are solely responsible for the websites you save to your workspace. Wibscreen does not review, endorse, or take responsibility for any third-party content you access through the app.'],
    ['Intellectual Property','The Wibscreen name, logo, design, and code are the intellectual property of Wibscreen. You may not copy, modify, or redistribute them without written permission.'],
    ['Disclaimer of Warranties','Wibscreen is provided "as is" without warranty of any kind. We do not guarantee uninterrupted access or that all third-party websites will be embeddable.'],
    ['Limitation of Liability','To the maximum extent permitted by law, Wibscreen is not liable for any indirect, incidental, or consequential damages arising from your use of the platform.'],
    ['Changes to Terms','We may update these terms at any time. Continued use of Wibscreen after changes constitutes acceptance of the updated terms.'],
  ]; @endphp
  @foreach($sections as $i=>$s)
  <div class="wb-page-card mb-3">
    <h2 class="h5 fw-bold mb-2">{{ ($i+1) }}. {{ $s[0] }}</h2>
    <p class="text-body-secondary mb-0">{{ $s[1] }}</p>
  </div>
  @endforeach
</div></section>
@include('partials.footer')
@endsection
