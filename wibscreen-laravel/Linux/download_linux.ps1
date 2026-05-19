# PowerShell Downloader Script for Wibscreen Linux VM Files
$ProgressPreference = 'SilentlyContinue'

$LinuxDir = $PSScriptRoot
Write-Host "Creating Linux VM assets directory in: $LinuxDir"

# 1. Download Tiny Core Linux ISO (approx. 17 MB - ultra lightweight)
$TinyCoreUrl = "http://tinycorelinux.net/15.x/x86/release/Core-current.iso"
$TinyCoreDest = Join-Path $LinuxDir "tinycore.iso"

if (-not (Test-Path $TinyCoreDest)) {
    Write-Host "Downloading Tiny Core Linux ISO from $TinyCoreUrl..."
    Invoke-WebRequest -Uri $TinyCoreUrl -OutFile $TinyCoreDest -UserAgent "Mozilla/5.0"
    Write-Host "Tiny Core Linux ISO downloaded successfully: $TinyCoreDest"
} else {
    Write-Host "Tiny Core Linux ISO already exists, skipping download."
}

# 2. Download v86 client-side emulator scripts and BIOS files
$Assets = @{
    "libv86.js"   = "https://cdn.jsdelivr.net/npm/v86@latest/build/libv86.js"
    "v86.wasm"    = "https://cdn.jsdelivr.net/npm/v86@latest/build/v86.wasm"
    "seabios.bin" = "https://cdn.jsdelivr.net/npm/v86@latest/bios/seabios.bin"
    "vgabios.bin" = "https://cdn.jsdelivr.net/npm/v86@latest/bios/vgabios.bin"
}

foreach ($Key in $Assets.Keys) {
    $DestPath = Join-Path $LinuxDir $Key
    if (-not (Test-Path $DestPath)) {
        Write-Host "Downloading $Key..."
        Invoke-WebRequest -Uri $Assets[$Key] -OutFile $DestPath
    } else {
        Write-Host "$Key already exists, skipping."
    }
}

Write-Host "Linux environment assets setup complete!"
