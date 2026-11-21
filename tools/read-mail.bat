@echo off
rem 提取最新激活邮件中的可用链接（内部调用 read-mail.ps1）
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0read-mail.ps1"
pause
