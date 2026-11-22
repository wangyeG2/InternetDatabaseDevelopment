# Extract the activation link from the newest signup email (quoted-printable decoded)
$ErrorActionPreference = 'Stop'
$repoRoot = Split-Path -Parent $PSScriptRoot
$mailDir  = Join-Path $repoRoot 'advanced\frontend\runtime\mail'

if (-not (Test-Path -LiteralPath $mailDir)) {
    Write-Host "[!] Mail folder not found: $mailDir" -ForegroundColor Yellow
    Write-Host "    Please register an account on the frontend first."
    exit 1
}

$mailFiles = @(Get-ChildItem -LiteralPath $mailDir -Filter '*.eml' -File -ErrorAction SilentlyContinue)

if ($mailFiles.Count -eq 0) {
    Write-Host "[!] No .eml files in: $mailDir" -ForegroundColor Yellow
    Write-Host "    Please register first (runtime mail is generated on signup)."
    exit 1
}

$emlPath = ($mailFiles | Sort-Object LastWriteTime -Descending | Select-Object -First 1).FullName

if ([string]::IsNullOrWhiteSpace($emlPath) -or -not (Test-Path -LiteralPath $emlPath)) {
    Write-Host "[!] Unexpected mail file path: $emlPath" -ForegroundColor Yellow
    exit 1
}

$raw   = Get-Content -LiteralPath $emlPath -Raw
$flat  = $raw -replace "=\r?\n", ""
$plain = [regex]::Replace($flat, "=([0-9A-Fa-f]{2})", { param($m) [char][Convert]::ToInt32($m.Groups[1].Value, 16) })
$plain = $plain -replace '&amp;', '&'

$url = [regex]::Match($plain, 'http://[^\s"<>]+').Value

if ([string]::IsNullOrWhiteSpace($url)) {
    Write-Host "[!] No link found in: $(Split-Path -Leaf $emlPath)" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "Mail file: $(Split-Path -Leaf $emlPath)  ($((Get-Item -LiteralPath $emlPath).LastWriteTime))"
Write-Host ""
Write-Host "Activation link (copy to browser):" -ForegroundColor Green
Write-Host $url
Write-Host ""
Write-Host "Open it; 'Your email has been confirmed!' means success."
