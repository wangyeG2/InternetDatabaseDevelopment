# 从 frontend/runtime/mail 下最新一封激活邮件中提取可用链接
$ErrorActionPreference = 'Stop'
$repoRoot = Split-Path -Parent $PSScriptRoot              # tools 的上一级 = 仓库根
$mailDir  = Join-Path $repoRoot 'advanced\frontend\runtime\mail'

if (-not (Test-Path $mailDir)) {
    Write-Host "[!] 未找到邮件目录：$mailDir" -ForegroundColor Yellow
    Write-Host "    请先在前台完成注册（激活邮件在注册后才会生成）。"
    exit 1
}

$eml = Get-ChildItem "$mailDir\*.eml" -ErrorAction SilentlyContinue |
       Sort-Object LastWriteTime -Descending |
       Select-Object -First 1                              # 只取最新一封

if (-not $eml) {
    Write-Host "[!] 邮件目录为空：$mailDir" -ForegroundColor Yellow
    Write-Host "    请先在前台完成注册。"
    exit 1
}

# 1) 去 quoted-printable 软换行  2) 解码 =XX 十六进制  3) 还原 &amp;
$raw   = Get-Content $eml.FullName -Raw
$flat  = $raw -replace "=\r?\n", ""
$plain = [regex]::Replace($flat, "=([0-9A-Fa-f]{2})", { param($m) [char][Convert]::ToInt32($m.Groups[1].Value, 16) })
$plain = $plain -replace '&amp;', '&'

$url = [regex]::Match($plain, 'http://[^\s"<>]+').Value

if (-not $url) {
    Write-Host "[!] 未能从邮件中提取链接：$($eml.Name)" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "邮件文件：$($eml.Name)  （$($eml.LastWriteTime)）"
Write-Host ""
Write-Host "激活链接（完整复制到浏览器打开）：" -ForegroundColor Green
Write-Host $url
Write-Host ""
Write-Host "提示：打开链接后看到 Your email has been confirmed! 且自动登录，即激活成功。"
