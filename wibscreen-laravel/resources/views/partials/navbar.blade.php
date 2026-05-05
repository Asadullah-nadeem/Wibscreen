<nav class="wb-navbar">
  <div class="container d-flex align-items-center justify-content-between gap-3">
    <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none" style="color:inherit;">
      <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen Logo" style="width:32px;height:32px;border-radius:8px;">
      <span class="fw-bold" style="font-family:'Manrope',sans-serif;font-size:1.05rem;">Wibscreen</span>
    </a>

    <div class="d-none d-md-flex gap-3 align-items-center">
      <a href="{{ route('home') }}"    class="text-body-secondary text-decoration-none small fw-medium {{ request()->routeIs('home')    ? 'text-white' : '' }}">Home</a>
      <a href="{{ route('about') }}"   class="text-body-secondary text-decoration-none small fw-medium {{ request()->routeIs('about')   ? 'text-white' : '' }}">About</a>
      <a href="{{ route('pricing') }}" class="text-body-secondary text-decoration-none small fw-medium {{ request()->routeIs('pricing') ? 'text-white' : '' }}">Pricing</a>
      <a href="{{ route('support') }}" class="text-body-secondary text-decoration-none small fw-medium {{ request()->routeIs('support') ? 'text-white' : '' }}">Support</a>
    </div>

    <div class="d-flex gap-2 align-items-center">
      <button id="theme-toggle" class="btn btn-link text-body-secondary p-1 border-0" title="Toggle Dark/Light Mode" style="font-size: 1.1rem;">
        <i class="fas fa-moon" id="theme-toggle-icon"></i>
      </button>

      @guest
        <a href="{{ route('login') }}"  class="btn btn-outline-secondary btn-sm">Sign In</a>
        <a href="{{ route('signup') }}" class="btn btn-primary btn-sm fw-semibold">Sinup</a>
      @else
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Dashboard</a>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-danger btn-sm fw-semibold">Logout</button>
        </form>
      @endguest
    </div>
  </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const themeToggle = document.getElementById('theme-toggle');
  const themeIcon = document.getElementById('theme-toggle-icon');
  
  function updateIcon(theme) {
    if (theme === 'dark') {
      themeIcon.classList.remove('fa-sun');
      themeIcon.classList.add('fa-moon');
    } else {
      themeIcon.classList.remove('fa-moon');
      themeIcon.classList.add('fa-sun');
    }
  }

  // Set initial icon
  updateIcon(document.documentElement.getAttribute('data-bs-theme'));

  themeToggle.addEventListener('click', function() {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('wb-theme', newTheme);
    updateIcon(newTheme);
  });
});
</script>
