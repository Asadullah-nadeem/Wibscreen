# Implementation Plan - Custom WibOS Bootloader & C Kernel Rebuild

This plan outlines the system design, architecture, and step-by-step changes to clean up unused legacy OS images and create a custom x86 bootloader and C kernel. The new OS will boot inside the browser-based v86 WebAssembly emulator, featuring a centered ASCII art logo, dynamic boot logs, a live system status clock (reading from the CMOS RTC), and theme switching.

---

## Proposed Changes

### Clean Up Unused Assets
We will delete the following unused folders and large files:
* [DELETE] `Linux/alpine/` (contains 49MB `alpine.iso`)
* [DELETE] `Linux/tinycore/` (contains 18MB `tinycore.iso`)
* [DELETE] `Linux/tinycore-gui/` (contains 25MB `tinycore-gui.iso`)
* [DELETE] `Linux/command/` (contains unused `info.txt`)
* [DELETE] `Linux/wasm_native/` (contains unused wasm examples)

---

### View Update

#### [MODIFY] [emulator.blade.php](file:///c:/xampp/htdocs/Wibscreen/resources/views/pages/emulator.blade.php)
* Simplify UI header, removing the OS selector dropdown since we are focusing on WibOS.
* Clean up the JS loading script to only fetch `wibos.img` and bypass unused OS conditions.

---

### Bootloader & Kernel Rebuild

#### [MODIFY] [boot.asm](file:///c:/xampp/htdocs/Wibscreen/Linux/src/boot.asm)
* Streamline the 16-bit startup logs.
* Ensure accurate sector reading for the 32-bit kernel.

#### [MODIFY] [kernel.c](file:///c:/xampp/htdocs/Wibscreen/Linux/src/kernel.c)
We will completely rebuild `kernel.c` with the following premium features:
1. **Centered ASCII Art Logo:** A styled logo representing "WibOS" positioned at the top center of the screen using VGA coordinate math.
2. **Top Status Bar:** Displaying the operating system name, a live clock reading from CMOS RTC (using BCD conversion), and system uptime in seconds.
3. **Dynamic Boot Logs:** An elegant, staggered log print on startup simulating system initialization steps (memory test, clock config, shell mount) with colored tags `[OK]`.
4. **Bottom Actions Bar:** Guiding the user on hotkeys (`[ESC] Screensaver`, `[color] Cycle Themes`, `[reboot] Restart`).

---

## Verification Plan

### Manual Verification
1. Open the x86 Emulator page in the browser: `http://localhost:8000/linux-vm` (or via the dashboard).
2. Verify that the BIOS POST boots successfully, fades out, and loads the new custom WibOS image.
3. Verify that the screen layout displays:
   - The top status bar with the live clock (updating seconds) and uptime counter.
   - The centered "WibOS" ASCII logo.
   - The dynamic startup boot logs.
   - The interactive `wibos> ` prompt.
4. Test commands: `help`, `about`, `clear`, `matrix`, `beep`, `color`, `reboot`, and `shutdown`.
