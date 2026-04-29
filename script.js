$(document).ready(function() {
    // --- Appearance Settings Management ---
    const defaultSettings = {
        theme: 'night',
        fontSize: '14px',
        fontWeight: '500',
        fontStyle: 'normal',
        textColor: '#f8fafc',
        primaryColor: '#6366f1',
        bgColor: '#0f172a',
        cardBg: '#1e293b',
        cardRadius: '16px',
        sidebarWidth: '260px'
    };

    let settings = { ...defaultSettings };

    // --- State Management ---
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
        
        // Settings Inputs
        inputs: {
            fontSize: $('#set-font-size'),
            fontWeight: $('#set-font-weight'),
            fontStyle: $('#set-font-style'),
            textColor: $('#set-text-color'),
            primaryColor: $('#set-primary-color'),
            bgColor: $('#set-bg-color'),
            cardBg: $('#set-card-bg'),
            cardRadius: $('#set-card-radius'),
            sidebarWidth: $('#set-sidebar-width')
        }
    };

    // --- Performance: Lazy Loading ---
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

    // --- Initialization ---
    init();

    function init() {
        loadSettings();
        loadState();
        applySettings();
        setupEvents();
        renderSidebar();
        renderDashboard();
        updateUI();
    }

    function setupEvents() {
        // Search
        UI.globalSearch.on('input', debounce(() => {
            state.searchQuery = UI.globalSearch.val().toLowerCase();
            renderSidebar();
            renderDashboard();
        }, 250));

        // Navigation
        UI.collectionsList.on('click', '.nav-link', function(e) {
            e.preventDefault();
            state.currentCollectionId = $(this).data('id');
            renderSidebar();
            toggleView(true);
        });

        UI.tabsGrid.on('click', '.tab-card', function() { loadInBrowser($(this).data('id')); });
        UI.tabsGrid.on('click', '.close-tab-btn', function(e) {
            e.stopPropagation();
            deleteWebsite($(this).data('id'));
        });

        $('#home-btn').on('click', () => {
            state.currentCollectionId = 'all';
            renderSidebar();
            toggleView(true);
        });

        // Settings & Theme
        $('#open-settings-btn').on('click', () => UI.settingsModal.show());
        $('#theme-toggle-btn').on('click', toggleTheme);
        $('input[name="theme-mode"]').on('change', function() { updateTheme($(this).val()); });
        $('#reset-settings-btn').on('click', resetSettings);

        // Bind all settings inputs
        Object.keys(UI.inputs).forEach(key => {
            UI.inputs[key].on('input change', function() {
                settings[key] = $(this).val() + (this.type === 'range' && key.includes('Radius') ? 'px' : this.type === 'range' && key.includes('Width') ? 'px' : '');
                applySettings();
            });
        });

        // Folder & Website
        $('#add-collection-btn').on('click', () => UI.createFolderModal.show());
        $('#confirm-create-folder').on('click', handleCreateFolder);
        $('#add-website-btn').on('click', () => UI.addWebsiteModal.show());
        $('#load-website-btn').on('click', handleAddWebsite);
    }

    // --- Appearance Engine ---

    function applySettings() {
        const root = document.documentElement.style;
        root.setProperty('--font-size-base', settings.fontSize);
        root.setProperty('--font-weight-base', settings.fontWeight);
        root.setProperty('--font-family', settings.fontStyle === 'italic' ? "'Inter', sans-serif, italic" : "'Inter', sans-serif");
        UI.body.css('font-style', settings.fontStyle);
        
        root.setProperty('--text-main', settings.textColor);
        root.setProperty('--primary-color', settings.primaryColor);
        root.setProperty('--bg-app', settings.bgColor);
        root.setProperty('--bg-card', settings.cardBg);
        root.setProperty('--card-radius', settings.cardRadius);
        root.setProperty('--sidebar-width', settings.sidebarWidth);

        saveSettings();
    }

    function updateTheme(mode) {
        settings.theme = mode;
        UI.body.attr('data-theme', mode);
        
        // Adjust default colors for Light mode if user hasn't customized yet
        if (mode === 'day') {
            settings.textColor = '#0f172a';
            settings.bgColor = '#f8fafc';
            settings.cardBg = '#ffffff';
            $('#theme-toggle-btn i').attr('class', 'fas fa-sun me-2');
            $('#theme-toggle-btn span').text('Day Mode');
        } else {
            settings.textColor = '#f8fafc';
            settings.bgColor = '#0f172a';
            settings.cardBg = '#1e293b';
            $('#theme-toggle-btn i').attr('class', 'fas fa-moon me-2');
            $('#theme-toggle-btn span').text('Night Mode');
        }
        
        syncSettingsToUI();
        applySettings();
    }

    function toggleTheme() {
        const next = settings.theme === 'night' ? 'day' : 'night';
        $(`#theme-${next}`).prop('checked', true);
        updateTheme(next);
    }

    function syncSettingsToUI() {
        UI.inputs.fontSize.val(settings.fontSize);
        UI.inputs.fontWeight.val(settings.fontWeight);
        UI.inputs.fontStyle.val(settings.fontStyle);
        UI.inputs.textColor.val(settings.textColor);
        UI.inputs.primaryColor.val(settings.primaryColor);
        UI.inputs.bgColor.val(settings.bgColor);
        UI.inputs.cardBg.val(settings.cardBg);
        UI.inputs.cardRadius.val(parseInt(settings.cardRadius));
        UI.inputs.sidebarWidth.val(parseInt(settings.sidebarWidth));
    }

    function resetSettings() {
        settings = { ...defaultSettings };
        syncSettingsToUI();
        applySettings();
        updateTheme('night');
        showToast("Settings reset to defaults");
    }

    // --- Content Rendering ---

    function renderSidebar() {
        const fragment = document.createDocumentFragment();
        UI.collectionSelect.empty();

        const allActive = state.currentCollectionId === 'all';
        fragment.appendChild(createNavItem('all', 'All Workspace', 'fas fa-shapes', allActive));

        state.collections.forEach(col => {
            if (state.searchQuery && !col.name.toLowerCase().includes(state.searchQuery)) return;
            fragment.appendChild(createNavItem(col.id, col.name, col.icon, state.currentCollectionId === col.id));
            UI.collectionSelect.append(`<option value="${col.id}">${col.name}</option>`);
        });

        UI.collectionsList.empty().append(fragment);
    }

    function createNavItem(id, name, icon, active) {
        const li = document.createElement('li');
        li.className = 'nav-item';
        li.innerHTML = `<a href="#" class="nav-link ${active ? 'active' : ''}" data-id="${id}"><i class="${icon}"></i><span>${name}</span></a>`;
        return li;
    }

    function renderDashboard() {
        lazyObserver.disconnect();
        const fragment = document.createDocumentFragment();
        const filtered = state.websites.filter(w => (state.currentCollectionId === 'all' || w.collectionId === state.currentCollectionId) && (!state.searchQuery || w.title.toLowerCase().includes(state.searchQuery)));

        filtered.forEach(site => {
            const isActive = site.id === state.activeTabId;
            const fav = `https://www.google.com/s2/favicons?sz=64&domain=${new URL(site.url).hostname}`;
            const col = document.createElement('div');
            col.className = 'col';
            col.innerHTML = `
                <div class="tab-card loading ${isActive ? 'active' : ''}" data-id="${site.id}" data-url="${site.url}">
                    <div class="tab-card-header">
                        <img src="${fav}" class="tab-favicon" onerror="this.src='https://favicons.githubusercontent.com/github.com'">
                        <span class="tab-card-title">${site.title}</span>
                        <button class="btn btn-link text-muted p-0 ms-auto close-tab-btn" data-id="${site.id}"><i class="fas fa-xmark"></i></button>
                    </div>
                    <div class="tab-card-preview"><iframe></iframe></div>
                </div>
            `;
            fragment.appendChild(col);
        });

        UI.tabsGrid.empty().append(fragment);
        UI.tabsGrid.find('.tab-card').each(function() { lazyObserver.observe(this); });
    }

    // --- Actions ---

    function handleCreateFolder() {
        const name = $('#new-folder-input').val().trim();
        if (name) {
            const id = 'col-' + Date.now();
            state.collections.push({ id, name, icon: 'fas fa-folder' });
            UI.createFolderModal.hide();
            $('#new-folder-input').val('');
            renderSidebar();
            saveState();
        }
    }

    function handleAddWebsite() {
        let url = $('#new-url-input').val().trim();
        let name = $('#new-name-input').val().trim();
        if (!url) return;
        if (!/^https?:\/\//i.test(url)) url = 'https://' + url;

        const id = 'site-' + Date.now();
        const site = { id, url: getSmartUrl(url), originalUrl: url, title: name || extractTitle(url), collectionId: UI.collectionSelect.val() };
        state.websites.push(site);
        createIframe(site);
        loadInBrowser(id);
        UI.addWebsiteModal.hide();
        $('#new-url-input, #new-name-input').val('');
        updateUI();
    }

    function createIframe(site) {
        UI.iframesContainer.append(`<iframe id="${site.id}-iframe" src="${site.url}" class="browser-tab-iframe"></iframe>`);
    }

    function loadInBrowser(id) {
        state.activeTabId = id;
        UI.iframesContainer.find('.browser-tab-iframe').hide();
        const site = state.websites.find(s => s.id === id);
        if (site) {
            $(`#${id}-iframe`).show();
            UI.mainUrlBar.val(site.originalUrl);
            UI.placeholder.hide();
        }
        toggleView(false);
        saveState();
        renderDashboard();
    }

    function deleteWebsite(id) {
        state.websites = state.websites.filter(s => s.id !== id);
        $(`#${id}-iframe`).remove();
        if (state.activeTabId === id) state.activeTabId = state.websites.length > 0 ? state.websites[0].id : null;
        saveState();
        renderDashboard();
        updateUI();
    }

    // --- Helpers ---
    function toggleView(show) { show ? UI.tabManager.show() : UI.tabManager.hide(); }
    function updateUI() { UI.tabCountBadge.text(state.websites.length).toggle(state.websites.length > 0); }
    function getSmartUrl(url) { return ['google.com','facebook.com','github.com','youtube.com'].some(b => url.includes(b)) ? `https://corsproxy.io/?${encodeURIComponent(url)}` : url; }
    function extractTitle(url) { try { const h = new URL(url).hostname.replace('www.','').split('.')[0]; return h.charAt(0).toUpperCase() + h.slice(1); } catch(_) { return "Website"; } }
    function debounce(f, w) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => f.apply(this, a), w); }; }
    function showToast(m) { alert(m); } // Simple fallback
    
    function saveSettings() { localStorage.setItem('wib_settings_v2', JSON.stringify(settings)); }
    function loadSettings() {
        const s = JSON.parse(localStorage.getItem('wib_settings_v2') || '{}');
        settings = { ...settings, ...s };
        UI.body.attr('data-theme', settings.theme);
        syncSettingsToUI();
    }
    function saveState() { localStorage.setItem('wib_state_v2', JSON.stringify(state)); }
    function loadState() {
        const s = JSON.parse(localStorage.getItem('wib_state_v2') || '{}');
        if (s.collections) state.collections = s.collections;
        if (s.websites) {
            state.websites = s.websites;
            state.websites.forEach(w => createIframe(w));
        }
        state.activeTabId = s.activeTabId;
    }
});
