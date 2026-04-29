@extends('layouts.app')
@section('title', 'Cookies Policy — Wibscreen')
@section('content')
@include('partials.navbar')
<section class="wb-hero pb-4"><div class="container">
  <span class="wb-section-label">Legal</span>
  <h1 class="wb-hero-title mb-2">Cookies Policy</h1>
  <p class="wb-hero-sub text-body-secondary">Last updated: {{ date('F Y') }}</p>
</div></section>
<section class="py-5"><div class="container" style="max-width:760px;">
  @php $sections=[
    ['Do We Use Cookies?','Wibscreen does not use traditional HTTP cookies for tracking or analytics. We use browser localStorage exclusively to store your workspace data locally on your device.'],
    ['What Is localStorage?','localStorage is a web browser feature that allows websites to store data on your device — similar to cookies, but the data never leaves your browser and is not sent to any server automatically.'],
    ['What We Store','We store: your collections list, saved websites, UI settings (theme, accent color, layout), and a session flag (wb_logged_in). None of this data is transmitted to our servers.'],
    ['Third-Party Scripts','We load Bootstrap, Font Awesome, and Google Fonts from CDNs. These CDNs may set their own cookies or log request data. Please refer to their respective privacy policies.'],
    ['Controlling Your Data','You can clear all Wibscreen localStorage at any time via your browser\'s developer tools → Application → Local Storage. This will reset your workspace to its default state.'],
    ['No Advertising Cookies','The Free plan shows minimal ads but we do not use advertising tracking cookies. Pro and Business plans are completely ad-free.'],
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
