# JohenHR Desktop Notifier
# Berjalan di background (Task Scheduler, saat logon), polling server
# dan menampilkan notifikasi Windows (toast) untuk evaluasi baru.
#
# Konfigurasi dibaca dari: %LOCALAPPDATA%\JohenHR\config.json
#   { "ServerUrl": "http://192.168.x.x", "Token": "<desktop_token>" }

param(
    [string]$ServerUrl = '',
    [string]$Token = '',
    [int]$IntervalSeconds = 30
)

$ErrorActionPreference = 'SilentlyContinue'

$configDir = Join-Path $env:LOCALAPPDATA 'JohenHR'
$configFile = Join-Path $configDir 'config.json'
$stateFile = Join-Path $configDir 'state.json'

if (-not $ServerUrl -or -not $Token) {
    if (Test-Path $configFile) {
        try {
            $cfg = Get-Content $configFile -Raw | ConvertFrom-Json
            if (-not $ServerUrl) { $ServerUrl = $cfg.ServerUrl }
            if (-not $Token) { $Token = $cfg.Token }
        } catch { }
    }
}

if (-not $ServerUrl -or -not $Token) {
    Write-Error 'ServerUrl dan Token wajib diisi. Jalankan install.ps1 terlebih dahulu.'
    exit 1
}

$ServerUrl = $ServerUrl.TrimEnd('/')

$lastId = 0
if (Test-Path $stateFile) {
    try {
        $st = Get-Content $stateFile -Raw | ConvertFrom-Json
        $lastId = [int]$st.LastId
    } catch { $lastId = 0 }
}

function Show-Notification {
    param([string]$Title, [string]$Body)
    try {
        [Windows.UI.Notifications.ToastNotificationManager, Windows.UI.Notifications, ContentType = WindowsRuntime] | Out-Null
        [Windows.Data.Xml.Dom.XmlDocument, Windows.Data.Xml.Dom.XmlDocument, ContentType = WindowsRuntime] | Out-Null
        $template = [Windows.UI.Notifications.ToastNotificationManager]::GetTemplateContent([Windows.UI.Notifications.ToastTemplateType]::ToastText02)
        $textNodes = $template.GetElementsByTagName('text')
        if ($textNodes.Length -gt 0) {
            $textNodes.Item(0).AppendChild($template.CreateTextNode($Title)) | Out-Null
        }
        if ($textNodes.Length -gt 1) {
            $textNodes.Item(1).AppendChild($template.CreateTextNode($Body)) | Out-Null
        }
        $toast = New-Object Windows.UI.Notifications.ToastNotification -ArgumentList $template
        $notifier = [Windows.UI.Notifications.ToastNotificationManager]::CreateToastNotifier('JohenHR.DesktopNotifier')
        $notifier.Show($toast)
    } catch {
        Write-Output "TOAST FAIL: $($_.Exception.Message)"
    }
}

function Persist-State {
    try {
        if (-not (Test-Path $configDir)) { New-Item -ItemType Directory -Path $configDir -Force | Out-Null }
        @{ LastId = $lastId } | ConvertTo-Json | Set-Content -Path $stateFile -Encoding UTF8
    } catch { }
}

Write-Output "JohenHR Desktop Notifier berjalan. Server: $ServerUrl"
Persist-State

while ($true) {
    try {
        $uri = "$ServerUrl/api/desktop/notifications?token=$Token&after=$lastId"
        $resp = Invoke-RestMethod -Uri $uri -Method Get -TimeoutSec 15
        $items = @($resp.notifications)
        foreach ($n in $items) {
            Show-Notification -Title $n.title -Body $n.body
            if ([int]$n.id -gt $lastId) { $lastId = [int]$n.id }
        }
        if ($items.Count -gt 0) { Persist-State }
    } catch {
        # server sedang tidak terjangkau -> coba lagi nanti
    }
    Start-Sleep -Seconds $IntervalSeconds
}