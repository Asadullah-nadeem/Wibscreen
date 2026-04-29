$(document).ready(function() {
    // --- Configuration & Constants ---
    const DEBOUNCE_MS = 250;
    const LAZY_LOAD_DELAY = 400;

    // --- State Management (With Preloaded Defaults) ---
    const defaultState = {
        collections: [
            { id: 'work', name: 'Work', icon: 'fas fa-briefcase' },
            { id: 'social', name: 'Social', icon: 'fas fa-hashtag' },
            { id: 'professional', name: 'Professional', icon: 'fas fa-user-tie' },
            { id: 'research', name: 'Research', icon: 'fas fa-microscope' }
        ],
        websites: [],
        activeTabId: null,
        currentCollectionId: 'all',
        searchQuery: ''
    };

    let state = { ...defaultState };

    // --- DOM Selectors ---
    const UI = {
        collectionsList: $('#collections-list'),
        collectionSelect: $('#new-collection-select'),
        tabsGrid: $('#tabs-grid'),
        tabManager: $('#tab-manager'),
        iframesContainer: $('#iframes-container'),
        mainUrlBar: $('#main-url-input'),
        tabCountBadge: $('#tab-count-badge'),
        globalSearch: $('#global-search'),
        placeholder: $('#browser-placeholder'),
        statusText: $('#status-text'),
        
        // Modals
        addWebsiteModal: new bootstrap.Modal(document.getElementById('addWebsiteModal')),
        createFolderModal: new bootstrap.Modal(document.getElementById('createFolderModal')),
        
        // Context Menu
        contextMenu: $('#context-menu')
    };

    let contextTargetId = null;

    // --- Performance: Lazy Loading ---
    const lazyObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const $card = $(entry.target);
                const url = $card.data('url');
                const $iframe = $card.find('iframe');
                
                if (!$iframe.attr('src')) {
                    setTimeout(() => {
                        $iframe.attr('src', url);
                        $iframe.on('load', () => $card.removeClass('loading'));
                    }, LAZY_LOAD_DELAY);
                }
                lazyObserver.unobserve(entry.target);
            }
        });
    }, { root: UI.tabManager[0], threshold: 0.1 });

    // --- Core Logic ---
    init();

    function init() {
        loadFromCache();
        setupEvents();
        renderSidebar();
        renderDashboard();
        updateGlobalUI();
    }

    function setupEvents() {
        // Search
        UI.globalSearch.on('input', debounce(function() {
            state.searchQuery = $(this).val().toLowerCase();
            renderSidebar();
            renderDashboard();
        }, DEBOUNCE_MS));

        // Folder Creation
        $('#add-collection-btn').on('click', () => UI.createFolderModal.show());
        $('#confirm-create-folder').on('click', handleCreateFolder);

        // Website Creation
        $('#add-website-btn').on('click', () => UI.addWebsiteModal.show());
        $('#load-website-btn').on('click', handleAddWebsite);

        // Navigation (Delegated)
        UI.collectionsList.on('click', '.nav-link', function(e) {
            e.preventDefault();
            state.currentCollectionId = $(this).data('id');
            renderSidebar();
            toggleView(true);
        });

        UI.tabsGrid.on('click', '.tab-card', function() {
            loadInBrowser($(this).data('id'));
        });

        UI.tabsGrid.on('click', '.close-tab-btn', function(e) {
            e.stopPropagation();
            deleteWebsite($(this).data('id'));
        });

        $('#refresh-btn').on('click', () => {
            if (state.activeTabId) {
                const $if = $(`#${state.activeTabId}-iframe`);
                $if.attr('src', $if.attr('src'));
                showToast("Refreshing session...");
            }
        });

        $('#home-btn').on('click', () => {
            state.currentCollectionId = 'all';
            renderSidebar();
            toggleView(true);
        });

        // Context Menu
        UI.collectionsList.on('contextmenu', '.nav-link', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (id === 'all') return;
            contextTargetId = id;
            UI.contextMenu.css({ top: e.pageY, left: e.pageX }).addClass('show');
        });

        $(document).on('click', () => UI.contextMenu.removeClass('show'));
    }

    // --- Rendering Layer ---

    function renderSidebar() {
        const fragment = document.createDocumentFragment();
        UI.collectionSelect.empty();

        // System Folder: All
        const allItem = createNavElement('all', 'All Workspace', 'fas fa-shapes', state.currentCollectionId === 'all');
        fragment.appendChild(allItem);

        state.collections.forEach(col => {
            if (state.searchQuery && !col.name.toLowerCase().includes(state.searchQuery)) return;
            const item = createNavElement(col.id, col.name, col.icon, state.currentCollectionId === col.id);
            fragment.appendChild(item);
            UI.collectionSelect.append(`<option value="${col.id}">${col.name}</option>`);
        });

        UI.collectionsList.empty().append(fragment);
    }

    function createNavElement(id, name, icon, active) {
        const li = document.createElement('li');
        li.className = 'nav-item';
        li.innerHTML = `
            <a href="#" class="nav-link ${active ? 'active' : ''}" data-id="${id}">
                <i class="${icon}"></i>
                <span>${name}</span>
            </a>
        `;
        return li;
    }

    function renderDashboard() {
        lazyObserver.disconnect();
        const fragment = document.createDocumentFragment();
        
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
            
            const col = document.createElement('div');
            col.className = 'col';
            col.innerHTML = `
                <div class="tab-card loading ${isActive ? 'active' : ''}" data-id="${site.id}" data-url="${site.url}">
                    <div class="tab-card-header">
                        <img src="${favicon}" class="tab-favicon" onerror="this.src='https://favicons.githubusercontent.com/github.com'">
                        <span class="tab-card-title">${site.title}</span>
                        <button class="btn btn-link text-muted p-0 ms-auto close-tab-btn" data-id="${site.id}">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                    <div class="tab-card-preview">
                        <div class="preview-iframe-wrapper">
                            <iframe></iframe>
                        </div>
                    </div>
                </div>
            `;
            fragment.appendChild(col);
        });

        UI.tabsGrid.empty().append(fragment);
        UI.tabsGrid.find('.tab-card').each(function() { lazyObserver.observe(this); });

        if (filtered.length === 0 && state.websites.length > 0) {
            UI.tabsGrid.append(`<div class="col-12 text-center py-5"><p class="text-muted">No websites found.</p></div>`);
        }
    }

    // --- Actions ---

    function handleCreateFolder() {
        const name = $('#new-folder-input').val().trim();
        if (name) {
            const id = 'col-' + Date.now();
            state.collections.push({ id, name, icon: 'fas fa-folder' });
            saveToCache();
            renderSidebar();
            UI.createFolderModal.hide();
            $('#new-folder-input').val('');
            showToast(`Collection "${name}" created.`);
        }
    }

    function handleAddWebsite() {
        let url = $('#new-url-input').val().trim();
        let name = $('#new-name-input').val().trim();
        let colId = UI.collectionSelect.val();

        if (!url) return;
        if (!/^https?:\/\//i.test(url)) url = 'https://' + url;

        const siteId = 'site-' + Date.now();
        const site = {
            id: siteId,
            url: getSmartUrl(url),
            originalUrl: url,
            title: name || extractTitle(url),
            collectionId: colId
        };

        state.websites.push(site);
        saveToCache();
        
        createIframe(site);
        loadInBrowser(siteId);
        
        UI.addWebsiteModal.hide();
        $('#new-url-input, #new-name-input').val('');
        toggleView(false);
        updateGlobalUI();
    }

    function createIframe(site) {
        const $iframe = $(`<iframe id="${site.id}-iframe" src="${site.url}" class="browser-tab-iframe"></iframe>`);
        UI.iframesContainer.append($iframe);
    }

    function loadInBrowser(id) {
        state.activeTabId = id;
        UI.iframesContainer.find('.browser-tab-iframe').hide();
        
        const site = state.websites.find(s => s.id === id);
        if (site) {
            $(`#${id}-iframe`).show();
            UI.mainUrlBar.val(site.originalUrl);
            UI.placeholder.hide();
            UI.statusText.text(`Browsing: ${site.title}`);
        }
        
        toggleView(false);
        saveToCache();
        renderDashboard();
    }

    function deleteWebsite(id) {
        state.websites = state.websites.filter(s => s.id !== id);
        $(`#${id}-iframe`).remove();
        if (state.activeTabId === id) state.activeTabId = state.websites.length > 0 ? state.websites[0].id : null;
        saveToCache();
        renderDashboard();
        updateGlobalUI();
        if (state.activeTabId) loadInBrowser(state.activeTabId);
        else { UI.placeholder.show(); UI.statusText.text('Workspace Idle'); }
    }

    // --- Helpers ---

    function toggleView(showGrid) {
        if (showGrid) {
            renderDashboard();
            UI.tabManager.show();
        } else {
            UI.tabManager.hide();
        }
    }

    function updateGlobalUI() {
        UI.tabCountBadge.text(state.websites.length).toggle(state.websites.length > 0);
    }

    function getSmartUrl(url) {
        const bl = ['google.com', 'facebook.com', 'twitter.com', 'github.com', 'youtube.com'];
        return bl.some(b => url.includes(b)) ? `https://corsproxy.io/?${encodeURIComponent(url)}` : url;
    }

    function extractTitle(url) {
        try {
            const h = new URL(url).hostname.replace('www.', '').split('.')[0];
            return h.charAt(0).toUpperCase() + h.slice(1);
        } catch (_) { return "Website"; }
    }

    function getCollectionName(id) {
        return state.collections.find(x => x.id === id)?.name || 'Default';
    }

    function debounce(f, w) {
        let t; return function(...a) { clearTimeout(t); t = setTimeout(() => f.apply(this, a), w); };
    }

    function showToast(m) {
        const id = 't-' + Date.now();
        UI.toastContainer.append(`
            <div id="${id}" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert">
                <div class="d-flex"><div class="toast-body">${m}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>
            </div>
        `);
        new bootstrap.Toast(document.getElementById(id)).show();
    }

    function saveToCache() { localStorage.setItem('wib_final_v1', JSON.stringify(state)); }
    function loadFromCache() {
        const d = JSON.parse(localStorage.getItem('wib_final_v1') || '{}');
        if (d.collections) state.collections = d.collections;
        if (d.websites) {
            state.websites = d.websites;
            state.websites.forEach(s => createIframe(s));
        }
        state.activeTabId = d.activeTabId;
    }
});
