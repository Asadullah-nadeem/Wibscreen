<footer class="wb-footer">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="d-flex align-items-center gap-2 mb-3">
          <div class="wb-logo-badge" style="width:30px;height:30px;font-size:.7rem;">WS</div>
          <span class="fw-bold" style="font-family:'Manrope',sans-serif;">Wibscreen</span>
        </div>
        <p class="small text-body-secondary mb-0">Your personal browser workspace. Organize the web your way.</p>
      </div>
      <div class="col-sm-6 col-lg-2">
        <h6 class="fw-bold small text-uppercase mb-3" style="letter-spacing:.8px;opacity:.5;">Platform</h6>
        <div class="d-flex flex-column gap-2">
          <a href="{{ route('home') }}"    class="small">Home</a>
          <a href="{{ route('about') }}"   class="small">About Us</a>
          <a href="{{ route('pricing') }}" class="small">Pricing</a>
          <a href="{{ route('support') }}" class="small">Support</a>
        </div>
      </div>
      <div class="col-sm-6 col-lg-2">
        <h6 class="fw-bold small text-uppercase mb-3" style="letter-spacing:.8px;opacity:.5;">Legal</h6>
        <div class="d-flex flex-column gap-2">
          <a href="{{ route('privacy') }}"  class="small">Privacy Policy</a>
          <a href="{{ route('terms') }}"    class="small">Terms</a>
          <a href="{{ route('security') }}" class="small">Security</a>
          <a href="{{ route('cookies') }}"  class="small">Cookies</a>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <h6 class="fw-bold small text-uppercase mb-3" style="letter-spacing:.8px;opacity:.5;">Account</h6>
        <div class="d-flex flex-column gap-2">
          <a href="{{ route('login') }}"  class="small">Sign In</a>
          <a href="{{ route('signup') }}" class="small">Create Account</a>
        </div>
      </div>
    </div>
    <div class="border-top pt-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-color:var(--wb-border)!important;">
      <span class="small text-body-secondary">© {{ date('Y') }} Wibscreen. All rights reserved.</span>
      <div class="d-flex gap-3">
        <a href="{{ route('privacy') }}"  class="small text-body-secondary text-decoration-none">Privacy</a>
        <a href="{{ route('terms') }}"    class="small text-body-secondary text-decoration-none">Terms</a>
        <a href="{{ route('security') }}" class="small text-body-secondary text-decoration-none">Security</a>
      </div>
    </div>
    <div class="border-top pt-3 mt-1 text-center" style="border-color:var(--wb-border)!important;">
      <span class="small text-body-secondary">
        Developed &amp; Designed by
        <a href="https://codeaxe.co.in" target="_blank" rel="noopener" class="text-decoration-none fw-semibold" style="color:var(--wb-primary);">CodeAxe Technologies</a>
        — Built with <span style="color:#ef4444;">❤️</span> in India
      </span>
    </div>
  </div>
</footer>
