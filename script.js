$(document).ready(function() {
    // --- Data Layer (State) ---
    let state = {
        collections: [
            { id: 'work', name: 'Work', icon: 'fas fa-briefcase' },
            { id: 'social', name: 'Social', icon: 'fas fa-hashtag' },
            { id: 'tools', name: 'Tools', icon: 'fas fa-wrench' },
            { id: 'personal', name: 'Personal', icon: 'fas fa-user' }
        ],
        websites: [],
        activeTabId: null,
        currentCollectionId: 'all',
        searchQuery: ''
    };

    // --- Selectors ---
    const $collectionsList = $('#collections-list');
    const $collectionSelect = $('#new-collection-select');
    const $tabsGrid = $('#tabs-grid');
    const $tabManager = $('#tab-manager');
    const $iframesContainer = $('#iframes-container');
    const $mainUrlBar = $('#main-url-input');
    const $tabCountBadge = $('#tab-count-badge');
    const $globalSearch = $('#global-search');
    const $addModal = new bootstrap.Modal(document.getElementById('addWebsiteModal'));
    const $contextMenu = $('#context-menu');

    let contextTargetId = null;

    // --- Initialization ---
    init();

    function init() {
        loadFromCache();
        setupInteractions();
        renderSidebar();
        renderDashboard();
        updateUIState();
    }

    // --- jQuery UI Interactions ---

    function setupInteractions() {
        // Search
        $globalSearch.on('input', function() {
            state.searchQuery = $(this).val().toLowerCase();
            renderSidebar();
            renderDashboard();
        });

        // Collection Management
        $('#add-collection-btn').on('click', () => {
            const name = prompt("Enter Folder Name:");
            if (name) {
                const id = 'col-' + Date.now();
                state.collections.push({ id, name, icon: 'fas fa-folder' });
                saveToCache();
                renderSidebar();
                showToast(`Folder "${name}" created`);
            }
        });

        // Website Actions
        $('#add-website-btn').on('click', () => $addModal.show());
        $('#load-website-btn').on('click', handleAddWebsite);

        // Navigation
        $('#tab-switcher-btn, #home-btn').on('click', () => {
            state.currentCollectionId = 'all';
            toggleView(true);
            renderSidebar();
        });
        
        $('#refresh-btn').on('click', refreshBrowser);

        // Context Menu Handlers
        $(document).on('contextmenu', '.nav-link', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (id === 'all') return;
            contextTargetId = id;
            $contextMenu.css({ top: e.pageY, left: e.pageX }).addClass('show');
        });

        $(document).on('click', () => $contextMenu.removeClass('show'));

        $('#ctx-rename').on('click', function(e) {
            e.preventDefault();
            const col = state.collections.find(c => c.id === contextTargetId);
            if (col) {
                const newName = prompt("Rename folder:", col.name);
                if (newName) {
                    col.name = newName;
                    saveToCache();
                    renderSidebar();
                }
            }
        });

        $('#ctx-delete').on('click', function(e) {
            e.preventDefault();
            const col = state.collections.find(c => c.id === contextTargetId);
            if (col && confirm(`Delete "${col.name}" and all its websites?`)) {
                state.websites = state.websites.filter(w => w.collectionId !== col.id);
                state.collections = state.collections.filter(c => c.id !== col.id);
                if (state.currentCollectionId === col.id) state.currentCollectionId = 'all';
                saveToCache();
                renderSidebar();
                renderDashboard();
                updateUIState();
            }
        });
    }

    // --- Dynamic Rendering (jQuery) ---

    function renderSidebar() {
        $collectionsList.empty();
        $collectionSelect.empty().append('<option value="un-categorized">Default</option>');

        // Special "All Workspace" link
        const allActive = state.currentCollectionId === 'all';
        const $allItem = $(`
            <li class="nav-item">
                <a href="#" class="nav-link ${allActive ? 'active' : ''}" data-id="all">
                    <i class="fas fa-grid-2"></i>
                    <span>All Workspace</span>
                </a>
            </li>
        `);
        $allItem.find('a').on('click', (e) => {
            e.preventDefault();
            state.currentCollectionId = 'all';
            renderSidebar();
            toggleView(true);
        });
        $collectionsList.append($allItem);

        state.collections.forEach(col => {
            if (state.searchQuery && !col.name.toLowerCase().includes(state.searchQuery)) return;

            const isActive = state.currentCollectionId === col.id;
            const $item = $(`
                <li class="nav-item">
                    <a href="#" class="nav-link ${isActive ? 'active' : ''}" data-id="${col.id}">
                        <i class="${col.icon}"></i>
                        <span>${col.name}</span>
                    </a>
                </li>
            `);

            $item.find('a').on('click', (e) => {
                e.preventDefault();
                state.currentCollectionId = col.id;
                renderSidebar();
                toggleView(true);
            });

            $collectionsList.append($item);
            $collectionSelect.append(`<option value="${col.id}">${col.name}</option>`);
        });
    }

    function renderDashboard() {
        $tabsGrid.empty();
        $('#current-collection-name').text(state.currentCollectionId === 'all' ? 'My Workspace' : getCollectionName(state.currentCollectionId));

        const filtered = state.websites.filter(w => {
            const matchCol = state.currentCollectionId === 'all' || w.collectionId === state.currentCollectionId;
            const matchSearch = !state.searchQuery || 
                                w.title.toLowerCase().includes(state.searchQuery) || 
                                w.url.toLowerCase().includes(state.searchQuery);
            return matchCol && matchSearch;
        });

        filtered.forEach(site => {
            const isActive = site.id === state.activeTabId;
            const favicon = `https://www.google.com/s2/favicons?sz=64&domain=${new URL(site.url).hostname}`;
            
            const $card = $(`
                <div class="col">
                    <div class="tab-card ${isActive ? 'active' : ''}" data-id="${site.id}">
                        <div class="tab-card-header">
                            <img src="${favicon}" class="tab-favicon" onerror="this.src='https://favicons.githubusercontent.com/github.com'">
                            <span class="tab-card-title">${site.title}</span>
                            <button class="btn btn-link text-muted p-0 ms-auto close-tab-btn" data-id="${site.id}">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                        <div class="tab-card-preview">
                            <div class="preview-iframe-wrapper">
                                <iframe src="${site.url}" frameborder="0"></iframe>
                            </div>
                            <div class="preview-overlay"></div>
                        </div>
                    </div>
                </div>
            `);

            $card.find('.tab-card').on('click', () => loadInBrowser(site.id));
            $card.find('.close-tab-btn').on('click', (e) => {
                e.stopPropagation();
                removeWebsite(site.id);
            });

            $tabsGrid.append($card);
        });

        if (filtered.length === 0 && state.websites.length > 0) {
            $tabsGrid.append(`<div class="col-12 text-center py-5"><p class="text-muted">No websites found in this collection.</p></div>`);
        }
    }

    // --- Browser Layer ---

    function handleAddWebsite() {
        let url = $('#new-url-input').val().trim();
        let name = $('#new-name-input').val().trim();
        let colId = $('#new-collection-select').val();

        if (!url) return;
        if (!/^https?:\/\//i.test(url)) url = 'https://' + url;

        const siteId = 'site-' + Date.now();
        const smartUrl = getSmartUrl(url);

        const newSite = {
            id: siteId,
            url: smartUrl,
            originalUrl: url,
            title: name || extractTitle(url),
            collectionId: colId
        };

        state.websites.push(newSite);
        saveToCache();
        
        createBrowserInstance(newSite);
        loadInBrowser(siteId);
        
        $addModal.hide();
        $('#new-url-input, #new-name-input').val('');
        toggleView(false);
        updateUIState();
    }

    function createBrowserInstance(site) {
        const $iframe = $(`<iframe id="${site.id}-iframe" src="${site.url}" class="browser-tab-iframe"></iframe>`);
        $iframesContainer.append($iframe);
    }

    function loadInBrowser(id) {
        state.activeTabId = id;
        $('.browser-tab-iframe').removeClass('active');
        const site = state.websites.find(s => s.id === id);
        if (site) {
            $(`#${id}-iframe`).addClass('active');
            $mainUrlBar.val(site.originalUrl);
            $('#browser-placeholder').fadeOut(200);
        }
        toggleView(false);
        saveToCache();
    }

    function refreshBrowser() {
        if (state.activeTabId) {
            const $el = $(`#${state.activeTabId}-iframe`);
            $el.attr('src', $el.attr('src'));
            showToast("Refreshing...");
        }
    }

    // --- Technical Helpers ---

    function getSmartUrl(url) {
        const blocks = ['google.com', 'facebook.com', 'twitter.com', 'github.com', 'youtube.com'];
        if (blocks.some(b => url.includes(b))) {
            return `https://corsproxy.io/?${encodeURIComponent(url)}`;
        }
        return url;
    }

    function removeWebsite(id) {
        state.websites = state.websites.filter(s => s.id !== id);
        $(`#${id}-iframe`).remove();
        if (state.activeTabId === id) {
            state.activeTabId = state.websites.length > 0 ? state.websites[0].id : null;
            if (!state.activeTabId) $('#browser-placeholder').fadeIn(200);
        }
        saveToCache();
        renderDashboard();
        updateUIState();
        if (state.activeTabId) loadInBrowser(state.activeTabId);
    }

    // --- UI Helpers ---

    function toggleView(showDashboard) {
        if (showDashboard) {
            renderDashboard();
            $tabManager.fadeIn(200).addClass('active');
        } else {
            $tabManager.fadeOut(200).removeClass('active');
        }
    }

    function updateUIState() {
        $tabCountBadge.text(state.websites.length).toggle(state.websites.length > 0);
    }

    function getCollectionName(id) {
        const c = state.collections.find(x => x.id === id);
        return c ? c.name : 'Default';
    }

    function extractTitle(url) {
        try {
            const h = new URL(url).hostname.replace('www.', '').split('.')[0];
            return h.charAt(0).toUpperCase() + h.slice(1);
        } catch (_) { return "Website"; }
    }

    function showToast(m) {
        const id = 'toast-' + Date.now();
        const $t = $(`
            <div id="${id}" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${m}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `);
        $('#toast-container').append($t);
        const toast = new bootstrap.Toast(document.getElementById(id));
        toast.show();
        $t.on('hidden.bs.toast', () => $t.remove());
    }

    function saveToCache() { localStorage.setItem('wib_v3', JSON.stringify(state)); }
    function loadFromCache() {
        const d = JSON.parse(localStorage.getItem('wib_v3') || '{}');
        if (d.collections) state.collections = d.collections;
        if (d.websites) {
            state.websites = d.websites;
            state.websites.forEach(s => createBrowserInstance(s));
        }
        state.activeTabId = d.activeTabId;
    }
});
