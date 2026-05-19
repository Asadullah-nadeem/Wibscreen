<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark" class="wb-app-root">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>Wibscreen | Browser Workspace</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Scripts and Styles -->
  @vite(['resources/css/app.scss', 'resources/js/app.js', 'resources/js/dashboard.js'])

  <script>
    (function() {
      const savedTheme = localStorage.getItem('wb-theme') || 'dark';
      document.documentElement.setAttribute('data-bs-theme', savedTheme);
    })();
  </script>
</head>

<body>
<!-- Sidebar Overlay (mobile) -->
<div class="wb-overlay" id="wb-overlay"></div>

<div class="wb-app">

  <!-- ══════════════════════════════════
       SIDEBAR
  ══════════════════════════════════ -->
  <aside class="wb-sidebar" id="wb-sidebar">

    <!-- Header -->
    <div class="wb-sidebar-header d-flex align-items-center gap-3">
      <img src="{{ asset('assets/img/logo.png') }}" alt="Wibscreen Logo" style="width:30px;height:30px;border-radius:8px;">
      <span class="fw-bold flex-grow-1" style="font-family:'Manrope',sans-serif;font-size:1.05rem;">Wibscreen</span>
      <button class="wb-icon-btn d-md-none" id="wb-sidebar-close" aria-label="Close menu">
        <i class="fas fa-xmark"></i>
      </button>
    </div>

    <!-- Scrollable body -->
    <div class="wb-sidebar-body">
      <!-- Search -->
      <div class="wb-search-wrap mb-3">
        <i class="fas fa-search wb-search-icon"></i>
        <input id="wb-search" type="text" class="form-control wb-search-input"
               placeholder="Search tabs…" autocomplete="off" autocorrect="off" spellcheck="false">
      </div>

      <!-- Label -->
      <span class="wb-label">Collections</span>

      <!-- Nav -->
      <ul class="list-unstyled mb-3" id="wb-nav-list"></ul>

      <!-- New Folder -->
      <button id="wb-new-folder-btn"
              class="btn btn-outline-secondary btn-sm w-100 border-dashed py-2 rounded-3">
        <i class="fas fa-plus me-2"></i>New Folder
      </button>
    </div>

    <!-- Footer -->
    <div class="wb-sidebar-footer d-flex flex-column gap-2">
      <!-- Profile Block -->
      <!-- Profile Block -->
      <button id="wb-profile-btn" class="btn btn-sm text-start d-flex align-items-center gap-2 w-100 p-2 rounded-3" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);">
        <div id="wb-avatar" style="width:32px;height:32px;border-radius:10px;background:var(--wb-primary);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.75rem;color:#fff;flex-shrink:0;font-family:'Manrope',sans-serif;">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div class="text-start overflow-hidden">
          <div class="small fw-semibold text-truncate" id="wb-profile-name" style="max-width:140px;">{{ auth()->user()->name }}</div>
          <div style="font-size:.68rem;opacity:.55;text-transform: capitalize;" id="wb-profile-plan">{{ auth()->user()->plan }} Plan</div>
        </div>
        <i class="fas fa-chevron-right ms-auto opacity-40 small"></i>
      </button>

      <button id="wb-linux-btn" class="wb-footer-btn">
        <i class="fas fa-terminal text-primary"></i>
        <span>Linux VM</span>
      </button>

      <button id="wb-theme-btn" class="wb-footer-btn">
        <i id="wb-theme-icon" class="fas fa-moon"></i>
        <span id="wb-theme-label">Night Mode</span>
      </button>

      <button id="wb-settings-btn" class="wb-footer-btn">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </button>

      <a href="{{ route('pricing') }}" class="wb-footer-btn">
        <i class="fas fa-crown" style="color:#eab308;"></i>
        <span>Upgrade Plan</span>
      </a>

      <div class="d-flex align-items-center gap-2 mt-1 ps-1">
        <div class="wb-status-dot"></div>
        <span class="small text-body-secondary">System Active</span>
      </div>

      <!-- CodeAxe Credit -->
      <div class="border-top pt-2 mt-1" style="border-color:rgba(255,255,255,.07)!important;">
        <p class="mb-0 text-center" style="font-size:.62rem;opacity:.4;line-height:1.5;">
          Developed &amp; Designed by<br>
          <a href="https://codeaxe.co.in" target="_blank" rel="noopener"
             class="text-decoration-none fw-semibold" style="color:var(--wb-primary);opacity:1;">
            CodeAxe Technologies
          </a><br>
          Built with ❤️ in India
        </p>
      </div>
    </div>
  </aside>

  <!-- ══════════════════════════════════
       MAIN
  ══════════════════════════════════ -->
  <main class="wb-main">

    <!-- Top Bar -->
    <div class="wb-topbar">
      <!-- Hamburger (mobile only) -->
      <button class="wb-menu-btn" id="wb-hamburger" aria-label="Open menu">
        <i class="fas fa-bars"></i>
      </button>

      <!-- URL Bar -->
      <div class="wb-url-bar">
        <i class="fas fa-shield-halved text-primary flex-shrink-0"></i>
        <input id="wb-url-display" type="text" value="Workspace Ready" readonly>
        <div class="d-flex gap-2 ms-2 flex-shrink-0">
          <i class="fas fa-rotate-right wb-topbar-icon" id="wb-refresh-btn" title="Refresh"></i>
          <i class="fas fa-house-user wb-topbar-icon" id="wb-home-btn" title="Home"></i>
        </div>
      </div>
    </div>

    <!-- Workspace -->
    <div class="wb-workspace">

      <!-- Dashboard -->
      <div class="wb-dashboard" id="wb-tab-manager">

        <!-- Dashboard Header -->
        <div class="wb-dashboard-header d-flex align-items-start justify-content-between flex-wrap gap-3">
          <div>
            <h1 class="wb-dashboard-title" id="wb-dash-title">Explore Workspace</h1>
            <p class="wb-dashboard-sub">Manage your collections and saved tabs</p>
            
            @if(auth()->user()->isPlanExpired())
              <div class="alert alert-warning border-0 small py-2 mt-2" style="background:rgba(234,179,8,.1);color:#eab308;">
                <i class="fas fa-triangle-exclamation me-2"></i> Your <strong>{{ ucfirst(auth()->user()->plan) }}</strong> plan has expired. Your workspace is currently frozen. <a href="{{ route('pricing') }}" class="fw-bold text-decoration-none" style="color:#eab308;text-decoration:underline!important;">Renew Now</a>
              </div>
            @endif

            @if(session('signup_success'))
              <div class="alert alert-success alert-dismissible fade show small py-2 mt-2 border-0" role="alert" style="background:rgba(34,197,94,.1);color:#22c55e;">
                <i class="fas fa-circle-check me-2"></i> {{ session('signup_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size:.5rem;padding:1rem;"></button>
              </div>
            @endif

            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show small py-2 mt-2 border-0" role="alert" style="background:rgba(34,197,94,.1);color:#22c55e;">
                <i class="fas fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size:.5rem;padding:1rem;"></button>
              </div>
            @endif

            @if(session('info'))
              <div class="alert alert-info alert-dismissible fade show small py-2 mt-2 border-0" role="alert" style="background:rgba(9,150,243,.1);color:#0996f3;">
                <i class="fas fa-circle-info me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size:.5rem;padding:1rem;"></button>
              </div>
            @endif

            @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show small py-2 mt-2 border-0" role="alert" style="background:rgba(239,68,68,.1);color:#ef4444;">
                <i class="fas fa-circle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size:.5rem;padding:1rem;"></button>
              </div>
            @endif
          </div>
          <div class="wb-topbar-actions">
            <div class="btn-group wb-sort-group" role="group">
              <button type="button" id="wb-sort-newest" class="btn btn-outline-secondary active" title="Newest first">
                <i class="fas fa-clock"></i>
              </button>
              <button type="button" id="wb-sort-alpha" class="btn btn-outline-secondary" title="A – Z">
                <i class="fas fa-sort-alpha-down"></i>
              </button>
            </div>
            <button class="wb-fab" id="wb-add-tab-btn" title="Add new tab" aria-label="Add tab">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>

        <!-- Dynamic collection sections -->
        <div id="wb-dashboard"></div>
      </div>

      <!-- Iframe Layer / Browser Panel -->
      <div id="wb-iframe-layer" class="wb-iframe-layer">

        <!-- Browser Toolbar -->
        <div class="wb-browser-toolbar">
          <button class="wb-browser-btn" id="wb-browser-back" title="Back to Workspace" aria-label="Back">
            <i class="fas fa-arrow-left"></i>
          </button>
          <button class="wb-browser-btn" id="wb-browser-refresh" title="Refresh" aria-label="Refresh">
            <i class="fas fa-rotate-right"></i>
          </button>
          <div class="wb-browser-url">
            <i class="fas fa-shield-halved text-primary" style="font-size:.8rem;flex-shrink:0;"></i>
            <span class="wb-browser-url-text" id="wb-browser-url-text">Loading…</span>
          </div>
          <button class="wb-browser-btn" id="wb-browser-newtab" title="Open in new tab" aria-label="Open in new tab">
            <i class="fas fa-arrow-up-right-from-square"></i>
          </button>
          <button class="wb-browser-btn close-btn" id="wb-browser-close" title="Close browser" aria-label="Close">
            <i class="fas fa-xmark"></i>
          </button>
        </div>

        <!-- Loading bar -->
        <div class="wb-load-bar" id="wb-load-bar"></div>

        <!-- Content area: iframes + blocked message -->
        <div class="wb-iframe-content">
          <!-- Blocked site fallback -->
          <div class="wb-iframe-blocked" id="wb-iframe-blocked">
            <div class="wb-iframe-blocked-icon"><i class="fas fa-shield-halved"></i></div>
            <h5 class="fw-bold mb-1">Site cannot be embedded</h5>
            <p class="text-body-secondary small mb-3" id="wb-blocked-domain" style="max-width:340px;">
              This website blocks embedding for security reasons (X-Frame-Options). You can still open it in a new tab.
            </p>
            <button class="btn btn-primary fw-semibold px-4" id="wb-open-newtab-btn">
              <i class="fas fa-arrow-up-right-from-square me-2"></i>Open in New Tab
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="wb-bottombar">
      <div class="d-flex align-items-center gap-2">
        <div class="wb-status-dot"></div>
        <span class="text-body-secondary small" id="wb-status-text">Ready</span>
      </div>
      <span class="badge bg-primary rounded-pill" id="wb-tab-badge" style="display:none;">0</span>
      <div class="px-3 py-1 rounded-pill bg-body-secondary small fw-semibold">Desktop 1</div>
    </div>
  </main>
</div>

<!-- ════════════════════════════════════════════════
     MODALS
════════════════════════════════════════════════ -->

<!-- Add Tab -->
<div class="modal fade" id="wb-modal-tab" tabindex="-1" aria-labelledby="modal-tab-title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="modal-tab-title">Add New Tab</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-3 pb-2">
        <div class="mb-3">
          <label class="form-label small fw-semibold text-body-secondary" for="wb-url-input">Website URL</label>
          <input id="wb-url-input" type="url" class="form-control" placeholder="https://example.com" autocomplete="off">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold text-body-secondary" for="wb-name-input">Display Name <span class="opacity-50">(optional)</span></label>
          <input id="wb-name-input" type="text" class="form-control" placeholder="My Site">
        </div>
        <div>
          <label class="form-label small fw-semibold text-body-secondary" for="wb-col-select">Collection</label>
          <select id="wb-col-select" class="form-select"></select>
        </div>
      </div>
      <div class="modal-footer border-top-0 pt-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="wb-confirm-tab" class="btn btn-primary fw-semibold px-4">Add to Workspace</button>
      </div>
    </div>
  </div>
</div>

<!-- New Folder -->
<div class="modal fade" id="wb-modal-folder" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold">New Collection</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3">
        <input id="wb-folder-input" type="text" class="form-control" placeholder="Work, Social, Research…" autocomplete="off">
      </div>
      <div class="modal-footer border-top-0 pt-2">
        <button type="button" class="btn btn-primary w-100 fw-semibold" id="wb-confirm-folder">Create Collection</button>
      </div>
    </div>
  </div>
</div>

<!-- Rename -->
<div class="modal fade" id="wb-modal-rename" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold">Rename Collection</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3">
        <input id="wb-rename-input" type="text" class="form-control" placeholder="New name…">
      </div>
      <div class="modal-footer border-top-0 pt-2">
        <button type="button" class="btn btn-primary w-100 fw-semibold" id="wb-confirm-rename">Save Changes</button>
      </div>
    </div>
  </div>
</div>

<!-- Settings -->
<div class="modal fade" id="wb-modal-settings" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold">Settings</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3">
        <div class="row g-4">
          <div class="col-sm-6">
            <h6 class="fw-bold small text-body-secondary text-uppercase mb-3" style="letter-spacing:.8px;">Appearance</h6>
            <button class="btn btn-outline-secondary w-100" id="wb-settings-theme-btn">
              <i class="fas fa-circle-half-stroke me-2"></i>Toggle Day / Night
            </button>
          </div>
          <div class="col-sm-6">
            <h6 class="fw-bold small text-body-secondary text-uppercase mb-3" style="letter-spacing:.8px;">Accent Color</h6>
            <input type="color" id="wb-accent-color" class="form-control form-control-color w-100" value="#6366f1">
          </div>
          <div class="col-12">
            <h6 class="fw-bold small text-body-secondary text-uppercase mb-3" style="letter-spacing:.8px;">Layout</h6>
            <div class="row g-3">
              <div class="col-sm-6">
                <label class="form-label small">Card Radius — <span id="wb-radius-val">16px</span></label>
                <input type="range" id="wb-card-radius" class="form-range" min="0" max="32" value="16">
              </div>
              <div class="col-sm-6">
                <label class="form-label small">Sidebar Width — <span id="wb-sw-val">280px</span></label>
                <input type="range" id="wb-sidebar-w" class="form-range" min="200" max="400" value="280">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="wb-modal-profile" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold">My Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3">
        <!-- Avatar + Name -->
        <div class="text-center mb-4">
          <div id="modal-avatar" style="width:72px;height:72px;border-radius:20px;background:var(--wb-primary);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.5rem;color:#fff;margin:0 auto 12px;font-family:'Manrope',sans-serif;">{{ substr(auth()->user()->name, 0, 1) }}</div>
          <h5 class="fw-bold mb-0" id="modal-profile-name">{{ auth()->user()->name }}</h5>
          <p class="small text-body-secondary mb-2" id="modal-profile-email">{{ auth()->user()->email }}</p>
          @php
            $planDetails = [
              'free' => ['label' => 'Free Plan', 'icon' => 'fa-seedling', 'color' => '#64748b', 'desc' => '1 workspace · 10 tabs · 150hrs/mo'],
              'pro' => ['label' => 'Pro Plan', 'icon' => 'fa-bolt', 'color' => '#6366f1', 'desc' => '10 workspaces · Unlimited tabs · 720hrs/mo'],
              'business' => ['label' => 'Business Plan', 'icon' => 'fa-building', 'color' => '#eab308', 'desc' => 'Unlimited everything · 24/7 support']
            ];
            $currentPlan = $planDetails[auth()->user()->plan] ?? $planDetails['free'];
          @endphp
          <span class="badge rounded-pill px-3 py-2" id="modal-plan-badge" style="background:rgba(99,102,241,.15);color:{{ $currentPlan['color'] }};font-weight:700;">
            <i class="fas {{ $currentPlan['icon'] }} me-1"></i>{{ $currentPlan['label'] }}
          </span>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
          <div class="col-4">
            <div class="text-center p-2 rounded-3" style="background:var(--bs-body-bg);border:1px solid var(--wb-border);">
              <div class="fw-bold" id="stat-workspaces">1</div>
              <div class="small text-body-secondary">Workspaces</div>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded-3" style="background:var(--bs-body-bg);border:1px solid var(--wb-border);">
              <div class="fw-bold" id="stat-tabs">0</div>
              <div class="small text-body-secondary">Tabs</div>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded-3" style="background:var(--bs-body-bg);border:1px solid var(--wb-border);">
              <div class="fw-bold" id="stat-folders">0</div>
              <div class="small text-body-secondary">Folders</div>
            </div>
          </div>
        </div>

        <!-- Edit Name -->
        <div class="mb-3">
          <label class="form-label small fw-semibold">Display Name</label>
          <input type="text" id="profile-name-input" class="form-control" value="{{ auth()->user()->name }}" placeholder="Your name">
        </div>

        <!-- Plan Info -->
        <div class="p-3 rounded-3 mb-4" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <div class="small fw-bold text-uppercase" style="letter-spacing:0.5px; color:var(--wb-primary);">{{ $currentPlan['label'] }}</div>
            </div>
            @if(auth()->user()->plan !== 'business')
              <a href="{{ route('pricing') }}" class="btn btn-primary btn-sm fw-semibold px-3 py-1" style="font-size:0.75rem;">Upgrade</a>
            @endif
          </div>
          
          <div class="small text-body-secondary mb-1">
            <i class="far fa-calendar-check me-1"></i> 
            @if(auth()->user()->plan === 'free')
              Lifetime access
            @else
              @if(isset($latestSubscription))
                Duration: <strong>{{ $latestSubscription->metadata['duration'] ?? '1 Month' }}</strong>
              @else
                Active Subscription
              @endif
            @endif
          </div>

          @if(auth()->user()->plan_expiry_at)
          <div class="small {{ auth()->user()->isPlanExpired() ? 'text-danger' : 'text-body-secondary' }}">
            <i class="far fa-clock me-1"></i> 
            {{ auth()->user()->isPlanExpired() ? 'Expired on:' : 'Expires on:' }} 
            <strong>{{ auth()->user()->plan_expiry_at->format('d M, Y | h:i A') }}</strong>
          </div>
          @endif
        </div>

        <hr class="opacity-10 my-4">

        <!-- Danger Zone -->
        <div class="mb-2">
          <label class="form-label small fw-bold text-danger">Danger Zone</label>
          <div class="d-flex flex-column gap-2">
            <button type="button" id="wb-deactivate-btn" class="btn btn-sm btn-outline-warning text-start">
              <i class="fas fa-pause-circle me-2"></i>Deactivate Account
              <div class="x-small opacity-75 ms-4">Temporarily hide your profile and data.</div>
            </button>
            <button type="button" id="wb-delete-btn" class="btn btn-sm btn-outline-danger text-start">
              <i class="fas fa-trash-alt me-2"></i>Delete Permanently
              <div class="x-small opacity-75 ms-4">Delete all workspaces, tabs, and notes. Irreversible.</div>
            </button>
          </div>
        </div>

      </div>
      <div class="modal-footer border-top-0 pt-0 justify-content-between">
        <form action="{{ route('logout') }}" method="POST" id="logout-form">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-arrow-right-from-bracket me-1"></i>Log Out
          </button>
        </form>
        <button type="button" class="btn btn-primary fw-semibold px-4" id="wb-save-profile">Save Changes</button>
      </div>
    </div>
  </div>
</div>

<!-- Hidden Forms -->
<form id="deactivate-form" action="{{ route('account.deactivate') }}" method="POST" style="display: none;">@csrf</form>
<form id="delete-form" action="{{ route('account.delete') }}" method="POST" style="display: none;">@csrf @method('DELETE')</form>

<script>
  window.DB_STATE = {
      collections: @json($collections),
      userPlan: "{{ auth()->user()->plan }}",
      planStatus: "{{ auth()->user()->plan_status }}",
      username: "{{ auth()->user()->name }}"
  };
</script>
</body>
</html>
