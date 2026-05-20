<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wibscreen x86 Emulator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0b0f19;
            color: #f3f4f6;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        #header {
            background-color: #111827;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #1f2937;
        }
        #header h1 {
            margin: 0;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #60a5fa;
        }
        #container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background-color: #000;
            width: 100%;
            height: 100%;
        }
        #emulator-screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #000;
            position: relative;
            width: 100%;
            height: 100%;
        }
        /* Style for the terminal screen */
        #screen_container {
            background-color: #000;
            white-space: pre;
            font-family: monospace;
            font-size: 14px;
            line-height: 16px;
            cursor: text;
        }
        #screen_container:focus {
            outline: none;
        }
        #status {
            padding: 8px 20px;
            background-color: #111827;
            border-top: 1px solid #1f2937;
            font-size: 0.8rem;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }
        .btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            transition: background 0.15s ease;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
        .scaled-screen {
            width: 100% !important;
            height: calc(100vh - 140px) !important;
            max-height: none !important;
            min-height: none !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .scaled-screen canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
        }
        .scaled-screen pre {
            font-size: 1.6vw !important;
            line-height: 1.8vw !important;
        }
    </style>
    <script src="/linux/libv86.js"></script>
</head>
<body>
    <div id="header">
        <h1><i class="fas fa-microchip text-success"></i> WibOS / Linux VM Emulator</h1>
        <div style="display: flex; align-items: center; gap: 12px;">
            <label for="os-select" style="font-size: 0.8rem; font-family: system-ui, -apple-system, sans-serif; color: #9ca3af;">Select OS:</label>
            <select id="os-select" style="background-color: #1f2937; color: white; border: 1px solid #374151; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; outline: none; cursor: pointer; font-family: system-ui, -apple-system, sans-serif;">
                <option value="wibos">WibOS (Custom C/Assembly OS)</option>
                <option value="slitaz">Tiny Core Linux (GUI Desktop - 19MB)</option>
            </select>
            <button class="btn" id="btn-restart"><i class="fas fa-redo"></i> Restart VM</button>
            <button class="btn" id="btn-toggle-scale" style="background-color: #374151;"><i class="fas fa-expand-arrows-alt"></i> Scale to Fit</button>
            <button class="btn" id="btn-toggle-status" style="background-color: #374151;"><i class="fas fa-eye-slash"></i> Hide Status</button>
            <button class="btn" id="btn-fullscreen"><i class="fas fa-expand"></i> Fullscreen</button>
        </div>
    </div>
    
    <div id="container">
        <!-- Emulator Container -->
        <div id="emulator-screen">

            <div id="screen_container" tabindex="0"></div>
            <div id="status">
                <span id="status-text">Initializing emulator...</span>
                <span>Keyboard: Enabled | Mouse: Click inside screen to capture</span>
            </div>
        </div>
    </div>



    <script>
        const DB_NAME = 'WibscreenVM';
        const STORE_NAME = 'States';
        const CACHE_NAME = 'wibscreen-v86-cache';

        // Retrieve selected OS
        let selectedOS = localStorage.getItem('wibscreen_selected_os') || 'wibos';
        if (selectedOS !== 'wibos' && selectedOS !== 'slitaz') {
            selectedOS = 'wibos';
        }
        const STATE_KEY = 'v86_state_' + selectedOS;

        // IndexedDB Helpers to Cache Boot State
        function getSavedState() {
            return new Promise((resolve) => {
                try {
                    const request = indexedDB.open(DB_NAME, 1);
                    request.onupgradeneeded = function(e) {
                        e.target.result.createObjectStore(STORE_NAME);
                    };
                    request.onsuccess = function(e) {
                        const db = e.target.result;
                        const transaction = db.transaction(STORE_NAME, 'readonly');
                        const store = transaction.objectStore(STORE_NAME);
                        const getReq = store.get(STATE_KEY);
                        getReq.onsuccess = function() {
                            resolve(getReq.result || null);
                        };
                        getReq.onerror = function() { resolve(null); };
                    };
                    request.onerror = function() { resolve(null); };
                } catch (err) {
                    resolve(null);
                }
            });
        }

        function saveState(state) {
            return new Promise((resolve) => {
                try {
                    const request = indexedDB.open(DB_NAME, 1);
                    request.onsuccess = function(e) {
                        const db = e.target.result;
                        const transaction = db.transaction(STORE_NAME, 'readwrite');
                        const store = transaction.objectStore(STORE_NAME);
                        store.put(state, STATE_KEY);
                        transaction.oncomplete = function() { resolve(true); };
                    };
                } catch (err) {
                    resolve(false);
                }
            });
        }

        // US Keyboard layout scan codes mapping
        const scanCodes = {
            'a': 0x1E, 'b': 0x30, 'c': 0x2E, 'd': 0x20, 'e': 0x12, 'f': 0x21,
            'g': 0x22, 'h': 0x23, 'i': 0x17, 'j': 0x24, 'k': 0x25, 'l': 0x26,
            'm': 0x32, 'n': 0x31, 'o': 0x18, 'p': 0x19, 'q': 0x10, 'r': 0x13,
            's': 0x1F, 't': 0x14, 'u': 0x16, 'v': 0x2F, 'w': 0x11, 'x': 0x2D,
            'y': 0x15, 'z': 0x2C, '1': 0x02, '2': 0x03, '3': 0x04, '4': 0x05,
            '5': 0x06, '6': 0x07, '7': 0x08, '8': 0x09, '9': 0x0A, '0': 0x0B,
            '\n': 0x1C, ' ': 0x39, '-': 0x0C, '=': 0x0D, '[': 0x1A, ']': 0x1B,
            ';': 0x27, '\'': 0x28, '`': 0x29, '\\': 0x2B, ',': 0x33, '.': 0x34,
            '/': 0x35, '\t': 0x0F, '_': 0x0C, '+': 0x0D, '@': 0x03, ':': 0x27,
            '~': 0x29, '$': 0x06
        };

        // Cache Storage Helpers to avoid double loading
        async function getCacheStorageAssetUrl(key, url, statusText) {
            try {
                const cache = await caches.open(CACHE_NAME);
                let response = await cache.match(url);
                if (!response) {
                    statusText.innerText = "Downloading " + key + " (first-time cache)...";
                    response = await fetch(url);
                    if (!response.ok) throw new Error("Fetch failed");
                    await cache.put(url, response.clone());
                }
                const blob = await response.blob();
                return URL.createObjectURL(blob);
            } catch (e) {
                console.error("Cache Storage Error for " + key + ":", e);
                return url; // fallback to raw path
            }
        }



        function attachEmulatorListeners(emulator, statusText, isWibOS) {
            emulator.add_listener("emulator-ready", function() {
                statusText.innerText = isWibOS 
                    ? "WibOS Custom C/Assembly Kernel Loaded successfully!" 
                    : "Tiny Core Linux VM Booted successfully! Click inside the VM screen to capture mouse.";
            });
        }

        window.onload = async function() {
            const statusText = {
                set innerText(val) {
                    console.log("[Status]:", val);
                    const el = document.getElementById("status-text");
                    if (el) el.innerText = val;
                }
            };

            // Setup select dropdown value
            const osSelect = document.getElementById("os-select");
            if (osSelect) {
                osSelect.value = selectedOS;
                osSelect.onchange = function() {
                    localStorage.setItem('wibscreen_selected_os', this.value);
                    window.location.reload();
                };
            }

            let isoFilename, isoLabel, memSize, vgaMemSize;
            const isWibOS = (selectedOS === 'wibos');
            if (isWibOS) {
                isoFilename = "wibos/wibos.img";
                isoLabel = "WibOS Custom Boot Image";
                memSize = 16 * 1024 * 1024;
                vgaMemSize = 2 * 1024 * 1024;
            } else {
                isoFilename = "tinycore.iso";
                isoLabel = "Tiny Core Linux Live GUI";
                memSize = 256 * 1024 * 1024;
                vgaMemSize = 8 * 1024 * 1024;
            }

            statusText.innerText = "Locating VM engine in Cache Storage...";
            const wasmUrl = await getCacheStorageAssetUrl("WebAssembly Engine", "/linux/v86.wasm", statusText);
            const biosUrl = await getCacheStorageAssetUrl("System BIOS", "/linux/seabios.bin", statusText);
            const vgaBiosUrl = await getCacheStorageAssetUrl("VGA BIOS", "/linux/vgabios.bin", statusText);
            const isoUrl = "/linux/" + isoFilename;

            const V86Starter = window.V86Starter || window.V86;

            const config = {
                wasm_path: wasmUrl,
                memory_size: memSize,
                vga_memory_size: vgaMemSize,
                screen_container: document.getElementById("screen_container"),
                bios: { url: biosUrl },
                vga_bios: { url: vgaBiosUrl },
                autostart: true
            };

            if (isWibOS) {
                config.fda = { url: isoUrl };
            } else {
                config.cdrom = { url: isoUrl };
            }

            // Initialize and boot V86 emulator directly, bypassing simulated BIOS POST
            statusText.innerText = "Booting OS kernel...";
            try {
                const emulator = new V86Starter(config);
                attachEmulatorListeners(emulator, statusText, isWibOS);
            } catch (e) {
                console.error("V86 Initialization failed:", e);
                statusText.innerText = "Error: " + e.message;
            }

            // Scale and Status Bar toggles
            let isScaled = localStorage.getItem('wibscreen_scaled') === 'true';
            let isStatusHidden = localStorage.getItem('wibscreen_status_hidden') === 'true';

            const btnToggleScale = document.getElementById("btn-toggle-scale");
            const btnToggleStatus = document.getElementById("btn-toggle-status");
            const screenContainer = document.getElementById("screen_container");
            const statusElem = document.getElementById("status");

            function updateScaleUI() {
                if (isScaled) {
                    screenContainer.classList.add("scaled-screen");
                    btnToggleScale.innerHTML = '<i class="fas fa-compress"></i> Original Size';
                } else {
                    screenContainer.classList.remove("scaled-screen");
                    btnToggleScale.innerHTML = '<i class="fas fa-expand-arrows-alt"></i> Scale to Fit';
                }
            }

            function updateStatusUI() {
                if (isStatusHidden) {
                    statusElem.style.display = "none";
                    btnToggleStatus.innerHTML = '<i class="fas fa-eye"></i> Show Status';
                } else {
                    statusElem.style.display = "flex";
                    btnToggleStatus.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Status';
                }
            }

            btnToggleScale.onclick = function() {
                isScaled = !isScaled;
                localStorage.setItem('wibscreen_scaled', isScaled);
                updateScaleUI();
            };

            btnToggleStatus.onclick = function() {
                isStatusHidden = !isStatusHidden;
                localStorage.setItem('wibscreen_status_hidden', isStatusHidden);
                updateStatusUI();
            };

            updateScaleUI();
            updateStatusUI();

            document.getElementById("btn-restart").onclick = async function() {
                statusText.innerText = "Rebooting VM...";
                window.location.reload();
            };

            document.getElementById("btn-fullscreen").onclick = function() {
                var elem = document.getElementById("emulator-screen");
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                }
            };
        };
    </script>
</body>
</html>
