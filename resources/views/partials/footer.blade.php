<footer class="wb-footer">
  <div class="container">
    <div class="row g-5 mb-5">
      <!-- Brand Section -->
      <div class="col-lg-4 col-md-12">
        <div class="d-flex align-items-center gap-2 mb-4">
          <div class="bg-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" style="width: 28px; height: 28px;">
          </div>
          <span class="fw-bold fs-4 display-font text-emphasis">Wibscreen</span>
        </div>
        <p class="text-secondary mb-4 fs-6" style="max-width: 320px; line-height: 1.7;">
          Reimagining your browser workspace. Organize your digital life with clarity, speed, and total privacy.
        </p>
        <div class="d-flex gap-2">
          <a href="#" class="btn btn-outline-wb p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 12px;"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="btn btn-outline-wb p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 12px;"><i class="bi bi-github"></i></a>
          <a href="#" class="btn btn-outline-wb p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 12px;"><i class="bi bi-linkedin"></i></a>
          <a href="#" class="btn btn-outline-wb p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 12px;"><i class="bi bi-instagram"></i></a>
        </div>
      </div>

      <!-- Links Sections -->
      <div class="col-lg-2 col-md-4 col-6">
        <h6 class="fw-bold text-uppercase fs-xs opacity-50 mb-4" style="letter-spacing: 1px;">Platform</h6>
        <ul class="list-unstyled d-flex flex-column gap-3">
          <li><a href="{{ route('home') }}" class="footer-link">Home</a></li>
          <li><a href="{{ route('about') }}" class="footer-link">About Us</a></li>
          <li><a href="{{ route('pricing') }}" class="footer-link">Pricing</a></li>
          <li><a href="{{ route('support') }}" class="footer-link">Support</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-md-4 col-6">
        <h6 class="fw-bold text-uppercase fs-xs opacity-50 mb-4" style="letter-spacing: 1px;">Legal</h6>
        <ul class="list-unstyled d-flex flex-column gap-3">
          <li><a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a></li>
          <li><a href="{{ route('terms') }}" class="footer-link">Terms of Service</a></li>
          <li><a href="{{ route('security') }}" class="footer-link">Security</a></li>
          <li><a href="{{ route('cookies') }}" class="footer-link">Cookie Policy</a></li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-4">
        <h6 class="fw-bold text-uppercase fs-xs opacity-50 mb-4" style="letter-spacing: 1px;">Stay Connected</h6>
        <p class="text-secondary small mb-4">Get the latest updates on new features and tools.</p>
        <form class="d-flex gap-2">
          <input type="email" class="form-control bg-body-tertiary border-emphasis-subtle rounded-3 py-2 px-3 shadow-none fs-6" placeholder="Email address">
          <button type="button" class="btn btn-primary-wb px-3 py-2"><i class="bi bi-arrow-right"></i></button>
        </form>
      </div>
    </div>

    <!-- Bottom Section -->
    <div class="pt-5 mt-5 border-top border-emphasis-subtle d-flex flex-column flex-md-row justify-content-between align-items-center gap-4">
      <div class="text-secondary small fw-medium">
        &copy; {{ date('Y') }} Wibscreen Platform. All rights reserved.
      </div>
      <div class="text-secondary small d-flex align-items-center gap-2">
        <span>Crafted by</span>
        <a href="https://codeaxe.co.in" target="_blank" class="text-primary fw-bold text-decoration-none">CodeAxe Technologies</a>
        <span class="opacity-25 mx-1">/</span>
        <span class="d-flex align-items-center">Built with <i class="bi bi-heart-fill text-danger mx-1 fs-xs"></i> in India</span>
      </div>
    </div>
  </div>
</footer>
