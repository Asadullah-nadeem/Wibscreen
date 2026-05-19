/* =====================================================
   Wibscreen — app.js
   Responsive Edition — jQuery interaction layer
   ===================================================== */
import $ from 'jquery';

$(function () {

  /* ─── Constants ─────────────────────────────────── */
  const STORAGE_SETTINGS = 'wb_settings_v8';
  const STORAGE_STATE    = 'wb_state_v8';

  const DEFAULT_SETTINGS = { theme: 'dark', sortBy: 'newest' };

  const DEFAULT_COLLECTIONS = [
    { id: 'work',         name: 'Work',         icon: 'fas fa-briefcase' },
    { id: 'social',       name: 'Social',       icon: 'fas fa-hashtag' },
    { id: 'professional', name: 'Professional', icon: 'fas fa-user-tie' },
    { id: 'research',     name: 'Research',     icon: 'fas fa-microscope' }
  ];

  /* ─── State ──────────────────────────────────────── */
  let settings = loadJSON(STORAGE_SETTINGS, DEFAULT_SETTINGS);
  
  // Sync with global theme if exists
  const globalTheme = localStorage.getItem('wb-theme');
  if (globalTheme) settings.theme = globalTheme;

  const state = {
    collections: [],
    websites:    [],
    notes:       [],
    activeTabId: null,
    currentCollection: 'all', // 'all' or id
    query: ''
  };

  /* ─── DOM ────────────────────────────────────────── */
  const $html        = $('html');
  const $sidebar     = $('#wb-sidebar');
  const $overlay     = $('#wb-overlay');
  const $navList     = $('#wb-nav-list');
  const $colSelect   = $('#wb-col-select');
  const $dashboard   = $('#wb-dashboard');
  const $dashTitle   = $('#wb-dash-title');
  const $iframeLayer   = $('#wb-iframe-layer');
  const $iframeContent = $('.wb-iframe-content');
  const $urlDisplay    = $('#wb-url-display');
  const $searchInput = $('#wb-search');
  const $tabBadge    = $('#wb-tab-badge');
  const $statusText  = $('#wb-status-text');

  /* ─── Bootstrap Modals ───────────────────────────── */
  const modalAddTab    = bsModal('wb-modal-tab');
  const modalNewFolder = bsModal('wb-modal-folder');
  const modalRename    = bsModal('wb-modal-rename');
  const modalSettings  = bsModal('wb-modal-settings');
  const modalProfile   = bsModal('wb-modal-profile');

  let renameTargetId = null;
  let isSubmittingTab = false;
  let isSubmittingFolder = false;

  /* ─── Boot ───────────────────────────────────────── */
  $(function() {
    boot();
  });

  function boot () {
    // CSRF Setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function(xhr, status, error) {
            if (xhr.status === 419) {
                // CSRF Token Expired - Try to refresh silently
                console.log('CSRF Token expired. Attempting refresh...');
                $.get('/refresh-csrf').done(function(data) {
                    if (data.token) {
                        // Update meta tag and AJAX setup for future requests
                        $('meta[name="csrf-token"]').attr('content', data.token);
                        $.ajaxSetup({
                            headers: { 'X-CSRF-TOKEN': data.token }
                        });
                        console.log('CSRF Token refreshed successfully.');
                    } else {
                        window.location.reload();
                    }
                }).fail(function() {
                    window.location.reload();
                });
            }
        }
    });

    loadState();
    applyTheme(settings.theme, false);          // silent (no save)
    setSortUI(settings.sortBy);
    bindEvents();
    render();

    // Usage Tracking (150 hours limit)
    setInterval(() => {
        $.post('/track-usage', { _token: $('meta[name="csrf-token"]').attr('content') }).done(data => {
            if (data.exceeded) {
                alert("You have reached your 150-hour monthly limit. Please upgrade to Pro for unlimited access.");
                window.location.href = "/pricing";
            }
        });
    }, 60000); // 1 minute
  }

  /* ═══════════════════════════════════════════════════
     RESPONSIVE SIDEBAR (Mobile)
  ═══════════════════════════════════════════════════ */
  function openSidebar () {
    $sidebar.addClass('open');
    $overlay.addClass('show');
    $('body').css('overflow', 'hidden');  // prevent background scroll
  }

  function closeSidebar () {
    $sidebar.removeClass('open');
    $overlay.removeClass('show');
    $('body').css('overflow', '');
  }

  /* ═══════════════════════════════════════════════════
     THEME
  ═══════════════════════════════════════════════════ */
  function applyTheme (theme, save = true) {
    settings.theme = theme;
    $html.attr('data-bs-theme', theme);
    const isDay = theme === 'light';
    $('#wb-theme-icon').attr('class', `fas fa-${isDay ? 'sun' : 'moon'}`);
    $('#wb-theme-label').text(isDay ? 'Day Mode' : 'Night Mode');
    if (save) {
      saveSettings();
      localStorage.setItem('wb-theme', theme);
    }
  }

  /* ═══════════════════════════════════════════════════
     SORT
  ═══════════════════════════════════════════════════ */
  function setSortUI (val) {
    $('#wb-sort-newest, #wb-sort-alpha').removeClass('active');
    $(`#wb-sort-${val === 'alpha' ? 'alpha' : 'newest'}`).addClass('active');
  }

  function setSortBy (val) {
    settings.sortBy = val;
    setSortUI(val);
    saveSettings();
    render();
  }

  /* ═══════════════════════════════════════════════════
     EVENTS
  ═══════════════════════════════════════════════════ */
  function bindEvents () {

    /* ── Sidebar toggle (mobile) ── */
    $('#wb-hamburger').on('click', openSidebar);
    $('#wb-sidebar-close').on('click', closeSidebar);
    $overlay.on('click', closeSidebar);

    /* ── Theme ── */
    $('#wb-theme-btn').on('click', () =>
      applyTheme(settings.theme === 'dark' ? 'light' : 'dark')
    );

    /* ── Settings ── */
    $('#wb-settings-btn').on('click', () => modalSettings.show());

    /* ── Linux VM click ── */
    $('#wb-linux-btn').on('click', function(e) {
      e.preventDefault();
      openLinuxTerminal();
    });

    /* ── x86 Emulator click ── */
    $('#wb-emulator-btn').on('click', function(e) {
      e.preventDefault();
      openX86Emulator();
    });

    /* ── Browser toolbar ── */
    $('#wb-browser-back, #wb-browser-close').on('click', showDashboard);

    $('#wb-browser-refresh').on('click', () => {
      if (state.activeTabId) {
        const $f = $(`#iframe-${state.activeTabId}`);
        if ($f.length) {
          showLoadBar();
          try { $f[0].contentWindow.location.reload(); } catch (_) {}
        }
      }
    });

    $('#wb-browser-newtab, #wb-open-newtab-btn').on('click', () => {
      if (state.activeTabId === 'linux-vm') {
        const terminalUrl = window.location.protocol + "//" + window.location.hostname + ":7681/";
        window.open(terminalUrl, '_blank', 'noopener');
        return;
      }
      const site = findSite(state.activeTabId);
      if (site) {
        if (site.url.startsWith('cli://')) {
          const iframeSrc = $(`#iframe-${state.activeTabId}`).attr('src');
          if (iframeSrc) {
            window.open(iframeSrc, '_blank', 'noopener');
          } else {
            $.getJSON('/terminal-token').done(function(response) {
              const urlObj = new URL(site.url.replace('cli://', 'http://'));
              const dnsOption = urlObj.searchParams.get('dns_option') || 'local';
              const nameserver = urlObj.searchParams.get('nameserver') || urlObj.hostname;
              const terminalUrl = window.location.protocol + "//" + window.location.host + "/" + response.token + "?nameserver=" + encodeURIComponent(nameserver) + "&dns_option=" + encodeURIComponent(dnsOption);
              window.open(terminalUrl, '_blank', 'noopener');
            });
          }
        } else {
          window.open(site.url, '_blank', 'noopener');
        }
      }
    });

    /* ── Home ── */
    $('#wb-home-btn').on('click', () => {
      state.currentCollection = 'all';
      showDashboard();
      if (window.innerWidth < 768) closeSidebar();
    });

    /* ── Search ── */
    $searchInput.on('input', debounce(function () {
      state.query = $(this).val().toLowerCase().trim();
      renderDashboard();
    }, 280));

    /* ── Nav links (delegated) ── */
    $navList.on('click', '.wb-nav-link', function (e) {
      e.preventDefault();
      state.currentCollection = $(this).data('id');
      showDashboard();
      if (window.innerWidth < 768) closeSidebar();
    });

    /* ── Edit / Delete (global delegation — works on dynamic content) ── */
    $(document).on('click', '.wb-edit-btn', function (e) {
      e.preventDefault(); e.stopPropagation();
      isSubmittingFolder = false;
      renameTargetId = $(this).data('id');
      const col = findCol(renameTargetId);
      if (col) { $('#wb-rename-input').val(col.name); modalRename.show(); }
    });

    $(document).on('click', '.wb-delete-btn', function (e) {
      e.preventDefault(); e.stopPropagation();
      const id  = $(this).data('id');
      const col = findCol(id);
      if (col && confirm(`Delete "${col.name}" and all its tabs/notes?`)) deleteCollection(id);
    });

    /* ── Add Note ── */
    $(document).on('click', '.wb-add-note-btn', function (e) {
      e.preventDefault(); e.stopPropagation();
      const colId = $(this).data('id');
      const content = prompt("Enter your note content:");
      if (!content) return;
      
      $.post('/notes', {
        _token: $('meta[name="csrf-token"]').attr('content'),
        collection_id: colId,
        title: "Note",
        content: content
      }).done(note => {
        state.notes.push({
            id: note.id,
            collectionId: note.collection_id,
            title: note.title,
            content: note.content,
            color: note.color
        });
        render();
      });
    });

    /* ── Delete Note ── */
    $dashboard.on('click', '.delete-note-btn', function (e) {
      e.preventDefault(); e.stopPropagation();
      const id = $(this).data('id');
      if (confirm("Delete this note?")) {
        $.ajax({
          url: `/notes/${id}`,
          method: 'DELETE',
          data: { _token: $('meta[name="csrf-token"]').attr('content') }
        }).done(() => {
          state.notes = state.notes.filter(n => n.id != id);
          render();
        });
      }
    });

    /* ── Card click → browser ── */
    $dashboard.on('click', '.wb-card', function (e) {
      if ($(e.target).closest('.wb-card-close').length) {
        return;
      }
      openInBrowser($(this).data('id'));
    });

    /* ── Close tab ── */
    $dashboard.on('click', '.wb-card-close', function (e) {
      e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
      deleteWebsite($(this).data('id'));
    });

    /* ── New folder ── */
    $('#wb-new-folder-btn').on('click', () => {
      isSubmittingFolder = false;
      $('#wb-folder-input').val('');
      modalNewFolder.show();
    });

    $('#wb-confirm-folder').on('click', handleCreateFolder);

    /* ── Rename ── */
    $('#wb-confirm-rename').on('click', handleRenameFolder);

    /* ── Add tab ── */
    let currentAddTabMode = 'gui';

    $('#wb-tab-mode-gui').on('click', function() {
      currentAddTabMode = 'gui';
      $(this).addClass('active');
      $('#wb-tab-mode-cli').removeClass('active');
      $('#wb-gui-fields').show();
      $('#wb-cli-fields').hide();
      setTimeout(() => $('#wb-url-input').focus(), 50);
    });

    $('#wb-tab-mode-cli').on('click', function() {
      currentAddTabMode = 'cli';
      $(this).addClass('active');
      $('#wb-tab-mode-gui').removeClass('active');
      $('#wb-gui-fields').hide();
      $('#wb-cli-fields').show();
      setTimeout(() => $('#wb-cli-name-input').focus(), 50);
    });

    $('#wb-cli-dns-select').on('change', function() {
      if ($(this).val() === 'custom') {
        $('#wb-cli-custom-ns-group').slideDown(200);
        setTimeout(() => $('#wb-cli-ns-input').focus(), 250);
      } else {
        $('#wb-cli-custom-ns-group').slideUp(200);
      }
    });

    $('#wb-add-tab-btn').on('click', () => {
      isSubmittingTab = false;
      $('#wb-url-input, #wb-name-input, #wb-cli-name-input, #wb-cli-ns-input').val('');
      $('#wb-cli-dns-select').val('local');
      $('#wb-cli-custom-ns-group').hide();
      $('#wb-tab-mode-gui').trigger('click');
      modalAddTab.show();
    });

    $('#wb-confirm-tab').on('click', () => handleAddTab(currentAddTabMode));

    $('#wb-url-input, #wb-cli-ns-input, #wb-cli-name-input').on('keydown', function (e) {
      if (e.key === 'Enter') handleAddTab(currentAddTabMode);
    });

    /* ── Sort ── */
    $('#wb-sort-newest').on('click', () => setSortBy('newest'));
    $('#wb-sort-alpha').on('click',  () => setSortBy('alpha'));

    /* ── Refresh (topbar) ── */
    $('#wb-refresh-btn').on('click', () => $('#wb-browser-refresh').trigger('click'));

    /* ── Close sidebar on resize to desktop ── */
    $(window).on('resize', debounce(() => {
      if (window.innerWidth >= 768) closeSidebar();
    }, 200));

    /* ── Swipe to close sidebar (touch) ── */
    let touchStartX = 0;
    document.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
    document.addEventListener('touchend', e => {
      const dx = e.changedTouches[0].screenX - touchStartX;
      if (dx < -60 && $sidebar.hasClass('open')) closeSidebar();
    }, { passive: true });

    /* ── Settings customizer sliders ── */
    const _root = document.documentElement;
    
    $('#wb-accent-color').on('input', function () {
      _root.style.setProperty('--wb-primary', this.value);
    });
    
    $('#wb-card-radius').on('input', function () {
      _root.style.setProperty('--wb-card-radius', this.value + 'px');
      $('#wb-radius-val').text(this.value + 'px');
    });
    
    $('#wb-sidebar-w').on('input', function () {
      _root.style.setProperty('--wb-sidebar-w', this.value + 'px');
      $('#wb-sw-val').text(this.value + 'px');
    });
    
    $('#wb-settings-theme-btn').on('click', function () {
      $('#wb-theme-btn').trigger('click');
    });

    /* ── Profile Logic ── */
    $('#wb-profile-btn').on('click', function (e) {
      e.preventDefault();
      modalProfile.show();
    });

    $('#wb-save-profile').on('click', function () {
      modalProfile.hide();
    });

    $('#wb-deactivate-btn').on('click', function () {
      if (confirm("Are you sure you want to deactivate your account? You will be logged out and your data will be hidden until you contact support to reactivate.")) {
        document.getElementById('deactivate-form').submit();
      }
    });

    $('#wb-delete-btn').on('click', function () {
      if (confirm("🚨 WARNING: PERMANENT DELETION\n\nThis will permanently delete your account and ALL your data (Workspaces, Tabs, and Notes). This action is IRREVERSIBLE.\n\nType 'DELETE' to confirm:")) {
        const confirmation = prompt("Please type 'DELETE' to confirm permanent account removal:");
        if (confirmation === 'DELETE') {
          document.getElementById('delete-form').submit();
        } else {
          alert("Deletion cancelled. Text did not match.");
        }
      }
    });
  }

  /* ═══════════════════════════════════════════════════
     RENDER
  ═══════════════════════════════════════════════════ */
  function render () {
    renderSidebar();
    renderDashboard();
    updateBottomBar();
  }

  /* ── Sidebar ─────────────────────────────────────── */
  function renderSidebar () {
    $navList.empty();
    $colSelect.empty();

    $navList.append(makeNavItem('all', 'All Workspace', 'fas fa-shapes', state.currentCollection === 'all', true));

    sortedCols().forEach(col => {
      $navList.append(makeNavItem(col.id, col.name, col.icon, state.currentCollection === col.id));
      $colSelect.append(`<option value="${col.id}">${col.name}</option>`);
    });
  }

  function makeNavItem (id, name, icon, active, isSystem = false) {
    const actions = isSystem ? '' : `
      <div class="wb-nav-actions">
        <button class="wb-icon-btn wb-edit-btn" data-id="${id}" title="Rename" aria-label="Rename ${name}">
          <i class="fas fa-pen"></i>
        </button>
        <button class="wb-icon-btn wb-delete-btn delete" data-id="${id}" title="Delete" aria-label="Delete ${name}">
          <i class="fas fa-trash"></i>
        </button>
      </div>`;

    return $(`
      <li class="wb-nav-item">
        <a href="#" class="wb-nav-link ${active ? 'active' : ''}" data-id="${id}" role="button">
          <span class="wb-nav-icon"><i class="${icon}"></i></span>
          <span class="wb-nav-label">${name}</span>
          ${actions}
        </a>
      </li>
    `);
  }

  /* ── Dashboard ───────────────────────────────────── */
  function renderDashboard () {
    $dashboard.empty();

    const isAll = state.currentCollection === 'all';
    $dashTitle.text(isAll ? 'Explore Workspace' : getColName(state.currentCollection));

    const cols = isAll ? sortedCols() : sortedCols().filter(c => c.id == state.currentCollection);

    if (!cols.length) {
      $dashboard.append(emptyPageHTML('No collections yet', 'Click "+ New Folder" to create your first collection.'));
      return;
    }

    cols.forEach((col, i) => {
      const sites = sitesForCol(col.id);
      if (isAll && state.query && !sites.length && !col.name.toLowerCase().includes(state.query)) return;

      const $section = $(`
        <div class="wb-section" style="animation-delay:${i * 0.06}s;">
          <div class="wb-section-header">
            <i class="${col.icon} text-primary"></i>
            <h2 class="wb-section-title">${escHtml(col.name)}</h2>
            <span class="wb-section-badge badge bg-secondary-subtle text-secondary-emphasis">${sites.length}</span>
            <div class="wb-section-actions">
              <button class="wb-section-action-btn wb-add-note-btn" data-id="${col.id}" title="Add Note" aria-label="Add Note">
                <i class="fas fa-sticky-note"></i>
              </button>
              <button class="wb-section-action-btn wb-edit-btn"   data-id="${col.id}" title="Rename" aria-label="Rename">
                <i class="fas fa-pen"></i>
              </button>
              <button class="wb-section-action-btn wb-delete-btn delete" data-id="${col.id}" title="Delete" aria-label="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <div class="wb-card-grid"></div>
        </div>
      `);

      const $grid = $section.find('.wb-card-grid');

      // Render Tabs
      if (sites.length > 0) {
        sites.forEach(site => $grid.append(cardHTML(site)));
      }

      // Render Notes
      const notes = notesForCol(col.id);
      if (notes.length > 0) {
        notes.forEach(note => $grid.append(noteHTML(note)));
      }

      if (sites.length === 0 && notes.length === 0) {
        $grid.append(emptyColHTML());
      }

      $dashboard.append($section);
    });

    /* Lazy-load iframes */
    lazyObserver.disconnect();
    $dashboard.find('.wb-card').each(function () { lazyObserver.observe(this); });
  }

  function notesForCol (colId) {
    return state.notes.filter(n => n.collectionId == colId);
  }

  function noteHTML (n) {
    return `
      <div class="wb-card note-card" style="border-top: 4px solid ${n.color || '#6366f1'}; min-height: 180px;">
        <div class="wb-card-header border-0 pb-0">
          <span class="wb-card-title fw-bold">${escHtml(n.title)}</span>
          <button class="wb-card-close delete-note-btn" data-id="${n.id}" aria-label="Delete Note">
            <i class="fas fa-xmark"></i>
          </button>
        </div>
        <div class="p-3 pt-2 small text-body-secondary" style="white-space: pre-wrap;">${escHtml(n.content)}</div>
      </div>`;
  }

  function cardHTML (s) {
    const isCli = s.url.startsWith('cli://');
    const fav = isCli 
      ? 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22%3E%3Cpath fill=%22%236366f1%22 d=%22M20,19H4A2,2 0 0,1 2,17V7A2,2 0 0,1 4,5H20A2,2 0 0,1 22,7V17A2,2 0 0,1 20,19M4,7V17H20V7H4M6,9H11V11H6V9M6,13H15V15H6V13Z%22/%3E%3C/svg%3E'
      : `https://www.google.com/s2/favicons?sz=64&domain=${hostname(s.url)}`;

    const cls = s.id == state.activeTabId ? ' active-tab' : '';
    
    let previewHTML = '';
    if (isCli) {
      // Parse nameserver and dns_option for clean display
      let displayNs = 'localhost';
      try {
        const urlObj = new URL(s.url.replace('cli://', 'http://'));
        displayNs = urlObj.searchParams.get('nameserver') || urlObj.hostname;
      } catch (_) {}

      previewHTML = `
        <div class="wb-thumb-fallback wb-cli-preview-box" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0b0f19; color: #38bdf8; font-family: monospace; width: 100%; height: 100%;">
          <i class="fas fa-terminal mb-2" style="font-size: 1.8rem; color: var(--wb-primary);"></i>
          <span style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; letter-spacing: 0.5px;">CLI TERMINAL</span>
          <span class="text-truncate px-2" style="font-size: 0.65rem; color: #475569; max-width: 100%;">${escHtml(displayNs)}</span>
        </div>`;
    } else {
      const thumb = `https://image.thum.io/get/width/400/crop/280/noanimate/${encodeURIComponent(s.url)}`;
      previewHTML = `
        <div class="wb-thumb-skeleton"></div>
        <img class="wb-thumb-img"
             src="${thumb}"
             alt="${escHtml(s.title)} preview"
             loading="lazy"
             onload="this.classList.add('loaded');this.previousElementSibling.style.display='none';"
             onerror="this.style.display='none';this.previousElementSibling.innerHTML='<i class=\'fas fa-globe\'></i><span>${escHtml(hostname(s.url))}</span>';this.previousElementSibling.classList.add('wb-thumb-fallback');">`;
    }

    return `
      <div class="wb-card${cls}" data-id="${s.id}" data-url="${s.url}" role="button" tabindex="0" aria-label="${escHtml(s.title)}">
        <div class="wb-card-header">
          <img src="${fav}" class="wb-card-favicon" loading="lazy"
               onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 18 18%22%3E%3Crect width=%2218%22 height=%2218%22 rx=%224%22 fill=%22%236366f1%22/%3E%3C/svg%3E'">
          <span class="wb-card-title">${escHtml(s.title)}</span>
          <button class="wb-card-close wb-card-close-btn" data-id="${s.id}" aria-label="Close ${escHtml(s.title)}">
            <i class="fas fa-xmark"></i>
          </button>
        </div>
        <div class="wb-card-preview">
          ${previewHTML}
        </div>
      </div>`;
  }

  function emptyColHTML () {
    return `
      <div class="wb-empty" style="grid-column: 1/-1;">
        <div class="wb-empty-icon"><i class="fas fa-layer-group"></i></div>
        <p class="wb-empty-text">No websites yet</p>
        <p class="wb-empty-sub">Tap the <b>+</b> button to add one.</p>
      </div>`;
  }

  function emptyPageHTML (title, sub) {
    return `
      <div class="d-flex align-items-center justify-content-center" style="min-height:55vh;">
        <div class="text-center px-4">
          <div class="mb-3 opacity-15" style="font-size:3rem;"><i class="fas fa-compass"></i></div>
          <h5 class="fw-bold">${title}</h5>
          <p class="text-body-secondary small">${sub}</p>
        </div>
      </div>`;
  }

  /* ─── Lazy Load (screenshots load natively via img loading=lazy) ─── */
  const lazyObserver = new IntersectionObserver(() => {}, {});

  /* ═══════════════════════════════════════════════════
     ACTIONS
  ═══════════════════════════════════════════════════ */
  function checkLimit (type) {
    const plan = (typeof DB_STATE !== 'undefined') ? DB_STATE.userPlan : 'free';
    const status = (typeof DB_STATE !== 'undefined') ? DB_STATE.planStatus : 'active';
    
    // If business plan is pending, treat as free for limits
    const effectivePlan = (plan === 'business' && status === 'pending') ? 'free' : plan;
    
    if (type === 'workspace') {
      const count = state.collections.length;
      const limit = effectivePlan === 'pro' ? 10 : (effectivePlan === 'business' ? 999 : 1);
      if (count >= limit) {
        alert(`Limit Reached: Your ${plan} plan allows only ${limit} workspace(s). Please upgrade for more.`);
        return false;
      }
    }
    
    if (type === 'email') {
      const limit = (plan === 'pro' || plan === 'business') ? 99999 : 10;
      // return false if count >= limit (logic placeholder)
    }

    if (type === 'bot') {
      const limit = (plan === 'pro' || plan === 'business') ? 99999 : 10;
      // return false if count >= limit (logic placeholder)
    }

    return true;
  }

  function handleCreateFolder () {
    if (isSubmittingFolder) return;
    const name = $('#wb-folder-input').val().trim();
    if (!name) { $('#wb-folder-input').focus(); return; }
    
    // Check Plan Limit
    if (!checkLimit('workspace')) return;

    isSubmittingFolder = true;
    const $btn = $('#wb-confirm-folder');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...');

    $.post('/collections', { 
        _token: $('meta[name="csrf-token"]').attr('content'),
        name: name, 
        icon: 'fas fa-folder' 
    })
      .done(function(data) {
        state.collections.push({ id: data.id, name: data.name, icon: data.icon });
        modalNewFolder.hide();
        saveState(); render();
      })
      .fail(function(xhr) {
        alert(xhr.responseJSON?.error || 'Failed to create collection.');
      })
      .always(function() {
        isSubmittingFolder = false;
        $btn.prop('disabled', false).html(originalHtml);
      });
  }

  function handleRenameFolder () {
    if (isSubmittingFolder) return;
    const name = $('#wb-rename-input').val().trim();
    if (!name || !renameTargetId) return;
    
    let cleanId = renameTargetId;
    if (typeof cleanId === 'string' && cleanId.startsWith('col-')) {
        cleanId = cleanId.replace('col-', '');
    }

    isSubmittingFolder = true;
    const $btn = $('#wb-confirm-rename');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

    $.post(`/collections/${cleanId}/rename`, { 
        _token: $('meta[name="csrf-token"]').attr('content'),
        name: name 
    })
      .done(function(data) {
        const col = findCol(renameTargetId);
        if (col) { col.name = name; saveState(); render(); }
        modalRename.hide();
      })
      .fail(function() {
        alert('Failed to rename collection.');
      })
      .always(function() {
        isSubmittingFolder = false;
        $btn.prop('disabled', false).html(originalHtml);
      });
  }

  function deleteCollection (id) {
    let cleanId = id;
    if (typeof cleanId === 'string' && cleanId.startsWith('col-')) {
        cleanId = cleanId.replace('col-', '');
    }

    $.ajax({
      url: `/collections/${cleanId}`,
      method: 'DELETE',
      data: {
          _token: $('meta[name="csrf-token"]').attr('content')
      }
    })
    .done(function() {
      state.collections = state.collections.filter(c => c.id != id);
      state.websites    = state.websites.filter(w => w.collectionId != id);
      if (state.currentCollection == id) state.currentCollection = 'all';
      saveState(); render();
    })
    .fail(function() {
      alert('Failed to delete collection.');
    });
  }

  function openLinuxTerminal() {
    state.activeTabId = 'linux-vm';
    
    // Show browser panel
    $iframeLayer.addClass('active');
    $('#wb-iframe-blocked').removeClass('show');
    $iframeContent.find('.wb-tab-iframe').removeClass('active');
    showLoadBar();

    $.getJSON('/terminal-token')
      .done(function (response) {
        const token = response.token;
        const terminalUrl = window.location.protocol + "//" + window.location.host + "/" + token;
        
        $('#wb-browser-url-text').text("Linux VM console - " + terminalUrl);
        $urlDisplay.val(terminalUrl);

        if ($('#iframe-linux-vm').length === 0) {
          $iframeContent.append(
            `<iframe id="iframe-linux-vm" class="wb-tab-iframe" src="${terminalUrl}"
                     title="Linux VM Console" loading="lazy"></iframe>`
          );
        } else {
          $('#iframe-linux-vm').attr('src', terminalUrl);
        }

        const $frame = $('#iframe-linux-vm');
        $frame.off('load.wb error.wb').on('load.wb', function () {
          doneLoadBar();
        }).on('error.wb', function () {
          doneLoadBar();
          alert('Failed to load Linux VM console.');
        });

        $frame.addClass('active');
        updateBottomBar();
      })
      .fail(function () {
        doneLoadBar();
        alert('Failed to authorize terminal session. Please try logging in again.');
      });
  }

  function openX86Emulator() {
    state.activeTabId = 'x86-emulator';
    
    // Show browser panel
    $iframeLayer.addClass('active');
    $('#wb-iframe-blocked').removeClass('show');
    $iframeContent.find('.wb-tab-iframe').removeClass('active');
    showLoadBar();

    const emulatorUrl = window.location.protocol + "//" + window.location.host + "/linux-vm";
    $('#wb-browser-url-text').text("x86 Emulator (WibOS Custom C/C++ VM) - " + emulatorUrl);
    $urlDisplay.val(emulatorUrl);

    if ($('#iframe-x86-emulator').length === 0) {
      $iframeContent.append(
        `<iframe id="iframe-x86-emulator" class="wb-tab-iframe" src="${emulatorUrl}"
                 title="x86 Emulator" loading="lazy"></iframe>`
      );
    } else {
      const currentSrc = $('#iframe-x86-emulator').attr('src');
      if (currentSrc !== emulatorUrl) {
        $('#iframe-x86-emulator').attr('src', emulatorUrl);
      }
    }

    const $frame = $('#iframe-x86-emulator');
    $frame.off('load.wb error.wb').on('load.wb', function () {
      doneLoadBar();
    }).on('error.wb', function () {
      doneLoadBar();
      alert('Failed to load x86 emulator.');
    });

    $frame.addClass('active');
    updateBottomBar();
  }

  function handleAddTab (mode) {
    if (isSubmittingTab) return;

    let url = '';
    let name = '';
    
    if (mode === 'cli') {
      const dnsOption = $('#wb-cli-dns-select').val();
      let ns = '';
      if (dnsOption === 'local') ns = 'localhost';
      else if (dnsOption === 'google') ns = '8.8.8.8';
      else if (dnsOption === 'cloudflare') ns = '1.1.1.1';
      else {
        ns = $('#wb-cli-ns-input').val().trim();
        if (!ns) { $('#wb-cli-ns-input').focus(); return; }
      }
      url = `cli://${ns}?dns_option=${dnsOption}`;
      
      const displayNameInput = $('#wb-cli-name-input').val().trim();
      name = displayNameInput || (dnsOption === 'local' ? 'Local VM Console' : `Linux VM (${ns})`);
    } else {
      url = $('#wb-url-input').val().trim();
      name = $('#wb-name-input').val().trim();
      if (!url) { $('#wb-url-input').focus(); return; }
      if (!/^https?:\/\//i.test(url)) url = 'https://' + url;
      name = name || titleFromUrl(url);
    }

    let colId = $colSelect.val() || (state.collections[0]?.id ?? null);
    if (!colId) { alert('Please create a workspace first.'); return; }
    
    // Clean ID if it contains 'col-' prefix from old localStorage
    if (typeof colId === 'string' && colId.startsWith('col-')) {
        colId = colId.replace('col-', '');
    }
    
    // Check Tab Limit for this collection
    const plan = (typeof DB_STATE !== 'undefined') ? DB_STATE.userPlan : 'free';
    const status = (typeof DB_STATE !== 'undefined') ? DB_STATE.planStatus : 'active';
    const effectivePlan = (plan === 'business' && status === 'pending') ? 'free' : plan;

    const tabLimit = (effectivePlan === 'pro' || effectivePlan === 'business') ? 99999 : 10;
    const currentTabCount = state.websites.filter(w => w.collectionId == colId).length;
    
    if (currentTabCount >= tabLimit) {
        alert(`Limit Reached: Your ${effectivePlan} plan allows only ${tabLimit} tabs per workspace. Please upgrade for unlimited tabs.`);
        return;
    }

    // Set submitting state and disable button
    isSubmittingTab = true;
    const $btn = $('#wb-confirm-tab');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Adding...');

    $.post('/tabs', { 
      _token: $('meta[name="csrf-token"]').attr('content'),
      collection_id: colId,
      title: name,
      url: url
    })
    .done(function(data) {
      const site = {
        id: data.id,
        url: data.url,
        originalUrl: data.url,
        title: data.title,
        collectionId: data.collection_id
      };

      state.websites.push(site);
      mountIframe(site);
      openInBrowser(site.id);
      modalAddTab.hide();
      saveState(); render();
    })
    .fail(function(xhr) {
      const errorMsg = xhr.responseJSON?.error || (xhr.status === 404 ? 'Workspace not found. Please refresh.' : 'Failed to add tab. Server error.');
      alert(errorMsg);
      isSubmittingTab = false;
      $btn.prop('disabled', false).html(originalHtml);
    });
  }



  function openInBrowser (id) {
    state.activeTabId = id;
    const site = findSite(id);
    if (!site) return;

    // Show browser panel
    $iframeLayer.addClass('active');
    $('#wb-iframe-blocked').removeClass('show');
    $iframeContent.find('.wb-tab-iframe').removeClass('active');

    if (site.url.startsWith('cli://')) {
      let dnsOption = 'local';
      let nameserver = 'localhost';
      try {
        const urlObj = new URL(site.url.replace('cli://', 'http://'));
        dnsOption = urlObj.searchParams.get('dns_option') || 'local';
        nameserver = urlObj.searchParams.get('nameserver') || urlObj.hostname;
      } catch (e) {
        console.error(e);
      }

      // Update browser URL bar
      const displayUrl = `nameserver://${nameserver} (dns: ${dnsOption})`;
      $('#wb-browser-url-text').text("Linux VM console - " + displayUrl);
      $urlDisplay.val(displayUrl);

      // Fetch terminal token and open terminal
      showLoadBar();
      $.getJSON('/terminal-token')
        .done(function (response) {
          const token = response.token;
          const terminalUrl = window.location.protocol + "//" + window.location.host + "/" + token + "?nameserver=" + encodeURIComponent(nameserver) + "&dns_option=" + encodeURIComponent(dnsOption);

          if ($(`#iframe-${id}`).length === 0) {
            $iframeContent.append(
              `<iframe id="iframe-${id}" class="wb-tab-iframe" src="${terminalUrl}"
                       title="${escHtml(site.title)}" loading="lazy"></iframe>`
            );
          } else {
            const currentSrc = $(`#iframe-${id}`).attr('src') || '';
            if (!currentSrc.includes(token)) {
              $(`#iframe-${id}`).attr('src', terminalUrl);
            }
          }

          const $frame = $(`#iframe-${id}`);
          $frame.off('load.wb error.wb').on('load.wb', function () {
            doneLoadBar();
          }).on('error.wb', function () {
            doneLoadBar();
            alert('Failed to load Linux VM console.');
          });

          $frame.addClass('active');
          updateBottomBar();
        })
        .fail(function () {
          doneLoadBar();
          alert('Failed to authorize terminal session. Please try logging in again.');
        });

      saveState(); renderSidebar(); updateBottomBar();
      return;
    }

    // Update browser URL bar
    $('#wb-browser-url-text').text(site.url);
    $urlDisplay.val(site.url);

    // Mount iframe if not already
    mountIframe(site);
    const $frame = $(`#iframe-${id}`);

    // Show load bar
    showLoadBar();

    // Listen for load / block
    $frame.off('load.wb error.wb').on('load.wb', function () {
      doneLoadBar();
      // Check if iframe was blocked (blank srcdoc / 0 width body = blocked)
      try {
        const doc = this.contentDocument || this.contentWindow.document;
        // If same-origin empty page
        if (doc && doc.body && doc.body.innerHTML === '') showBlocked(site);
      } catch (e) {
        // Cross-origin means it LOADED (no error thrown by load event for blocked by CSP)
        doneLoadBar();
      }
    }).on('error.wb', function () {
      doneLoadBar();
      showBlocked(site);
    });

    $frame.addClass('active');

    saveState(); renderSidebar(); updateBottomBar();
  }

  function showBlocked (site) {
    $(`#iframe-${site.id}`).removeClass('active');
    $('#wb-blocked-domain').text(
      `"${hostname(site.url)}" blocks embedding. You can open it directly in a new tab.`
    );
    $('#wb-iframe-blocked').addClass('show');
  }

  function showLoadBar () {
    const $bar = $('#wb-load-bar');
    $bar.removeClass('loading done').css('opacity', 1);
    requestAnimationFrame(() => $bar.addClass('loading'));
  }

  function doneLoadBar () {
    const $bar = $('#wb-load-bar');
    $bar.removeClass('loading').addClass('done');
    setTimeout(() => $bar.removeClass('done'), 800);
  }

  function showDashboard () {
    $iframeLayer.removeClass('active');
    $('#wb-iframe-blocked').removeClass('show');
    state.activeTabId = null;
    $urlDisplay.val('Workspace Ready');
    render();
  }

  function deleteWebsite (id) {
    if (!confirm('Are you sure you want to delete this tab?')) return;
    $.ajax({
      url: `/tabs/${id}`,
      method: 'DELETE',
      data: {
          _token: $('meta[name="csrf-token"]').attr('content')
      }
    })
    .done(function() {
      state.websites = state.websites.filter(s => s.id != id);
      $(`#iframe-${id}`).remove();
      if (state.activeTabId == id) {
        showDashboard();
        saveState();
      } else {
        saveState(); render();
      }
    })
    .fail(function() {
      alert('Failed to delete tab.');
    });
  }

  function mountIframe (site) {
    if (site.url.startsWith('cli://')) return;
    if ($(`#iframe-${site.id}`).length) return;
    $iframeContent.append(
      `<iframe id="iframe-${site.id}" class="wb-tab-iframe" src="${site.url}"
               title="${escHtml(site.title)}" loading="lazy"></iframe>`
    );
  }

  /* ─── Bottom Bar ─────────────────────────────────── */
  function updateBottomBar () {
    const n = state.websites.length;
    $tabBadge.text(n).toggle(n > 0);
    $statusText.text(state.activeTabId ? 'Browsing' : 'Ready');
  }

  /* ═══════════════════════════════════════════════════
     DATA HELPERS
  ═══════════════════════════════════════════════════ */
  function sortedCols () {
    const list = [...state.collections];
    return settings.sortBy === 'alpha'
      ? list.sort((a, b) => a.name.localeCompare(b.name))
      : list;
  }

  function sitesForCol (colId) {
    let sites = state.websites.filter(w => w.collectionId === colId);
    if (state.query) {
      const q = state.query;
      sites = sites.filter(s =>
        s.title.toLowerCase().includes(q) || s.url.toLowerCase().includes(q)
      );
    }
    return settings.sortBy === 'alpha'
      ? sites.sort((a, b) => a.title.localeCompare(b.title))
      : sites;
  }

  /* ═══════════════════════════════════════════════════
     PERSISTENCE
  ═══════════════════════════════════════════════════ */
  function loadState () {
    // Priority: Database (DB_STATE) > LocalStorage > Default
    const saved = loadJSON(STORAGE_STATE, {});
    
    if (typeof DB_STATE !== 'undefined' && DB_STATE.collections.length > 0) {
        state.collections = DB_STATE.collections.map(c => ({
            id: c.id,
            name: c.name,
            icon: c.icon
        }));
        
        state.websites = [];
        state.notes = [];
        DB_STATE.collections.forEach(col => {
            col.tabs.forEach(tab => {
                state.websites.push({
                    id: tab.id,
                    collectionId: col.id,
                    title: tab.title,
                    url: tab.url
                });
            });
            
            // Load Notes
            if (col.notes) {
                col.notes.forEach(note => {
                    state.notes.push({
                        id: note.id,
                        collectionId: col.id,
                        title: note.title,
                        content: note.content,
                        color: note.color
                    });
                });
            }
        });
    } else {
        state.collections = saved.collections?.length ? saved.collections : [...DEFAULT_COLLECTIONS];
        state.websites    = saved.websites    || [];
    }

    state.activeTabId = saved.activeTabId || null;

    state.websites.forEach(mountIframe);

    if (state.activeTabId === 'linux-vm') {
      openLinuxTerminal();
    } else if (state.activeTabId === 'x86-emulator') {
      openX86Emulator();
    } else if (state.activeTabId) {
      const activeSite = findSite(state.activeTabId);
      if (activeSite && activeSite.url.startsWith('cli://')) {
        openInBrowser(state.activeTabId);
      } else {
        $iframeLayer.addClass('active');
        $(`#iframe-${state.activeTabId}`).addClass('active');
      }
    }
  }

  function saveState () {
    localStorage.setItem(STORAGE_STATE, JSON.stringify({
      collections: state.collections,
      websites:    state.websites,
      activeTabId: state.activeTabId
    }));
  }

  function saveSettings () {
    localStorage.setItem(STORAGE_SETTINGS, JSON.stringify(settings));
  }

  /* ═══════════════════════════════════════════════════
     UTILS
  ═══════════════════════════════════════════════════ */
  function bsModal  (id)  { return new bootstrap.Modal(document.getElementById(id)); }
  function findCol  (id)  { return state.collections.find(c => c.id == id); }
  function findSite (id)  { return state.websites.find(s => s.id == id); }
  function getColName (id){ return findCol(id)?.name || 'Collection'; }
  function hostname (url) { try { return new URL(url).hostname; } catch (_) { return ''; } }
  function titleFromUrl (url) {
    try {
      const h = hostname(url).replace(/^www\./, '').split('.')[0];
      return h.charAt(0).toUpperCase() + h.slice(1);
    } catch (_) { return 'Website'; }
  }
  function escHtml (s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function loadJSON (key, def) {
    try { return { ...def, ...JSON.parse(localStorage.getItem(key) || '{}') }; }
    catch (_) { return { ...def }; }
  }
  function debounce (fn, ms) {
    let t;
    return function (...a) { clearTimeout(t); t = setTimeout(() => fn.apply(this, a), ms); };
  }

});
