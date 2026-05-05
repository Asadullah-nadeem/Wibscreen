<nav class="wb-navbar">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between">
      <!-- Logo -->
      <a href="{{ route('home') }}" class="d-flex align-items-center gap-3 text-decoration-none group">
        <div class="bg-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm transition-all" style="width: 40px; height: 40px;">
          <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" style="width: 28px; height: 28px;">
        </div>
        <span class="fw-bold fs-4 display-font text-emphasis">Wibscreen</span>
      </a>

      <!-- Desktop Links -->
      <div class="d-none d-lg-flex align-items-center gap-1">
        <a href="{{ route('home') }}" class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('about') }}" class="nav-link-custom {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
        <a href="{{ route('pricing') }}" class="nav-link-custom {{ request()->routeIs('pricing') ? 'active' : '' }}">Pricing</a>
        <a href="{{ route('support') }}" class="nav-link-custom {{ request()->routeIs('support') ? 'active' : '' }}">Support</a>
      </div>

      <!-- Actions -->
      <div class="d-flex align-items-center gap-3">
        <button id="theme-toggle" class="btn btn-link text-secondary p-2 border-0 transition-all hover-bg-light rounded-circle" title="Toggle Theme" style="width: 40px; height: 40px;">
          <i class="bi bi-moon-stars-fill" id="theme-toggle-icon"></i>
        </button>

        @guest
          <div class="d-none d-sm-flex align-items-center gap-2">
            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none fw-bold text-emphasis px-3">Sign In</a>
            <a href="{{ route('signup') }}" class="btn btn-primary-wb">Get Started</a>
          </div>
          <button class="btn btn-link text-emphasis d-lg-none p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
            <i class="bi bi-list fs-3"></i>
          </button>
        @else
          <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-wb d-none d-sm-inline-block">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm rounded-3 px-3 py-2 fw-bold shadow-none">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
              </button>
            </form>
            <button class="btn btn-link text-emphasis d-lg-none p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
              <i class="bi bi-list fs-3"></i>
            </button>
          </div>
        @endguest
      </div>
    </div>
  </div>
</nav>

<!-- Mobile Menu (Offcanvas) -->
<div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="mobileMenu" style="width: 300px;">
  <div class="offcanvas-header border-bottom p-4">
    <div class="d-flex align-items-center gap-2">
       <div class="bg-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
         <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" style="width: 22px; height: 22px;">
       </div>
       <span class="fw-bold fs-5">Wibscreen</span>
    </div>
    <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-4 d-flex flex-column">
    <div class="d-flex flex-column gap-2 mb-5">
      <a href="{{ route('home') }}" class="nav-link-custom w-100 justify-content-start py-3 {{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="bi bi-house-door me-3"></i> Home
      </a>
      <a href="{{ route('about') }}" class="nav-link-custom w-100 justify-content-start py-3 {{ request()->routeIs('about') ? 'active' : '' }}">
        <i class="bi bi-info-circle me-3"></i> About Us
      </a>
      <a href="{{ route('pricing') }}" class="nav-link-custom w-100 justify-content-start py-3 {{ request()->routeIs('pricing') ? 'active' : '' }}">
        <i class="bi bi-tags me-3"></i> Pricing
      </a>
      <a href="{{ route('support') }}" class="nav-link-custom w-100 justify-content-start py-3 {{ request()->routeIs('support') ? 'active' : '' }}">
        <i class="bi bi-headset me-3"></i> Support
      </a>
    </div>
    
    @guest
      <div class="mt-auto d-flex flex-column gap-3">
        <a href="{{ route('login') }}" class="btn btn-outline-wb w-100 py-3">Sign In</a>
        <a href="{{ route('signup') }}" class="btn btn-primary-wb w-100 py-3">Get Started Free</a>
      </div>
    @endguest
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const themeToggle = document.getElementById('theme-toggle');
  const themeIcon = document.getElementById('theme-toggle-icon');
  const navbar = document.querySelector('.wb-navbar');
  
  function updateIcon(theme) {
    if (theme === 'dark') {
      themeIcon.className = 'bi bi-sun-fill';
    } else {
      themeIcon.className = 'bi bi-moon-stars-fill';
    }
  }

  updateIcon(document.documentElement.getAttribute('data-bs-theme'));

  themeToggle.addEventListener('click', function() {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('wb-theme', newTheme);
    updateIcon(newTheme);
  });

  window.addEventListener('scroll', function() {
    if (window.scrollY > 30) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
});
</script>
