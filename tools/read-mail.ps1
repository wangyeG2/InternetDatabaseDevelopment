# 从 frontend/runtime/mail 下最新一封激活邮件中提取可用链接
$ErrorActionPreference = 'Stop'

$repoRoot = Split-Path -Parent $PSScriptRoot              # tools 的上一级 = 仓库根
$mailDir  = Join-Path $repoRoot 'advanced\frontend\runtime\mail'

if (-not (Test-Path -LiteralPath $mailDir)) {
    Write-Host "[!] 未找到邮件目录：$mailDir" -ForegroundColor Yellow
    Write-Host "    请先在前台完成注册（激活邮件在注册后才会生成）。"
    exit 1
}

# @() 强制数组：无匹配时 Count 为 0，彻底绕开 $null 判定的边界行为
$mailFiles = @(Get-ChildItem -LiteralPath $mailDir -Filter '*.eml' -File -ErrorAction SilentlyContinue)

if ($mailFiles.Count -eq 0) {
    Write-Host "[!] 邮件目录中没有 .eml 文件：$mailDir" -ForegroundColor Yellow
    Write-Host "    请先在前台完成注册；若之前注册过，可能 runtime 已被清理，重新注册即可。"
    exit 1
}

# 路径先落成字符串再做空值拦截，不再对对象做属性导航
$emlPath = ($mailFiles | Sort-Object LastWriteTime -Descending |
            Select-Object -First 1).FullName

if ([string]::IsNullOrWhiteSpace($emlPath) -or -not (Test-Path -LiteralPath $emlPath)) {
    Write-Host "[!] 邮件文件路径异常：$emlPath" -ForegroundColor Yellow
    exit 1
}

# 解码 quoted-printable：去软换行 → 解 =XX → 还原 &amp;
$raw   = Get-Content -LiteralPath $emlPath -Raw
$flat  = $raw -replace "=\r?\n", ""
$plain = [regex]::Replace($flat, "=([0-9A-Fa-f]{2})", { param($m) [char][Convert]::ToInt32($m.Groups[1].Value, 16) })
$plain = $plain -replace '&amp;', '&'

$url = [regex]::Match($plain, 'http://[^\s"<>]+').Value

if ([string]::IsNullOrWhiteSpace($url)) {
    Write-Host "[!] 未能从邮件中提取链接：$(Split-Path -Leaf $emlPath)" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "邮件文件：$(Split-Path -Leaf $emlPath)  （$((Get-Item -LiteralPath $emlPath).LastWriteTime)）"
Write-Host ""
Write-Host "激活链接（完整复制到浏览器打开）：" -ForegroundColor Green
Write-Host $url
Write-Host ""
Write-Host "提示：打开链接后看到 Your email has been confirmed! 且自动登录，即激活成功。"
