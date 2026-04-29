<nav class="wb-navbar">
  <div class="container d-flex align-items-center justify-content-between gap-3">
    <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none" style="color:inherit;">
      <div class="wb-logo-badge">WS</div>
      <span class="fw-bold" style="font-family:'Manrope',sans-serif;font-size:1.05rem;">Wibscreen</span>
    </a>

    <div class="d-none d-md-flex gap-3 align-items-center">
      <a href="{{ route('home') }}"    class="text-body-secondary text-decoration-none small fw-medium {{ request()->routeIs('home')    ? 'text-white' : '' }}">Home</a>
      <a href="{{ route('about') }}"   class="text-body-secondary text-decoration-none small fw-medium {{ request()->routeIs('about')   ? 'text-white' : '' }}">About</a>
      <a href="{{ route('pricing') }}" class="text-body-secondary text-decoration-none small fw-medium {{ request()->routeIs('pricing') ? 'text-white' : '' }}">Pricing</a>
      <a href="{{ route('support') }}" class="text-body-secondary text-decoration-none small fw-medium {{ request()->routeIs('support') ? 'text-white' : '' }}">Support</a>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('login') }}"  class="btn btn-outline-secondary btn-sm">Sign In</a>
      <a href="{{ route('signup') }}" class="btn btn-primary btn-sm fw-semibold">Get Started</a>
    </div>
  </div>
</nav>
