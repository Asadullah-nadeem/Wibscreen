# PowerShell Downloader Script for Wibscreen Linux VM Files
$ProgressPreference = 'SilentlyContinue'

$LinuxDir = $PSScriptRoot
Write-Host "Verifying Linux VM assets directory: $LinuxDir"

# 1. Download Tiny Core Linux ISO (approx. 17 MB - ultra lightweight)
$TinyCoreUrl = "http://tinycorelinux.net/15.x/x86/release/Core-current.iso"
$TinyCoreDest = Join-Path $LinuxDir "tinycore.iso"

if (-not (Test-Path $TinyCoreDest)) {
    Write-Host "Downloading Tiny Core Linux ISO from $TinyCoreUrl..."
    try {
        Invoke-WebRequest -Uri $TinyCoreUrl -OutFile $TinyCoreDest -UserAgent "Mozilla/5.0" -UseBasicParsing -TimeoutSec 300
        Write-Host "Tiny Core Linux ISO downloaded successfully: $TinyCoreDest"
    } catch {
        Write-Error "Failed to download Tiny Core Linux ISO - $_"
    }
} else {
    Write-Host "Tiny Core Linux ISO already exists, skipping download."
}

# 2. Download Alpine Linux Minimal Virt ISO (approx. 44 MB - lightweight standard Linux)
$AlpineUrl = "https://dl-cdn.alpinelinux.org/alpine/v3.20/releases/x86/alpine-virt-3.20.0-x86.iso"
$AlpineDest = Join-Path $LinuxDir "alpine.iso"

if (-not (Test-Path $AlpineDest)) {
    Write-Host "Downloading Alpine Linux ISO from $AlpineUrl..."
    try {
        Invoke-WebRequest -Uri $AlpineUrl -OutFile $AlpineDest -UserAgent "Mozilla/5.0" -UseBasicParsing -TimeoutSec 400
        Write-Host "Alpine Linux ISO downloaded successfully: $AlpineDest"
    } catch {
        Write-Error "Failed to download Alpine Linux ISO - $_"
    }
} else {
    Write-Host "Alpine Linux ISO already exists, skipping download."
}

# 3. Download v86 client-side emulator scripts and BIOS files
$Assets = [ordered]@{
    "libv86.js"   = "https://cdn.jsdelivr.net/npm/v86@latest/build/libv86.js"
    "v86.wasm"    = "https://cdn.jsdelivr.net/npm/v86@latest/build/v86.wasm"
    "seabios.bin" = "https://cdn.jsdelivr.net/npm/v86@latest/bios/seabios.bin"
    "vgabios.bin" = "https://cdn.jsdelivr.net/npm/v86@latest/bios/vgabios.bin"
}

foreach ($Key in $Assets.Keys) {
    $DestPath = Join-Path $LinuxDir $Key
    if (-not (Test-Path $DestPath)) {
        Write-Host "Downloading $Key..."
        try {
            Invoke-WebRequest -Uri $Assets[$Key] -OutFile $DestPath -UserAgent "Mozilla/5.0" -UseBasicParsing -TimeoutSec 60
            Write-Host "$Key downloaded successfully."
        } catch {
            Write-Error "Failed to download ${Key} - $_"
        }
    } else {
        Write-Host "$Key already exists, skipping."
    }
}

Write-Host "Linux environment assets setup complete!"
