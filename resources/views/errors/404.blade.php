@extends('layouts.app')
@section('title', '404 — Page Not Found — Wibscreen')
@section('content')
@include('partials.navbar')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:40px 16px;">
  <div>
    <div style="font-size:5rem;font-family:'Manrope',sans-serif;font-weight:900;color:var(--wb-primary);line-height:1;">404</div>
    <h1 class="h3 fw-bold mt-2 mb-2">Page not found</h1>
    <p class="text-body-secondary mb-4">The page you're looking for doesn't exist or has been moved.</p>
    <a href="{{ route('home') }}" class="btn btn-primary px-5 fw-semibold me-2">Go Home</a>
    <a href="{{ route('support') }}" class="btn btn-outline-secondary px-4">Get Help</a>
  </div>
</div>
@include('partials.footer')
@endsection
