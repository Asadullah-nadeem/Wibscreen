/* =====================================================
   Wibscreen — app.js
   Responsive Edition — jQuery interaction layer
   ===================================================== */
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
  let state = {
    collections:       [],
    websites:          [],
    activeTabId:       null,
    currentCollection: 'all',
    query:             ''
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

  let renameTargetId = null;

  /* ─── Boot ───────────────────────────────────────── */
  boot();

  function boot () {
    loadState();
    applyTheme(settings.theme, false);          // silent (no save)
    setSortUI(settings.sortBy);
    bindEvents();
    render();
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
    $('#wb-theme-icon').attr('class', `fas fa-${isDay ? 'sun' : 'moon'} me-2`);
    $('#wb-theme-label').text(isDay ? 'Day Mode' : 'Night Mode');
    if (save) saveSettings();
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
      const site = findSite(state.activeTabId);
      if (site) window.open(site.url, '_blank', 'noopener');
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
      renameTargetId = $(this).data('id');
      const col = findCol(renameTargetId);
      if (col) { $('#wb-rename-input').val(col.name); modalRename.show(); }
    });

    $(document).on('click', '.wb-delete-btn', function (e) {
      e.preventDefault(); e.stopPropagation();
      const id  = $(this).data('id');
      const col = findCol(id);
      if (col && confirm(`Delete "${col.name}" and all its tabs?`)) deleteCollection(id);
    });

    /* ── Card click → browser ── */
    $dashboard.on('click', '.wb-card', function () {
      openInBrowser($(this).data('id'));
    });

    /* ── Close tab ── */
    $(document).on('click', '.wb-card-close', function (e) {
      e.preventDefault(); e.stopPropagation();
      deleteWebsite($(this).data('id'));
    });

    /* ── New folder ── */
    $('#wb-new-folder-btn').on('click', () => {
      $('#wb-folder-input').val('');
      modalNewFolder.show();
    });

    $('#wb-confirm-folder').on('click', handleCreateFolder);

    /* ── Rename ── */
    $('#wb-confirm-rename').on('click', handleRenameFolder);

    /* ── Add tab ── */
    $('#wb-add-tab-btn').on('click', () => {
      $('#wb-url-input, #wb-name-input').val('');
      modalAddTab.show();
      setTimeout(() => $('#wb-url-input').focus(), 350);
    });

    $('#wb-confirm-tab').on('click', handleAddTab);

    $('#wb-url-input').on('keydown', function (e) {
      if (e.key === 'Enter') handleAddTab();
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

    const cols = isAll ? sortedCols() : sortedCols().filter(c => c.id === state.currentCollection);

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

      if (sites.length > 0) {
        sites.forEach(site => $grid.append(cardHTML(site)));
      } else {
        $grid.append(emptyColHTML());
      }

      $dashboard.append($section);
    });

    /* Lazy-load iframes */
    lazyObserver.disconnect();
    $dashboard.find('.wb-card').each(function () { lazyObserver.observe(this); });
  }

  function cardHTML (s) {
    const fav   = `https://www.google.com/s2/favicons?sz=64&domain=${hostname(s.url)}`;
    const thumb = `https://image.thum.io/get/width/400/crop/280/noanimate/${encodeURIComponent(s.url)}`;
    const cls   = s.id === state.activeTabId ? ' active-tab' : '';
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
          <div class="wb-thumb-skeleton"></div>
          <img class="wb-thumb-img"
               src="${thumb}"
               alt="${escHtml(s.title)} preview"
               loading="lazy"
               onload="this.classList.add('loaded');this.previousElementSibling.style.display='none';"
               onerror="this.style.display='none';this.previousElementSibling.innerHTML='<i class=\'fas fa-globe\'></i><span>${escHtml(hostname(s.url))}</span>';this.previousElementSibling.classList.add('wb-thumb-fallback');">
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
  function handleCreateFolder () {
    const name = $('#wb-folder-input').val().trim();
    if (!name) { $('#wb-folder-input').focus(); return; }
    state.collections.push({ id: 'col-' + Date.now(), name, icon: 'fas fa-folder' });
    modalNewFolder.hide();
    saveState(); render();
  }

  function handleRenameFolder () {
    const name = $('#wb-rename-input').val().trim();
    if (!name || !renameTargetId) return;
    const col = findCol(renameTargetId);
    if (col) { col.name = name; saveState(); render(); }
    modalRename.hide();
  }

  function deleteCollection (id) {
    state.collections = state.collections.filter(c => c.id !== id);
    state.websites    = state.websites.filter(w => w.collectionId !== id);
    if (state.currentCollection === id) state.currentCollection = 'all';
    saveState(); render();
  }

  function handleAddTab () {
    let url  = $('#wb-url-input').val().trim();
    let name = $('#wb-name-input').val().trim();
    if (!url) { $('#wb-url-input').focus(); return; }
    if (!/^https?:\/\//i.test(url)) url = 'https://' + url;

    const id   = 'site-' + Date.now();
    const site = {
      id,
      url,
      originalUrl: url,
      title:       name || titleFromUrl(url),
      collectionId: $colSelect.val() || (state.collections[0]?.id ?? 'work')
    };

    state.websites.push(site);
    mountIframe(site);
    openInBrowser(id);
    modalAddTab.hide();
    saveState(); render();
  }

  function openInBrowser (id) {
    state.activeTabId = id;
    const site = findSite(id);
    if (!site) return;

    // Show browser panel
    $iframeLayer.addClass('active');
    $('#wb-iframe-blocked').removeClass('show');
    $iframeContent.find('.wb-tab-iframe').removeClass('active');

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
    state.websites = state.websites.filter(s => s.id !== id);
    $(`#iframe-${id}`).remove();
    if (state.activeTabId === id) {
      state.activeTabId = state.websites[0]?.id || null;
      if (!state.activeTabId) { $iframeLayer.removeClass('active'); $urlDisplay.val(''); }
    }
    saveState(); render();
    if (state.activeTabId) openInBrowser(state.activeTabId);
  }

  function mountIframe (site) {
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
        DB_STATE.collections.forEach(c => {
            if (c.tabs) {
                c.tabs.forEach(t => {
                    state.websites.push({
                        id: t.id,
                        url: t.url,
                        title: t.title,
                        collectionId: c.id
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

    if (state.activeTabId) {
      $iframeLayer.addClass('active');
      $(`#iframe-${state.activeTabId}`).addClass('active');
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
  function findCol  (id)  { return state.collections.find(c => c.id === id); }
  function findSite (id)  { return state.websites.find(s => s.id === id); }
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
