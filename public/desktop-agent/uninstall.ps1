# JohenHR Desktop Notifier - Uninstaller
# Menghapus task, config, dan notifier dari PC.

$ErrorActionPreference = 'SilentlyContinue'

$taskName = 'JohenHRDesktopNotifier'
if (Get-ScheduledTask -TaskName $taskName) {
    Stop-ScheduledTask -TaskName $taskName
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    Write-Host 'Task dihapus.'
}

$configDir = Join-Path $env:LOCALAPPDATA 'JohenHR'
if (Test-Path $configDir) {
    Remove-Item -Path $configDir -Recurse -Force
    Write-Host 'Konfigurasi dihapus.'
}

Write-Host 'Notifikasi Desktop JohenHR telah dihapus dari PC ini.'
Write-Host ''