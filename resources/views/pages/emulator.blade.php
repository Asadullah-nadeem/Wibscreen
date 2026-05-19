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
            font-family: 'Courier New', Courier, monospace;
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
            font-family: system-ui, -apple-system, sans-serif;
        }
        #container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            background-color: #000;
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
            font-family: system-ui, -apple-system, sans-serif;
        }
        .btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.8rem;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            transition: background 0.15s ease;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
    </style>
    <script src="/linux/libv86.js"></script>
</head>
<body>
    <div id="header">
        <h1><i class="fas fa-microchip text-success"></i> x86 WebAssembly VM</h1>
        <div style="display: flex; align-items: center; gap: 12px;">
            <label for="os-select" style="font-size: 0.8rem; font-family: system-ui, -apple-system, sans-serif; color: #9ca3af;">Select OS:</label>
            <select id="os-select" style="background-color: #1f2937; color: white; border: 1px solid #374151; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; outline: none; cursor: pointer; font-family: system-ui, -apple-system, sans-serif;">
                <option value="tinycore">Tiny Core Linux (17MB)</option>
                <option value="alpine">Alpine Linux Virt (44MB)</option>
            </select>
            <button class="btn" id="btn-restart"><i class="fas fa-redo"></i> Restart VM</button>
            <button class="btn" id="btn-fullscreen"><i class="fas fa-expand"></i> Fullscreen</button>
        </div>
    </div>
    
    <div id="container">
        <div id="screen_container" tabindex="0"></div>
    </div>

    <div id="status">
        <span id="status-text">Booting WebAssembly VM...</span>
        <span>Powered by v86 x86 Emulator</span>
    </div>

    <script>
        const DB_NAME = 'WibscreenVM';
        const STORE_NAME = 'States';
        
        // Use a unique state key per operating system
        const selectedOS = localStorage.getItem('wibscreen_selected_os') || 'tinycore';
        const STATE_KEY = 'v86_state_' + selectedOS;

        document.getElementById('os-select').value = selectedOS;

        // IndexedDB Helpers
        function getSavedState() {
            return new Promise((resolve) => {
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
                    getReq.onerror = function() {
                        resolve(null);
                    };
                };
                request.onerror = function() {
                    resolve(null);
                };
            });
        }

        function saveStateToDB(buffer) {
            return new Promise((resolve) => {
                const request = indexedDB.open(DB_NAME, 1);
                request.onsuccess = function(e) {
                    const db = e.target.result;
                    const transaction = db.transaction(STORE_NAME, 'readwrite');
                    const store = transaction.objectStore(STORE_NAME);
                    store.put(buffer, STATE_KEY);
                    transaction.oncomplete = function() {
                        resolve(true);
                    };
                };
            });
        }

        function clearSavedState() {
            return new Promise((resolve) => {
                const request = indexedDB.open(DB_NAME, 1);
                request.onsuccess = function(e) {
                    const db = e.target.result;
                    const transaction = db.transaction(STORE_NAME, 'readwrite');
                    const store = transaction.objectStore(STORE_NAME);
                    store.delete(STATE_KEY);
                    transaction.oncomplete = function() {
                        resolve(true);
                    };
                };
            });
        }

        // Keyboard Scancodes Mapping (US Set 1)
        const scanCodes = {
            'a': 0x1E, 'b': 0x30, 'c': 0x2E, 'd': 0x20, 'e': 0x12, 'f': 0x21, 'g': 0x22, 'h': 0x23, 'i': 0x17, 'j': 0x24, 'k': 0x25, 'l': 0x26, 'm': 0x32, 'n': 0x31, 'o': 0x18, 'p': 0x19, 'q': 0x10, 'r': 0x13, 's': 0x1F, 't': 0x14, 'u': 0x16, 'v': 0x2F, 'w': 0x11, 'x': 0x2D, 'y': 0x15, 'z': 0x2C,
            '0': 0x0B, '1': 0x02, '2': 0x03, '3': 0x04, '4': 0x05, '5': 0x06, '6': 0x07, '7': 0x08, '8': 0x09, '9': 0x0A,
            ' ': 0x39, '=': 0x0D, '-': 0x0C, '_': 0x0C, '+': 0x0D, '@': 0x03, ':': 0x27, '~': 0x29, '$': 0x05, '\\': 0x2B,
            '\n': 0x1C
        };

        const CACHE_NAME = 'wibscreen-v86-cache';

        // Cache Storage Helper to load assets from cache or download and cache them
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

        window.onload = async function() {
            const statusText = document.getElementById("status-text");
            const rawUsername = "{{ auth()->user()->name }}";
            const username = rawUsername.toLowerCase().replace(/[^a-z0-9]/g, '') || 'wibuser';

            let isoFilename = "tinycore.iso";
            let isoLabel = "Tiny Core Linux ISO";
            if (selectedOS === 'alpine') {
                isoFilename = "alpine.iso";
                isoLabel = "Alpine Linux ISO";
            }

            statusText.innerText = "Locating VM engine in Cache Storage...";
            const wasmUrl = await getCacheStorageAssetUrl("WebAssembly Engine", "/linux/v86.wasm", statusText);
            const biosUrl = await getCacheStorageAssetUrl("System BIOS", "/linux/seabios.bin", statusText);
            const vgaBiosUrl = await getCacheStorageAssetUrl("VGA BIOS", "/linux/vgabios.bin", statusText);
            const isoUrl = await getCacheStorageAssetUrl(isoLabel, "/linux/" + isoFilename, statusText);

            statusText.innerText = "Checking cache for boot state...";
            const savedState = await getSavedState();
            const isRestored = !!savedState;

            const config = {
                wasm_path: wasmUrl,
                memory_size: 64 * 1024 * 1024, // 64MB (compact & fast memory state)
                vga_memory_size: 2 * 1024 * 1024,
                screen_container: document.getElementById("screen_container"),
                bios: {
                    url: biosUrl,
                },
                vga_bios: {
                    url: vgaBiosUrl,
                },
                cdrom: {
                    url: isoUrl,
                },
                autostart: true,
            };

            if (isRestored) {
                config.state = { buffer: savedState };
                statusText.innerText = "Restoring cached VM state (takes < 1 sec)...";
            } else {
                statusText.innerText = "First-time boot. Preparing environment (takes ~15 sec)...";
            }

            var emulator = new V86Starter(config);

            function sendKey(code, isShift = false) {
                if (isShift) {
                    emulator.bus.send("keyboard-code", 0x2A); // Shift Press
                }
                emulator.bus.send("keyboard-code", code);
                emulator.bus.send("keyboard-code", code | 0x80); // Release
                if (isShift) {
                    emulator.bus.send("keyboard-code", 0x2A | 0x80); // Shift Release
                }
            }

            function sendString(str, delay = 25) {
                return new Promise((resolve) => {
                    let index = 0;
                    function next() {
                        if (index >= str.length) {
                            resolve();
                            return;
                        }
                        const char = str[index];
                        const lowerChar = char.toLowerCase();
                        const code = scanCodes[lowerChar];
                        const isShift = (char !== lowerChar && /[a-z]/i.test(char)) || ['_', '+', '@', ':', '~', '$'].includes(char);
                        
                        if (code !== undefined) {
                            sendKey(code, isShift);
                        }
                        index++;
                        setTimeout(next, delay);
                    }
                    next();
                });
            }

            emulator.add_listener("emulator-ready", async function() {
                if (isRestored) {
                    statusText.innerText = "Restoring session for " + username + "...";
                    
                    // Root/tc prompt setup
                    let loginCommands = [];
                    if (selectedOS === 'alpine') {
                        loginCommands = [
                            `stty erase ^?\n`,
                            `clear\n`
                        ];
                    } else {
                        loginCommands = [
                            `stty erase ^?\n`,
                            `sudo adduser -D -s /bin/sh ${username}\n`,
                            `su - ${username}\n`,
                            `stty erase ^?\n`,
                            `echo "stty erase ^?" >> ~/.profile\n`,
                            `export PS1="${username}@wibscreen:\\$ "\n`,
                            `clear\n`,
                            `echo "========================================="\n`,
                            `echo "   Welcome to Wibscreen x86 Console!"\n`,
                            `echo "   Logged in as: ${username}"\n`,
                            `echo "========================================="\n`
                        ];
                    }

                    for (const cmd of loginCommands) {
                        await sendString(cmd);
                        await new Promise(r => setTimeout(r, 300));
                    }
                    
                    statusText.innerText = "VM restored. Active user: " + username;
                } else {
                    statusText.innerText = "Booting OS kernel (first time)...";
                    
                    const bootTimeout = (selectedOS === 'alpine') ? 22000 : 12500;
                    
                    setTimeout(async function() {
                        statusText.innerText = "Caching pristine booted VM state...";
                        
                        emulator.save_state(async function(err, stateBuffer) {
                            if (!err && stateBuffer) {
                                await saveStateToDB(stateBuffer);
                                statusText.innerText = "State cached. Configuring user...";
                            } else {
                                statusText.innerText = "Failed to cache state. Configuring user...";
                            }
                            
                            // Now configure user login
                            let setupCommands = [];
                            if (selectedOS === 'alpine') {
                                setupCommands = [
                                    `root\n`, // Log in as default root user
                                    `stty erase ^?\n`,
                                    `adduser -D -s /bin/sh ${username}\n`,
                                    `su - ${username}\n`,
                                    `stty erase ^?\n`,
                                    `export PS1="${username}@wibscreen:\\$ "\n`,
                                    `clear\n`,
                                    `echo "========================================="\n`,
                                    `echo "   Welcome to Wibscreen Alpine Console!"\n`,
                                    `echo "   Logged in as: ${username}"\n`,
                                    `echo "========================================="\n`
                                ];
                            } else {
                                setupCommands = [
                                    `stty erase ^?\n`,
                                    `sudo adduser -D -s /bin/sh ${username}\n`,
                                    `su - ${username}\n`,
                                    `stty erase ^?\n`,
                                    `echo "stty erase ^?" >> ~/.profile\n`,
                                    `export PS1="${username}@wibscreen:\\$ "\n`,
                                    `clear\n`,
                                    `echo "========================================="\n`,
                                    `echo "   Welcome to Wibscreen x86 Console!"\n`,
                                    `echo "   Logged in as: ${username}"\n`,
                                    `echo "========================================="\n`
                                ];
                            }

                            for (const cmd of setupCommands) {
                                await sendString(cmd);
                                await new Promise(r => setTimeout(r, 450));
                            }
                            
                            statusText.innerText = "VM Booted & configured for: " + username;
                        });
                    }, bootTimeout);
                }
            });

            document.getElementById("btn-restart").onclick = async function() {
                statusText.innerText = "Clearing cache & rebooting VM...";
                await clearSavedState();
                window.location.reload();
            };

            document.getElementById("btn-fullscreen").onclick = function() {
                var elem = document.getElementById("screen_container");
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                }
            };

            document.getElementById('os-select').onchange = async function() {
                localStorage.setItem('wibscreen_selected_os', this.value);
                statusText.innerText = "Switching Operating System...";
                // Clear state so it boots fresh on first run of the other OS
                const oldStateKey = 'v86_state_' + this.value;
                const request = indexedDB.open(DB_NAME, 1);
                request.onsuccess = function(e) {
                    const db = e.target.result;
                    const transaction = db.transaction(STORE_NAME, 'readwrite');
                    const store = transaction.objectStore(STORE_NAME);
                    store.delete(oldStateKey);
                    transaction.oncomplete = function() {
                        window.location.reload();
                    };
                };
            };
        };
    </script>
</body>
</html>
