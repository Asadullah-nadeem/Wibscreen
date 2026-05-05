<footer class="wb-footer">
  <div class="container">
    <div class="row g-4 mb-5">
      <!-- Brand Section -->
      <div class="col-lg-4 col-md-6">
        <div class="d-flex align-items-center gap-2 mb-4">
          <div class="bg-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen" style="width: 24px; height: 24px;">
          </div>
          <span class="fw-bold fs-5 display-font text-emphasis">Wibscreen</span>
        </div>
        <p class="text-secondary small mb-4" style="max-width: 300px; line-height: 1.6;">
          Reimagining your browser workspace. Organize your digital life with clarity and speed.
        </p>
        <div class="d-flex gap-3">
          <a href="#" class="btn btn-outline-wb p-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="btn btn-outline-wb p-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-github"></i></a>
          <a href="#" class="btn btn-outline-wb p-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>

      <!-- Links Sections -->
      <div class="col-lg-2 col-6">
        <h6 class="fw-bold display-font mb-4">Platform</h6>
        <ul class="list-unstyled d-flex flex-column gap-2">
          <li><a href="{{ route('home') }}" class="footer-link">Home</a></li>
          <li><a href="{{ route('about') }}" class="footer-link">About Us</a></li>
          <li><a href="{{ route('pricing') }}" class="footer-link">Pricing</a></li>
          <li><a href="{{ route('support') }}" class="footer-link">Support</a></li>
        </ul>
      </div>

      <div class="col-lg-2 col-6">
        <h6 class="fw-bold display-font mb-4">Legal</h6>
        <ul class="list-unstyled d-flex flex-column gap-2">
          <li><a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a></li>
          <li><a href="{{ route('terms') }}" class="footer-link">Terms of Service</a></li>
          <li><a href="{{ route('security') }}" class="footer-link">Security</a></li>
          <li><a href="{{ route('cookies') }}" class="footer-link">Cookie Policy</a></li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-6">
        <h6 class="fw-bold display-font mb-4">Newsletter</h6>
        <p class="text-secondary small mb-4">Stay updated with the latest features and news.</p>
        <form class="d-flex gap-2">
          <input type="email" class="form-control bg-transparent border-emphasis-subtle rounded-3 py-2 px-3 shadow-none" placeholder="Email address" style="font-size: 0.9rem;">
          <button type="button" class="btn btn-primary-wb px-3 py-2"><i class="bi bi-send-fill"></i></button>
        </form>
      </div>
    </div>

    <!-- Bottom Section -->
    <div class="pt-4 mt-4 border-top border-emphasis-subtle d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
      <div class="text-secondary small">
        &copy; {{ date('Y') }} Wibscreen. All rights reserved.
      </div>
      <div class="text-secondary small d-flex align-items-center gap-2">
        <span>Designed & Developed by</span>
        <a href="https://codeaxe.co.in" target="_blank" class="text-primary fw-bold text-decoration-none">CodeAxe Technologies</a>
        <span class="opacity-25">|</span>
        <span>Built with <i class="bi bi-heart-fill text-danger mx-1"></i> in India</span>
      </div>
    </div>
  </div>
</footer>
