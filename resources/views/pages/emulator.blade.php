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
    </style>
    <script src="/linux/libv86.js"></script>
</head>
<body>
    <div id="header">
        <h1><i class="fas fa-microchip text-success"></i> WibOS Custom x86 VM</h1>
        <div style="display: flex; align-items: center; gap: 12px;">
            <label for="os-select" style="font-size: 0.8rem; font-family: system-ui, -apple-system, sans-serif; color: #9ca3af;">Select OS:</label>
            <select id="os-select" style="background-color: #1f2937; color: white; border: 1px solid #374151; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; outline: none; cursor: pointer; font-family: system-ui, -apple-system, sans-serif;">
                <option value="wibos">WibOS (Custom C/C++ OS)</option>
                <option value="tinycore">Tiny Core Linux (Command-line - 17MB)</option>
                <option value="tinycore-gui">Tiny Core Linux GUI (Graphical Desktop - 24MB)</option>
                <option value="alpine">Alpine Linux (Minimal Virt - 44MB)</option>
            </select>
            <button class="btn" id="btn-restart"><i class="fas fa-redo"></i> Restart VM</button>
            <button class="btn" id="btn-fullscreen"><i class="fas fa-expand"></i> Fullscreen</button>
        </div>
    </div>
    
    <div id="container">
        <!-- Emulator Container -->
        <div id="emulator-screen">
            <!-- Retro BIOS Loading Screen Overlay (Initially Hidden) -->
            <div id="bios-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: #000; color: #34d399; font-family: 'Courier New', Courier, monospace; padding: 30px; display: none; flex-direction: column; justify-content: flex-start; z-index: 10; font-size: 14px; line-height: 1.5; text-align: left; box-sizing: border-box;">
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #34d399; padding-bottom: 5px; margin-bottom: 15px; font-weight: bold;">
                    <span>WIBSCREEN BIOS v1.0.4</span>
                    <span>(C) 2026 Wibscreen Inc.</span>
                </div>
                <div id="bios-content" style="white-space: pre-wrap;"></div>
            </div>
            <div id="screen_container" tabindex="0"></div>
        </div>
    </div>



    <script>
        const DB_NAME = 'WibscreenVM';
        const STORE_NAME = 'States';
        const CACHE_NAME = 'wibscreen-v86-cache';

        // Sanitize Selected OS to prevent blank selections or caching bugs
        let selectedOS = localStorage.getItem('wibscreen_selected_os') || 'wibos';
        const validOSList = ['wibos', 'tinycore', 'tinycore-gui', 'alpine'];
        if (!validOSList.includes(selectedOS)) {
            selectedOS = 'wibos';
            localStorage.setItem('wibscreen_selected_os', 'wibos');
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
                        getReq.onerror = function() {
                            resolve(null);
                        };
                    };
                    request.onerror = function() {
                        resolve(null);
                    };
                } catch(e) {
                    resolve(null);
                }
            });
        }

        function saveStateToDB(buffer) {
            return new Promise((resolve) => {
                try {
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
                    request.onerror = function() {
                        resolve(false);
                    };
                } catch(e) {
                    resolve(false);
                }
            });
        }

        function clearSavedState() {
            return new Promise((resolve) => {
                try {
                    const request = indexedDB.open(DB_NAME, 1);
                    request.onsuccess = function(e) {
                        const db = e.target.result;
                        const transaction = db.transaction(STORE_NAME, 'readwrite');
                        const store = transaction.objectStore(STORE_NAME);
                        store.delete(STATE_KEY);
                        transaction.oncomplete = function() {
                            resolve(true);
                        };
                        transaction.onerror = function() {
                            resolve(false);
                        };
                    };
                    request.onerror = function() {
                        resolve(false);
                    };
                } catch (e) {
                    resolve(false);
                }
            });
        }

        // Keyboard Scancodes Mapping (US Set 1) for automated shells
        const scanCodes = {
            'a': 0x1E, 'b': 0x30, 'c': 0x2E, 'd': 0x20, 'e': 0x12, 'f': 0x21, 'g': 0x22, 'h': 0x23, 'i': 0x17, 'j': 0x24, 'k': 0x25, 'l': 0x26, 'm': 0x32, 'n': 0x31, 'o': 0x18, 'p': 0x19, 'q': 0x10, 'r': 0x13, 's': 0x1F, 't': 0x14, 'u': 0x16, 'v': 0x2F, 'w': 0x11, 'x': 0x2D, 'y': 0x15, 'z': 0x2C,
            '0': 0x0B, '1': 0x02, '2': 0x03, '3': 0x04, '4': 0x05, '5': 0x06, '6': 0x07, '7': 0x08, '8': 0x09, '9': 0x0A,
            ' ': 0x39, '=': 0x0D, '-': 0x0C, '_': 0x0C, '+': 0x0D, '@': 0x03, ':': 0x27, '~': 0x29, '$': 0x05, '\\': 0x2B,
            '\n': 0x1C
        };

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

        // Retro BIOS POST simulated boot animation
        function runBiosPOST(osLabel, osFilename, isWibOS, callback) {
            const overlay = document.getElementById("bios-overlay");
            const content = document.getElementById("bios-content");
            overlay.style.display = "flex";
            overlay.style.opacity = "1";
            content.innerText = "";
            
            const lines = [
                "CPU: Intel(R) Core(TM) i7-10700 Processor @ 3.20GHz",
                "Processor Cores: 1 Physical Core, 1 Logical Thread (x86 WebAssembly VM)",
                "FPU: 80387 Floating-Point Co-processor Integrated",
                "",
                "Memory Test: 0 KB",
                "",
                "Searching for bootable media storage...",
                isWibOS ? "  Floppy Drive A  : [" + osFilename + "] Floppy Disk Boot Sector" : "  CD-ROM Drive D  : [" + osFilename + "] ISO 9660 Volume",
                "  Primary Master  : None",
                "  Primary Slave   : None",
                "",
                "Initial Boot Strap Loader loaded successfully.",
                isWibOS ? "Booting from Floppy Drive A..." : "Booting from CD-ROM Drive D..."
            ];

            let lineIndex = 0;
            let currentText = "";
            
            function printNextLine() {
                if (lineIndex >= lines.length) {
                    // Wait a bit, then fade out the BIOS screen and boot the emulator
                    setTimeout(() => {
                        overlay.style.transition = "opacity 0.4s ease-out";
                        overlay.style.opacity = "0";
                        setTimeout(() => {
                            overlay.style.display = "none";
                            callback();
                        }, 400);
                    }, 600);
                    return;
                }

                const line = lines[lineIndex];
                if (line.startsWith("Memory Test:")) {
                    let currentMemory = 0;
                    let maxMemory = 65536; // 64MB Default
                    if (selectedOS === 'wibos') maxMemory = 16384;      // 16MB
                    if (selectedOS === 'tinycore-gui') maxMemory = 131072; // 128MB
                    
                    const interval = setInterval(() => {
                        currentMemory += 4096;
                        if (currentMemory >= maxMemory) {
                            currentMemory = maxMemory;
                            clearInterval(interval);
                            content.innerText = currentText + "Memory Test: " + currentMemory + " KB OK\n";
                            currentText = content.innerText;
                            lineIndex++;
                            setTimeout(printNextLine, 100);
                        } else {
                            content.innerText = currentText + "Memory Test: " + currentMemory + " KB\n";
                        }
                    }, 15);
                } else {
                    content.innerText = currentText + line + "\n";
                    currentText = content.innerText;
                    lineIndex++;
                    const delay = (line === "") ? 80 : 150;
                    setTimeout(printNextLine, delay);
                }
            }

            printNextLine();
        }

        // Helper to send input keys
        function sendKey(emulator, code, isShift = false) {
            if (!emulator) return;
            if (isShift) {
                emulator.bus.send("keyboard-code", 0x2A); // Shift Press
            }
            emulator.bus.send("keyboard-code", code);
            emulator.bus.send("keyboard-code", code | 0x80); // Release
            if (isShift) {
                emulator.bus.send("keyboard-code", 0x2A | 0x80); // Shift Release
            }
        }

        function sendString(emulator, str, delay = 25) {
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
                        sendKey(emulator, code, isShift);
                    }
                    index++;
                    setTimeout(next, delay);
                }
                next();
            });
        }

        function attachEmulatorListeners(emulator, isWibOS, isRestored, username, statusText) {
            emulator.add_listener("emulator-ready", async function() {
                if (isWibOS || selectedOS === 'tinycore-gui') {
                    statusText.innerText = isWibOS ? "WibOS Custom C/C++ VM Booted Instantly!" : "Tiny Core GUI Desktop Loaded successfully!";
                    return;
                }
                if (isRestored) {
                    statusText.innerText = "Restoring session for " + username + "...";
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
                        await sendString(emulator, cmd);
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
                            
                            let setupCommands = [];
                            if (selectedOS === 'alpine') {
                                setupCommands = [
                                    `root\n`,
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
                                await sendString(emulator, cmd);
                                await new Promise(r => setTimeout(r, 450));
                            }
                            
                            statusText.innerText = "VM Booted & configured for: " + username;
                        });
                    }, bootTimeout);
                }
            });
        }

        window.onload = async function() {
            const statusText = { set innerText(val) { console.log("[Status]:", val); } };
            const rawUsername = "{{ auth()->user()->name }}";
            const username = rawUsername.toLowerCase().replace(/[^a-z0-9]/g, '') || 'wibuser';

            // Set the Select dropdown value safely inside onload
            document.getElementById('os-select').value = selectedOS;

            let isoFilename = "wibos/wibos.img";
            let isoLabel = "WibOS Bootable Image";
            if (selectedOS === 'tinycore') {
                isoFilename = "tinycore/tinycore.iso";
                isoLabel = "Tiny Core Linux ISO";
            } else if (selectedOS === 'tinycore-gui') {
                isoFilename = "tinycore-gui/tinycore-gui.iso";
                isoLabel = "Tiny Core GUI Desktop ISO";
            } else if (selectedOS === 'alpine') {
                isoFilename = "alpine/alpine.iso";
                isoLabel = "Alpine Linux ISO";
            }

            statusText.innerText = "Locating VM engine in Cache Storage...";
            const wasmUrl = await getCacheStorageAssetUrl("WebAssembly Engine", "/linux/v86.wasm", statusText);
            const biosUrl = await getCacheStorageAssetUrl("System BIOS", "/linux/seabios.bin", statusText);
            const vgaBiosUrl = await getCacheStorageAssetUrl("VGA BIOS", "/linux/vgabios.bin", statusText);
            const isoUrl = await getCacheStorageAssetUrl(isoLabel, "/linux/" + isoFilename, statusText);

            const isWibOS = (selectedOS === 'wibos');

            statusText.innerText = "Checking cache for boot state...";
            // We only cache states for the text-mode Linux OS options
            const needsStateCheck = (selectedOS === 'tinycore' || selectedOS === 'alpine');
            const savedState = needsStateCheck ? await getSavedState() : null;
            const isRestored = !!savedState;

            let memSize = 64 * 1024 * 1024; // Default 64MB
            let vgaMemSize = 2 * 1024 * 1024;
            if (selectedOS === 'wibos') {
                memSize = 16 * 1024 * 1024;
            } else if (selectedOS === 'tinycore-gui') {
                memSize = 128 * 1024 * 1024;
                vgaMemSize = 8 * 1024 * 1024;
            }

            const config = {
                wasm_path: wasmUrl,
                memory_size: memSize,
                vga_memory_size: vgaMemSize,
                screen_container: document.getElementById("screen_container"),
                bios: { url: biosUrl },
                vga_bios: { url: vgaBiosUrl },
                autostart: true,
                cmdline: "tsc=reliable rcupdate.rcu_expedited=1 mitigations=off rw"
            };

            if (isWibOS) {
                config.fda = { url: isoUrl };
            } else {
                config.cdrom = { url: isoUrl };
            }

            if (isRestored) {
                config.state = { buffer: savedState };
            }

            // Run simulated BIOS POST screen, then start the V86 emulator!
            runBiosPOST(isoLabel, isoFilename, isWibOS, function() {
                statusText.innerText = isRestored ? "Restoring cached VM state..." : "Booting OS kernel...";
                const emulator = new V86Starter(config);
                attachEmulatorListeners(emulator, isWibOS, isRestored, username, statusText);
            });

            document.getElementById("btn-restart").onclick = async function() {
                statusText.innerText = "Clearing cache & rebooting VM...";
                try {
                    await clearSavedState();
                } catch (e) {
                    console.error("IndexedDB clear failed: ", e);
                }
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
