$(document).ready(function() {
    // --- Appearance Settings ---
    const defaultSettings = {
        theme: 'night', fontSize: '14px', fontWeight: '500', fontStyle: 'normal',
        textColor: '#f8fafc', primaryColor: '#6366f1', bgColor: '#0f172a',
        cardBg: '#1e293b', cardRadius: '20px', sidebarWidth: '280px'
    };
    let settings = { ...defaultSettings };

    // --- State ---
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
        searchQuery: '',
        sortBy: 'newest'
    };
    let state = { ...defaultState };

    // --- UI Selectors ---
    const UI = {
        body: $('body'),
        collectionsList: $('#collections-list'),
        collectionSelect: $('#new-collection-select'),
        tabsGrid: $('#tabs-grid'),
        tabManager: $('#tab-manager'),
        iframesContainer: $('#iframes-container'),
        mainUrlBar: $('#main-url-input'),
        tabCountBadge: $('#tab-count-badge'),
        globalSearch: $('#global-search'),
        placeholder: $('#browser-placeholder'),
        
        // Modals
        addWebsiteModal: new bootstrap.Modal(document.getElementById('addWebsiteModal')),
        createFolderModal: new bootstrap.Modal(document.getElementById('createFolderModal')),
        settingsModal: new bootstrap.Modal(document.getElementById('settingsModal')),
        renameModal: new bootstrap.Modal(document.getElementById('renameFolderModal')),
        
        inputs: {
            fontSize: $('#set-font-size'), fontWeight: $('#set-font-weight'),
            textColor: $('#set-text-color'), primaryColor: $('#set-primary-color'),
            cardRadius: $('#set-card-radius'), sidebarWidth: $('#set-sidebar-width')
        }
    };

    let targetEditId = null;

    // --- Observer ---
    const lazyObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const $card = $(entry.target);
                const $if = $card.find('iframe');
                if (!$if.attr('src')) {
                    $if.attr('src', $card.data('url'));
                    $if.on('load', () => $card.removeClass('loading'));
                }
                lazyObserver.unobserve(entry.target);
            }
        });
    }, { root: UI.tabManager[0], threshold: 0.1 });

    // --- Init ---
    init();

    function init() {
        loadSettings();
        loadState();
        applySettings();
        setupEvents();
        refreshUI();
    }

    function setupEvents() {
        // Fix: Robust Global Event Binding for Edit/Delete
        $(document).on('click', '.edit-col-btn', function(e) {
            e.preventDefault(); e.stopPropagation();
            targetEditId = $(this).data('id');
            const col = state.collections.find(c => c.id === targetEditId);
            if (col) {
                $('#rename-folder-input').val(col.name);
                UI.renameModal.show();
            }
        });

        $(document).on('click', '.delete-col-btn', function(e) {
            e.preventDefault(); e.stopPropagation();
            const id = $(this).data('id');
            if (confirm("Delete this collection and all its tabs?")) {
                deleteCollection(id);
            }
        });

        $(document).on('click', '.close-tab-btn', function(e) {
            e.preventDefault(); e.stopPropagation();
            deleteWebsite($(this).data('id'));
        });

        // Search
        UI.globalSearch.on('input', debounce(() => {
            state.searchQuery = UI.globalSearch.val().toLowerCase().trim();
            refreshUI();
        }, 300));

        // Navigation
        UI.collectionsList.on('click', '.nav-link', function(e) {
            e.preventDefault();
            state.currentCollectionId = $(this).data('id');
            refreshUI();
            toggleView(true);
        });

        // Dashboard Actions
        UI.tabsGrid.on('click', '.tab-card', function() { loadInBrowser($(this).data('id')); });

        $('#home-btn').on('click', () => { state.currentCollectionId = 'all'; refreshUI(); toggleView(true); });
        $('#open-settings-btn').on('click', () => UI.settingsModal.show());
        $('#theme-toggle-btn').on('click', toggleTheme);
        $('#reset-settings-btn').on('click', resetSettings);
        
        $('#sort-alphabetical').on('click', () => { state.sortBy = 'alphabetical'; refreshUI(); });
        $('#sort-newest').on('click', () => { state.sortBy = 'newest'; refreshUI(); });

        Object.keys(UI.inputs).forEach(key => {
            UI.inputs[key].on('input change', function() {
                settings[key] = $(this).val() + (this.type === 'range' ? 'px' : '');
                applySettings();
            });
        });

        $('#add-collection-btn').on('click', () => UI.createFolderModal.show());
        $('#confirm-create-folder').on('click', handleCreateFolder);
        $('#confirm-rename-folder').on('click', handleRenameFolder);
        $('#add-website-btn').on('click', () => UI.addWebsiteModal.show());
        $('#load-website-btn').on('click', handleAddWebsite);
    }

    function refreshUI() {
        renderSidebar();
        renderDashboard();
        updateUIState();
    }

    // --- Appearance ---
    function applySettings() {
        const r = document.documentElement.style;
        r.setProperty('--font-size-base', settings.fontSize);
        r.setProperty('--font-weight-base', settings.fontWeight);
        r.setProperty('--text-main', settings.textColor);
        r.setProperty('--primary-color', settings.primaryColor);
        r.setProperty('--card-radius', settings.cardRadius);
        r.setProperty('--sidebar-width', settings.sidebarWidth);
        saveSettings();
    }

    function updateTheme(m) {
        settings.theme = m;
        UI.body.attr('data-theme', m);
        syncSettingsToUI(); applySettings();
    }

    function toggleTheme() {
        const next = settings.theme === 'night' ? 'day' : 'night';
        updateTheme(next);
    }

    function syncSettingsToUI() {
        Object.keys(UI.inputs).forEach(k => UI.inputs[k].val(parseInt(settings[k]) || settings[k]));
        const icon = settings.theme === 'day' ? 'sun' : 'moon';
        $('#theme-toggle-btn i').attr('class', `fas fa-${icon} me-2`);
        $('#theme-toggle-btn span').text(settings.theme === 'day' ? 'Day Mode' : 'Night Mode');
    }

    function resetSettings() { settings = { ...defaultSettings }; updateTheme('night'); }

    // --- Rendering ---
    function renderSidebar() {
        const frag = document.createDocumentFragment();
        UI.collectionSelect.empty();
        
        frag.appendChild(createNavItem('all', 'All Workspace', 'fas fa-shapes', state.currentCollectionId === 'all', true));

        let cols = [...state.collections];
        if (state.sortBy === 'alphabetical') cols.sort((a, b) => a.name.localeCompare(b.name));

        cols.forEach(col => {
            if (state.searchQuery && !col.name.toLowerCase().includes(state.searchQuery)) return;
            frag.appendChild(createNavItem(col.id, col.name, col.icon, state.currentCollectionId === col.id));
            UI.collectionSelect.append(`<option value="${col.id}">${col.name}</option>`);
        });
        UI.collectionsList.empty().append(frag);
    }

    function createNavItem(id, name, icon, active, isSystem = false) {
        const li = document.createElement('li');
        li.className = 'nav-item position-relative group fade-in';
        const initials = getInitials(name);
        
        li.innerHTML = `
            <a href="#" class="nav-link ${active ? 'active' : ''}" data-id="${id}">
                <i class="${icon}"></i>
                <span class="flex-grow-1 text-truncate">${name}</span>
                ${!isSystem ? `
                    <div class="nav-actions d-none group-hover-flex align-items-center">
                        <i class="fas fa-edit edit-col-btn me-2 small cursor-pointer" data-id="${id}"></i>
                        <i class="fas fa-trash delete-col-btn small cursor-pointer" data-id="${id}"></i>
                    </div>
                ` : ''}
            </a>
        `;
        return li;
    }

    function renderDashboard() {
        lazyObserver.disconnect();
        UI.tabsGrid.empty();
        
        const isAll = state.currentCollectionId === 'all';
        $('#current-collection-name').text(isAll ? 'Explore Workspace' : getCollectionName(state.currentCollectionId));

        let targetCols = isAll ? [...state.collections] : state.collections.filter(c => c.id === state.currentCollectionId);
        if (state.sortBy === 'alphabetical') targetCols.sort((a, b) => a.name.localeCompare(b.name));

        targetCols.forEach(col => {
            let websites = state.websites.filter(w => {
                const matchCol = w.collectionId === col.id;
                const matchSearch = !state.searchQuery || 
                                    w.title.toLowerCase().includes(state.searchQuery) || 
                                    w.url.toLowerCase().includes(state.searchQuery);
                return matchCol && matchSearch;
            });

            if (isAll && state.searchQuery && websites.length === 0 && !col.name.toLowerCase().includes(state.searchQuery)) return;

            const section = $(`
                <div class="collection-section slide-up">
                    <div class="collection-header">
                        <i class="${col.icon} text-primary fs-4"></i>
                        <h3 class="h4 fw-bold mb-0 heading-title">${col.name}</h3>
                        <span class="badge rounded-pill bg-input text-muted small">${websites.length} tabs</span>
                        <div class="collection-actions">
                            <i class="fas fa-pen edit-col-btn cursor-pointer small" data-id="${col.id}" title="Rename"></i>
                            <i class="fas fa-trash delete-col-btn cursor-pointer small" data-id="${col.id}" title="Delete"></i>
                        </div>
                    </div>
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4"></div>
                </div>
            `);

            const row = section.find('.row');
            if (state.sortBy === 'alphabetical') websites.sort((a, b) => a.title.localeCompare(b.title));
            else websites.sort((a, b) => b.id.split('-')[1] - a.id.split('-')[1]);

            if (websites.length > 0) {
                websites.forEach(site => row.append(`<div class="col">${createTabCardHTML(site)}</div>`));
            } else {
                row.append(`
                    <div class="col-12">
                        <div class="empty-placeholder">
                            <i class="fas fa-layer-group opacity-25 display-6 mb-3 d-block"></i>
                            <p class="text-muted mb-0 fw-medium">No websites found</p>
                            <span class="small text-muted opacity-50">Add a new tab to this collection.</span>
                        </div>
                    </div>
                `);
            }
            UI.tabsGrid.append(section);
        });

        UI.tabsGrid.find('.tab-card').each(function() { lazyObserver.observe(this); });
        UI.placeholder.toggle(state.collections.length === 0);
    }

    function createTabCardHTML(s) {
        const isActive = s.id === state.activeTabId;
        const fav = `https://www.google.com/s2/favicons?sz=64&domain=${new URL(s.url).hostname}`;
        return `
            <div class="tab-card loading ${isActive ? 'active' : ''} fade-in" data-id="${s.id}" data-url="${s.url}">
                <div class="tab-card-header">
                    <img src="${fav}" class="tab-favicon" onerror="this.src='https://favicons.githubusercontent.com/github.com'">
                    <span class="tab-card-title text-truncate">${s.title}</span>
                    <button class="btn btn-link text-muted p-0 ms-auto close-tab-btn" data-id="${s.id}"><i class="fas fa-xmark"></i></button>
                </div>
                <div class="tab-card-preview"><iframe></iframe></div>
            </div>
        `;
    }

    // --- Actions ---
    function handleCreateFolder() {
        const name = $('#new-folder-input').val().trim();
        if (name) {
            state.collections.push({ id: 'col-' + Date.now(), name, icon: 'fas fa-folder' });
            UI.createFolderModal.hide(); $('#new-folder-input').val(''); saveState(); refreshUI();
        }
    }

    function handleRenameFolder() {
        const name = $('#rename-folder-input').val().trim();
        if (name && targetEditId) {
            const col = state.collections.find(c => c.id === targetEditId);
            if (col) {
                col.name = name;
                UI.renameModal.hide(); saveState(); refreshUI();
            }
        }
    }

    function deleteCollection(id) {
        state.collections = state.collections.filter(c => c.id !== id);
        state.websites = state.websites.filter(w => w.collectionId !== id);
        if (state.currentCollectionId === id) state.currentCollectionId = 'all';
        saveState(); refreshUI();
    }

    function handleAddWebsite() {
        let url = $('#new-url-input').val().trim();
        let name = $('#new-name-input').val().trim();
        if (!url) return;
        if (!/^https?:\/\//i.test(url)) url = 'https://' + url;
        const id = 'site-' + Date.now();
        const site = { id, url: getSmartUrl(url), originalUrl: url, title: name || extractTitle(url), collectionId: UI.collectionSelect.val() };
        state.websites.push(site);
        createIframe(site); loadInBrowser(id);
        UI.addWebsiteModal.hide(); $('#new-url-input, #new-name-input').val('');
        saveState(); refreshUI();
    }

    function createIframe(s) { if (!$(`#${s.id}-iframe`).length) UI.iframesContainer.append(`<iframe id="${s.id}-iframe" src="${s.url}" class="browser-tab-iframe"></iframe>`); }

    function loadInBrowser(id) {
        state.activeTabId = id;
        UI.iframesContainer.find('.browser-tab-iframe').hide();
        const s = state.websites.find(x => x.id === id);
        if (s) { $(`#${id}-iframe`).show(); UI.mainUrlBar.val(s.originalUrl); UI.placeholder.hide(); }
        toggleView(false); saveState(); refreshUI();
    }

    function deleteWebsite(id) {
        state.websites = state.websites.filter(s => s.id !== id);
        $(`#${id}-iframe`).remove();
        if (state.activeTabId === id) state.activeTabId = state.websites.length > 0 ? state.websites[0].id : null;
        saveState(); refreshUI();
        if (state.activeTabId) loadInBrowser(state.activeTabId);
    }

    // --- Helpers ---
    function toggleView(s) { s ? UI.tabManager.show() : UI.tabManager.hide(); }
    function updateUIState() { UI.tabCountBadge.text(state.websites.length).toggle(state.websites.length > 0); }
    function getSmartUrl(u) { return ['google.com','facebook.com','github.com','youtube.com'].some(b => u.includes(b)) ? `https://corsproxy.io/?${encodeURIComponent(u)}` : u; }
    function extractTitle(u) { try { const h = new URL(u).hostname.replace('www.','').split('.')[0]; return h.charAt(0).toUpperCase() + h.slice(1); } catch(_) { return "Website"; } }
    function getCollectionName(id) { return state.collections.find(x => x.id === id)?.name || 'Default'; }
    function debounce(f, w) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => f.apply(this, a), w); }; }
    function getInitials(name) { return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2); }
    
    function saveSettings() { localStorage.setItem('wib_settings_v6', JSON.stringify(settings)); }
    function loadSettings() { const s = JSON.parse(localStorage.getItem('wib_settings_v6') || '{}'); settings = { ...settings, ...s }; syncSettingsToUI(); }
    function saveState() { localStorage.setItem('wib_state_v6', JSON.stringify(state)); }
    function loadState() {
        const s = JSON.parse(localStorage.getItem('wib_state_v6') || '{}');
        if (s.collections) state.collections = s.collections;
        if (!state.collections || state.collections.length === 0) state.collections = [...defaultState.collections];
        if (s.websites) { state.websites = s.websites; state.websites.forEach(w => createIframe(w)); }
        state.activeTabId = s.activeTabId;
        state.sortBy = s.sortBy || 'newest';
    }
});
