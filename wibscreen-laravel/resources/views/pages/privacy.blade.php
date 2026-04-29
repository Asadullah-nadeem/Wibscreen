@extends('layouts.app')
@section('title', 'Privacy Policy — Wibscreen')
@section('content')
@include('partials.navbar')
<section class="wb-hero pb-4"><div class="container">
  <span class="wb-section-label">Legal</span>
  <h1 class="wb-hero-title mb-2">Privacy Policy</h1>
  <p class="wb-hero-sub text-body-secondary">Last updated: {{ date('F Y') }}</p>
</div></section>
<section class="py-5"><div class="container" style="max-width:760px;">
  @php $sections=[
    ['Overview','Wibscreen is a client-side browser workspace. We are committed to your privacy. This policy explains what data we handle and how.'],
    ['Data We Collect','We collect no personal data on our servers. Your workspace — collections, saved websites, settings — is stored entirely in your browser\'s localStorage. It never leaves your device unless you explicitly export it.'],
    ['How Data Is Used','Your data is used only to operate the Wibscreen application locally in your browser. We do not sell, share, or transmit your workspace data to any third party.'],
    ['Cookies & Storage','Wibscreen uses browser localStorage (not cookies) to persist your workspace. See our Cookies Policy for more detail.'],
    ['Third-Party Services','We use Google Fonts (CDN), Bootstrap (CDN), and Font Awesome (CDN) for styling. These may log basic request data under their own privacy policies. We do not use analytics or advertising trackers.'],
    ['Your Rights','You can delete all Wibscreen data at any time by clearing your browser\'s localStorage for this site. We do not retain any data on our servers to delete.'],
    ['Contact','Questions about this policy? Use our Support page to reach us.'],
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
