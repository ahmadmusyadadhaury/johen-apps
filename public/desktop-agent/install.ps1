# JohenHR Desktop Notifier - Installer
# Memasang agent notifikasi agar berjalan otomatis setiap login,
# lalu langsung menjalankannya.
#
# Pemakaian:
#   powershell -ExecutionPolicy Bypass -File install.ps1 -ServerUrl http://192.168.x.x -Token <TOKEN>

param(
    [Parameter(Mandatory = $true)][string]$ServerUrl,
    [Parameter(Mandatory = $true)][string]$Token
)

$ErrorActionPreference = 'Stop'

$ServerUrl = $ServerUrl.TrimEnd('/')
$configDir = Join-Path $env:LOCALAPPDATA 'JohenHR'
New-Item -ItemType Directory -Path $configDir -Force | Out-Null

$configFile = Join-Path $configDir 'config.json'
@{ ServerUrl = $ServerUrl; Token = $Token } | ConvertTo-Json | Set-Content -Path $configFile -Encoding UTF8

# Siapkan notifier.ps1 di folder config
$localNotifier = Join-Path $configDir 'notifier.ps1'
$srcNotifier = if ($PSScriptRoot) { Join-Path $PSScriptRoot 'notifier.ps1' } else { '' }
if ($srcNotifier -and (Test-Path $srcNotifier)) {
    Copy-Item -Path $srcNotifier -Destination $localNotifier -Force
} else {
    Invoke-WebRequest -Uri "$ServerUrl/desktop-agent/notifier.ps1" -OutFile $localNotifier -UseBasicParsing
}

# Daftarkan task berjalan saat logon
$taskName = 'JohenHRDesktopNotifier'
$action = New-ScheduledTaskAction -Execute 'powershell.exe' -Argument "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$localNotifier`""
$trigger = New-ScheduledTaskTrigger -AtLogOn
$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable
Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Force | Out-Null

Start-ScheduledTask -TaskName $taskName

Write-Host ''
Write-Host 'Notifikasi Desktop JohenHR berhasil dipasang dan sedang berjalan.'
Write-Host "Server : $ServerUrl"
Write-Host 'Task   : JohenHRDesktopNotifier (otomatis saat login)'
Write-Host 'Catatan: Pastikan PC terhubung ke jaringan kantor yang sama.'
Write-Host ''