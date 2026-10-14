@if(! $embedded)
@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Weekly Meeting</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Scan QR Code untuk absensi rapat mingguan</p>
    </div>
@endpush
@endif

<div x-data x-on:weekly-meeting-scan-success.window="$store.successModal.show($event.detail.message)" class="space-y-4 max-w-xl mx-auto">
<style>
    @keyframes qrPop { 0% { transform: scale(0.4); opacity: 0; } 60% { transform: scale(1.08); } 100% { transform: scale(1); opacity: 1; } }
    @keyframes qrCheckDraw { from { stroke-dashoffset: 36; } to { stroke-dashoffset: 0; } }
    @keyframes qrRing { 0% { box-shadow: 0 0 0 0 rgba(16,185,129,0.45); } 70% { box-shadow: 0 0 0 22px rgba(16,185,129,0); } 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); } }
    @keyframes qrPopIn { 0% { transform: scale(0.85); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    .animate-scan-ring { animation: qrRing 1.9s ease-out infinite; }
    .animate-scan-check { animation: qrPop 0.5s cubic-bezier(0.34,1.56,0.64,1) both; }
    .animate-scan-check .check-path { stroke-dasharray: 36; stroke-dashoffset: 36; animation: qrCheckDraw 0.55s ease-out 0.22s forwards; }
    .qr-scan-guide { position: absolute; z-index: 10; top: 50%; left: 50%; width: min(76%, 20rem); aspect-ratio: 1; transform: translate(-50%, -50%); border: 2px solid rgba(255,255,255,.78); border-radius: 1rem; box-shadow: 0 0 0 1px rgba(15,23,42,.28), 0 0 22px rgba(15,23,42,.3); pointer-events: none; }
    .qr-scan-guide-corner { position: absolute; width: 1.5rem; height: 1.5rem; border-color: #34d399; border-style: solid; }
    .qr-scan-guide-corner--tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; border-top-left-radius: .9rem; }
    .qr-scan-guide-corner--tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; border-top-right-radius: .9rem; }
    .qr-scan-guide-corner--bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; border-bottom-left-radius: .9rem; }
    .qr-scan-guide-corner--br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; border-bottom-right-radius: .9rem; }
</style>
    @if(!$currentMeeting)
    {{-- No Active Meeting --}}
    <div class="card">
        <div class="p-12 text-center">
            <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Belum ada weekly meeting</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Jadwal belum tersedia atau waktu rapat telah berakhir.</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Silakan hubungi Admin Master untuk membuat jadwal rapat baru.</p>
            <button type="button" id="weekly-face-register" class="btn-secondary text-xs mt-5">Daftarkan Wajah untuk Absen Mendatang</button>
        </div>
    </div>

    @elseif($alreadyAttended)
    {{-- Already Attended --}}
    <div class="card">
        <div class="p-12 text-center">
            <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-green-50 dark:bg-green-900/20 mx-auto mb-4 {{ $status === 'success' ? '' : 'animate-scan-ring' }}">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400 {{ $status === 'success' ? '' : 'animate-scan-check' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path class="{{ $status === 'success' ? '' : 'check-path' }}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Absensi Berhasil!</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $message }}</p>
            @if($currentMeeting)
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $currentMeeting->title }}</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $currentMeeting->meeting_date->format('d F Y') }} | {{ $currentMeeting->start_time?->format('H:i') ?? '-' }} - {{ $currentMeeting->end_time?->format('H:i') ?? '-' }}</p>
            </div>
            @endif
            <button wire:click="resetScanner" class="btn-primary text-xs mt-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Scan Ulang / Selesai
            </button>
        </div>
    </div>

    @else
    {{-- Scanner View --}}
    <div class="card">
        <div class="p-4 sm:p-6">
            {{-- Meeting Info --}}
            <div class="mb-6 text-center">
                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Presensi Weekly Meeting</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $currentMeeting->meeting_date->format('d F Y') }}</p>
            </div>

            {{-- Status Message --}}
            @if($message)
            <div class="mb-6 p-4 rounded-xl border-l-4
                @if($status === 'success')
                    bg-green-50 dark:bg-green-900/20 border-green-500 text-green-800 dark:text-green-300
                @elseif($status === 'error')
                    bg-red-50 dark:bg-red-900/20 border-red-500 text-red-800 dark:text-red-300
                @else
                    bg-blue-50 dark:bg-blue-900/20 border-blue-500 text-blue-800 dark:text-blue-300
                @endif
                flex items-start gap-3">
                @if($status === 'success')
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                @elseif($status === 'error')
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
                <p class="text-sm">{{ $message }}</p>
            </div>
            @endif

            {{-- Face attendance --}}
            <div class="mb-6 rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50/50 dark:bg-blue-950/20 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Absen dengan Face ID + Lokasi</h5>
                        @if($currentMeeting->latitude === null || $currentMeeting->longitude === null || ! $currentMeeting->radius_meters)
                            <p class="mt-1 text-xs text-amber-700 dark:text-amber-300">Admin belum mengatur titik koordinat dan radius untuk rapat ini.</p>
                        @else
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Verifikasi wajah melalui kamera dan pastikan berada dalam radius {{ $currentMeeting->radius_meters }} m.</p>
                        @endif
                        <p id="weekly-face-status" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Face ID didaftarkan satu kali pada akun Anda.</p>
                    </div>
                    <div class="flex w-full sm:w-auto flex-wrap gap-2 shrink-0">
                        <button type="button" id="weekly-face-register" class="btn-secondary flex-1 sm:flex-none text-xs whitespace-nowrap">Daftarkan Wajah</button>
                        <button type="button" id="weekly-face-attend" @disabled($currentMeeting->latitude === null || $currentMeeting->longitude === null || ! $currentMeeting->radius_meters) class="btn-primary flex-1 sm:flex-none text-xs whitespace-nowrap disabled:opacity-50">Absen Face ID</button>
                    </div>
                </div>
            </div>

            {{-- Camera Selection --}}
            @if(!$selectedCamera)
            <div class="mb-6 text-center">
                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <button type="button" @click="open = !open" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all px-5 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Pilih Kamera</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute left-1/2 -translate-x-1/2 z-20 mt-2 w-80 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">
                        <button type="button" wire:click="selectCamera('user')" @click="open = false" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Kamera Depan
                        </button>
                        <button type="button" wire:click="selectCamera('environment')" @click="open = false" class="flex w-full items-center gap-3 border-t border-gray-100 dark:border-gray-700 px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors whitespace-nowrap">
                            <svg class="w-5 h-5 shrink-0 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Kamera Belakang
                        </button>
                    </div>
                </div>
            </div>

            @else
            {{-- Camera Scanner --}}
            <div class="space-y-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Posisikan seluruh QR di dalam kotak panduan.</p>
                </div>

                <div id="scanner-container" class="relative">
                    <div id="scanner-frame" class="bg-gray-900 rounded-xl overflow-hidden relative" style="aspect-ratio: 3 / 4;">
                        <video id="scanner-video" class="w-full h-full object-contain" autoplay playsinline muted></video>
                        <div class="qr-scan-guide" aria-hidden="true">
                            <span class="qr-scan-guide-corner qr-scan-guide-corner--tl"></span>
                            <span class="qr-scan-guide-corner qr-scan-guide-corner--tr"></span>
                            <span class="qr-scan-guide-corner qr-scan-guide-corner--bl"></span>
                            <span class="qr-scan-guide-corner qr-scan-guide-corner--br"></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-3">
                    <button wire:click="resetScanner" class="btn-secondary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Ganti Kamera
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>


@script
<script>
(function () {
    if (window.__weeklyMeetingScanBound) return;
    window.__weeklyMeetingScanBound = true;

    let stream = null;
    let scanning = false;
    let lastScanAt = 0;
    let cameraRequestId = 0;
    let pausedUntil = 0;
    let cachedCoords = '';
    let cachedName = '';

    const faceRoutes = {
        status: @js(route('hris.weekly-meeting.face.status')),
        preflight: @js(route('hris.weekly-meeting.face.preflight')),
        register: @js(route('hris.weekly-meeting.face.register')),
        attend: @js(route('hris.weekly-meeting.attend.face')),
    };
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const api = async (url, body = null) => {
        const response = await fetch(url, { method: body ? 'POST' : 'GET', credentials: 'same-origin', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf }, body: body ? JSON.stringify(body) : undefined });
        const responseText = await response.text();
        let data;
        try { data = JSON.parse(responseText); }
        catch (_) {
            const title = responseText.match(/<title[^>]*>(.*?)<\/title>/is)?.[1]?.replace(/\s+/g, ' ').trim();
            throw new Error(`Server mengembalikan respons tidak valid (HTTP ${response.status})${title ? ': ' + title : '.'}`);
        }
        if (!response.ok) throw new Error(data.message || 'Permintaan gagal.');
        return data;
    };
    const showFaceMessage = (message, error = false) => {
        const box = document.createElement('div');
        box.className = 'mb-4 rounded-lg px-4 py-3 text-sm ' + (error ? 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-300' : 'bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-300');
        box.textContent = message;
        (document.getElementById('weekly-face-register') || document.getElementById('weekly-face-attend'))?.closest('.rounded-xl')?.before(box);
        setTimeout(() => box.remove(), 8000);
    };
    let humanPromise = null;
    function loadHuman() {
        if (window.Human?.Human || window.Human) return Promise.resolve(window.Human);
        if (!humanPromise) humanPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@vladmandic/human@3.3.6/dist/human.js';
            script.onload = () => resolve(window.Human);
            script.onerror = () => reject(new Error('Gagal memuat modul pengenalan wajah. Periksa koneksi internet.'));
            document.head.appendChild(script);
        });
        return humanPromise;
    }
    async function captureFace() {
        if (!navigator.mediaDevices?.getUserMedia) throw new Error('Kamera memerlukan HTTPS dan dukungan browser.');
        const overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;overflow-y:auto;background:rgba(15,23,42,.82);padding:max(16px,env(safe-area-inset-top)) max(16px,env(safe-area-inset-right)) max(16px,env(safe-area-inset-bottom)) max(16px,env(safe-area-inset-left))';
        const frameStyle = document.createElement('style');
        frameStyle.textContent = '.face-camera-stage{position:relative;width:100%;aspect-ratio:4/3;max-height:min(58vh,520px);max-height:min(58dvh,520px);overflow:hidden;border-radius:14px;background:linear-gradient(145deg,#0f172a,#111827)}.face-camera-video{display:block;width:100%;height:100%;object-fit:cover}.face-guide-oval{position:absolute;left:50%;top:48%;width:43%;height:66%;transform:translate(-50%,-50%);border:2px solid rgba(255,255,255,.94);border-radius:50%;box-shadow:0 0 0 3px rgba(37,99,235,.82),0 0 28px rgba(34,211,238,.62),inset 0 0 20px rgba(37,99,235,.14);pointer-events:none}.face-guide-oval:after{content:"";position:absolute;inset:7px;border:1px solid rgba(103,232,249,.6);border-radius:50%}.face-scan-line{position:absolute;left:27%;right:27%;top:18%;height:2px;border-radius:4px;background:#67e8f9;box-shadow:0 0 12px 3px rgba(34,211,238,.78);animation:faceScanMove 2.8s ease-in-out infinite alternate;pointer-events:none}@keyframes faceScanMove{from{top:18%;opacity:.55}to{top:76%;opacity:1}}@media(max-width:640px){.face-camera-stage{aspect-ratio:3/4}.face-guide-oval{width:58%;height:68%}.face-scan-line{left:22%;right:22%}}@media(prefers-reduced-motion:reduce){.face-scan-line{animation:none;top:48%}}';
        const panel = document.createElement('div');
        panel.style.cssText = 'width:min(100%,520px);max-height:calc(100vh - 32px);max-height:calc(100dvh - 32px);display:flex;flex-direction:column;gap:14px;padding:clamp(14px,3vw,22px);background:#fff;border:1px solid #dbeafe;border-radius:20px;box-shadow:0 24px 64px rgba(2,6,23,.42)';
        const titleRow = document.createElement('div');
        titleRow.style.cssText = 'display:flex;align-items:center;gap:12px;min-width:0';
        const brandMark = document.createElement('div');
        brandMark.setAttribute('aria-hidden', 'true');
        brandMark.style.cssText = 'display:grid;place-items:center;flex:0 0 42px;width:42px;height:42px;color:#fff;background:linear-gradient(145deg,#2563eb,#06b6d4);border-radius:13px;box-shadow:0 5px 14px rgba(37,99,235,.24)';
        brandMark.innerHTML = '<svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 19 6v5c0 4.7-2.9 8-7 10-4.1-2-7-5.3-7-10V6l7-3Z"/><path d="m9 12 2 2 4-4"/></svg>';
        const titleText = document.createElement('div');
        titleText.style.cssText = 'min-width:0';
        const heading = document.createElement('h2');
        heading.textContent = 'Verifikasi Wajah';
        heading.style.cssText = 'margin:0;color:#111827;font-size:clamp(16px,4vw,19px);font-weight:700;line-height:1.25';
        const subtitle = document.createElement('p');
        subtitle.textContent = 'Johen Application · Presensi aman';
        subtitle.style.cssText = 'margin:3px 0 0;color:#64748b;font-size:12px';
        titleText.append(heading, subtitle);
        titleRow.append(brandMark, titleText);
        const stage = document.createElement('div');
        stage.className = 'face-camera-stage';
        const video = document.createElement('video');
        video.autoplay = true; video.playsInline = true; video.muted = true;
        video.className = 'face-camera-video';
        const guide = document.createElement('div');
        guide.className = 'face-guide-oval';
        guide.setAttribute('aria-hidden', 'true');
        const scanLine = document.createElement('div');
        scanLine.className = 'face-scan-line';
        scanLine.setAttribute('aria-hidden', 'true');
        stage.append(video, guide, scanLine);
        const label = document.createElement('div');
        label.textContent = 'Meminta akses kamera...';
        label.setAttribute('role', 'status');
        label.setAttribute('aria-live', 'polite');
        label.style.cssText = 'padding:10px 13px;color:#1e40af;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;font-size:13px;line-height:1.45;overflow-wrap:anywhere';
        panel.append(titleRow, stage, label);
        overlay.append(panel);
        document.head.append(frameStyle);
        document.body.append(overlay);
        let media;
        try {
            media = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = media;
            await new Promise(resolve => video.onloadedmetadata = resolve);
            await video.play();
            label.textContent = 'Memuat model wajah. Mohon tunggu...';
            const HumanCtor = await loadHuman();
            const HumanClass = HumanCtor?.Human || HumanCtor?.default || HumanCtor;
            if (typeof HumanClass !== 'function') throw new Error('Modul pengenalan wajah tidak tersedia.');
            const human = new HumanClass({ modelBasePath: 'https://cdn.jsdelivr.net/npm/@vladmandic/human@3.3.6/models', face: { enabled: true, detector: { rotation: true, maxDetected: 2 }, mesh: { enabled: true }, description: { enabled: true }, antispoof: { enabled: true }, liveness: { enabled: true } }, body: { enabled: false }, hand: { enabled: false }, object: { enabled: false }, gesture: { enabled: false } });
            await human.load(); await human.warmup();
            label.textContent = 'Hadap kamera dengan pencahayaan cukup. Lepas masker/kacamata gelap dan berkedip.';
            const deadline = Date.now() + 30000;
            let samples = [];
            let faceSeen = false;
            let embeddingSeen = false;
            let livenessSeen = false;
            while (Date.now() < deadline && samples.length < 3) {
                const result = await human.detect(video);
                const face = result.face?.length === 1 ? result.face[0] : null;
                faceSeen ||= Boolean(face);
                embeddingSeen ||= Boolean(face?.embedding?.length === 1024);
                livenessSeen ||= Number(face?.real ?? 0) > 0.5 && Number(face?.live ?? 0) > 0.5;
                if (face?.embedding?.length === 1024 && Number(face.real ?? 0) > 0.5 && Number(face.live ?? 0) > 0.5) {
                    samples.push(face.embedding);
                    label.textContent = `Wajah terverifikasi (${samples.length}/3)...`;
                } else {
                    await new Promise(resolve => setTimeout(resolve, 400));
                }
            }
            if (samples.length < 3) {
                if (!faceSeen) throw new Error('Wajah belum terbaca kamera. Dekatkan wajah, hadap kamera, dan pastikan pencahayaan cukup.');
                if (!embeddingSeen) throw new Error('Wajah terlihat, tetapi model tidak menghasilkan data wajah. Muat ulang halaman dan coba lagi.');
                if (!livenessSeen) throw new Error('Wajah terdeteksi, tetapi pemeriksaan wajah hidup belum lolos. Hadap kamera langsung dan berkedip.');
                throw new Error('Verifikasi wajah belum stabil. Tahan posisi wajah beberapa detik lalu coba lagi.');
            }
            return samples[0].map((_, i) => samples.reduce((sum, sample) => sum + Number(sample[i]), 0) / samples.length);
        } finally {
            media?.getTracks().forEach(track => track.stop()); overlay.remove(); frameStyle.remove();
        }
    }
    const registerButtons = [...document.querySelectorAll('#weekly-face-register')];
    registerButtons.forEach(button => button.addEventListener('click', async () => {
        button.disabled = true;
        try {
            const embedding = await captureFace();
            const result = await api(faceRoutes.register, { embedding });
            showFaceMessage(result.message);
            registerButtons.forEach(item => item.remove());
            const faceStatus = document.getElementById('weekly-face-status');
            if (faceStatus) faceStatus.textContent = 'Wajah sudah terdaftar di akun ini.';
        } catch (error) { showFaceMessage(error.message || 'Pendaftaran wajah gagal.', true); }
        finally { button.disabled = false; }
    }));
    api(faceRoutes.status).then(status => {
        if (status.registered) registerButtons.forEach(button => button.remove());
        const faceStatus = document.getElementById('weekly-face-status');
        if (faceStatus) faceStatus.textContent = status.registered ? 'Wajah sudah terdaftar di akun ini.' : 'Daftarkan wajah satu kali sebelum absen.';
    }).catch(error => showFaceMessage(error.message || 'Status pendaftaran wajah gagal dimuat.', true));
    document.getElementById('weekly-face-attend')?.addEventListener('click', async event => {
        const button = event.currentTarget;
        try {
            button.disabled = true;
            const eligibility = await api(faceRoutes.preflight);
            if (!eligibility.can_attend) {
                showFaceMessage(eligibility.message || 'Belum bisa melakukan presensi saat ini.', true);
                return;
            }
            if (!navigator.geolocation) throw new Error('Browser tidak menyediakan lokasi.');
            const position = await new Promise((resolve, reject) => navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }));
            const embedding = await captureFace();
            const result = await api(faceRoutes.attend, { embedding, latitude: position.coords.latitude, longitude: position.coords.longitude, accuracy: position.coords.accuracy });
            showFaceMessage(result.message); setTimeout(() => window.location.reload(), 1200);
        } catch (error) { showFaceMessage(error.message || 'Absen Face ID gagal.', true); }
        finally { button.disabled = false; }
    });
    let locationInFlight = false;
    let geocodeInFlight = false;
    let pendingDispatch = false;
    let locationDenied = false;
    let lastGuideNoticeAt = 0;
    let scannerLibraryPromise = null;

    // Pustaka jsQR dimuat secara dinamis dari sini, bukan lewat stack blade,
    // karena blok script milik Livewire hanya dieksekusi sebagai JavaScript,
    // sehingga tag pemuat pustaka di dalamnya tidak akan pernah dijalankan.
    function loadScannerLibrary() {
        if (typeof window.jsQR !== 'undefined') return Promise.resolve();

        if (!scannerLibraryPromise) {
            scannerLibraryPromise = new Promise(function (resolve, reject) {
                const tag = document.createElement('script');
                tag.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
                tag.async = true;
                tag.onload = function () { resolve(); };
                tag.onerror = function () { reject(new Error('Gagal memuat pustaka jsQR.')); };
                document.head.appendChild(tag);
            });

            // Biarkan percobaan berikutnya mencoba ulang kalau gagal (mis. offline sesaat).
            scannerLibraryPromise.catch(function () { scannerLibraryPromise = null; });
        }

        return scannerLibraryPromise;
    }

    // Lokasi kantor tetap: label ini selalu dipakai saat GPS koordinator
    // berada di dalam radius titik tersebut, menggantikan nama acak dari
    // reverse-geocode yang kadang tidak konsisten.
    const FIXED_SPOTS = [
        { name: 'Topaz Commercial, Kota Bandung', lat: -6.959635, lon: 107.698876, radius: 500 },
        { name: 'Jl Bulevar Raya, Kota Bandung', lat: -6.962124, lon: 107.704894, radius: 500 }
    ];

    function distanceMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = function (d) { return d * Math.PI / 180; };
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const s = Math.sin(dLat / 2) * Math.sin(dLat / 2)
            + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        return 2 * R * Math.asin(Math.sqrt(s));
    }

    function matchFixedSpot(lat, lon) {
        for (let i = 0; i < FIXED_SPOTS.length; i++) {
            if (distanceMeters(lat, lon, FIXED_SPOTS[i].lat, FIXED_SPOTS[i].lon) <= FIXED_SPOTS[i].radius) {
                return FIXED_SPOTS[i].name;
            }
        }
        return '';
    }

    function notifyLocationBlocked(showToast) {
        if (!showToast) return;
        Livewire.dispatch('notify', {
            type: 'error',
            message: 'Izin lokasi diblokir browser. Ketuk ikon gembok di address bar, izinkan akses lokasi, lalu scan ulang QR.'
        });
    }

    // Request device geolocation for the "Lokasi" column in the admin
    // attendance table. Prompt izin lokasi dimunculkan saat menu Weekly
    // Meeting (halaman scan) dibuka DAN setiap kali user scan QR walau lokasi
    // belum aktif. Panggilan getCurrentPosition dilakukan langsung â€” itu cara
    // paling andal untuk memunculkan prompt browser.
    function requestLocation(showToast) {
        if (locationInFlight || cachedCoords || !navigator.geolocation) return;

        // Browser baru akan menampilkan prompt lagi jika status masih "belum
        // diputuskan". Setelah diblokir permanen, prompt tidak akan muncul â€”
        // beri tahu user cara mengizinkannya lewat pengaturan situs.
        if (locationDenied) {
            notifyLocationBlocked(showToast);
            return;
        }

        // Chrome Android kerap mengabaikan permintaan geolokasi yang dikirim
        // saat tab belum terlihat (proses load awal), sehingga prompt tidak
        // muncul. Tunggu sampai halaman benar-benar tampak dulu.
        if (document.visibilityState && document.visibilityState !== 'visible') {
            document.addEventListener('visibilitychange', function onVis() {
                if (document.visibilityState === 'visible') {
                    document.removeEventListener('visibilitychange', onVis);
                    requestLocation(showToast);
                }
            });
            return;
        }

        locationInFlight = true;
        navigator.geolocation.getCurrentPosition(function (pos) {
            cachedCoords = (pos.coords.latitude).toFixed(6) + ', ' + (pos.coords.longitude).toFixed(6);
            const spot = matchFixedSpot(pos.coords.latitude, pos.coords.longitude);
            if (spot) {
                cachedName = spot;
            } else {
                reverseGeocode(pos.coords);
            }
        }, function (err) {
            locationInFlight = false;
            if (err && err.code === 1) {
                locationDenied = true;
                notifyLocationBlocked(showToast);
            } else if (showToast) {
                Livewire.dispatch('notify', {
                    type: 'error',
                    message: 'Lokasi tidak terdeteksi. Aktifkan lokasi perangkat/browser, lalu scan ulang QR.'
                });
            }
        }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 5000 });
    }

    // Reverse-geocode the coordinates into a readable place name using the
    // free OpenStreetMap Nominatim service (no API key). Picks the most
    // specific label possible (nama tempat/bangunan, lalu jalan, lalu area).
    // Kalau hasilnya terlalu generik (hanya kota/postcode), fallback ke
    // koordinat mentah yang lebih presisi.
    function reverseGeocode(coords) {
        if (geocodeInFlight) return;
        geocodeInFlight = true;
        const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&zoom=18&addressdetails=1&accept-language=id&lat='
            + coords.latitude + '&lon=' + coords.longitude;
        fetch(url, { headers: { 'accept-language': 'id' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                const a = data && data.address;
                if (!a) return;

                const precCoords = (coords.latitude).toFixed(6) + ', ' + (coords.longitude).toFixed(6);
                const poi = data.name
                    || a.building || a.tourism || a.shop || a.amenity
                    || a.leisure || a.office || a.attraction || a.cafe || a.restaurant || '';
                const road = a.road || a.footway || a.pedestrian || a.residential || a.cycleway || '';
                const area = a.neighbourhood || a.hamlet || a.suburb || a.quarter || a.village || a.area || a.town || '';

                const specific = [poi, road, area].filter(function (p) {
                    // Buang bagian yang cuma angka (postcode) â€” bukan nama tempat.
                    return p && !/^\d+$/.test(String(p).trim());
                });

                // Tampilkan nama hanya jika ada label spesifik (tempat/jalan/area).
                // Kalau cuma kota/postcode â€” terlalu generik â€” pakai koordinat presisi.
                cachedName = specific.length ? specific.slice(0, 2).join(', ') : precCoords;
            })
            .catch(function () { cachedName = ''; })
            .finally(function () { geocodeInFlight = false; });
    }

    function getDeviceLocation() {
        if (cachedName) return cachedName;
        if (cachedCoords) {
            // Fallback: kalau nama belum siap, pastikan koordinat di dekat
            // lokasi kantor tetap tetap memakai label yang sudah ditentukan.
            const p = cachedCoords.split(', ');
            if (p.length === 2) {
                const spot = matchFixedSpot(parseFloat(p[0]), parseFloat(p[1]));
                if (spot) return spot;
            }
        }
        return cachedCoords;
    }

    function isQrInsideGuide(location, sourceWidth, sourceHeight) {
        const frame = document.getElementById('scanner-frame');
        const guide = frame && frame.querySelector('.qr-scan-guide');
        if (!frame || !guide || !location || !sourceWidth || !sourceHeight) return false;

        const frameRect = frame.getBoundingClientRect();
        const guideRect = guide.getBoundingClientRect();
        if (!frameRect.width || !frameRect.height) return false;

        const scaleX = sourceWidth / frameRect.width;
        const scaleY = sourceHeight / frameRect.height;
        const bounds = {
            left: (guideRect.left - frameRect.left) * scaleX,
            right: (guideRect.right - frameRect.left) * scaleX,
            top: (guideRect.top - frameRect.top) * scaleY,
            bottom: (guideRect.bottom - frameRect.top) * scaleY,
        };
        const corners = [
            location.topLeftCorner,
            location.topRightCorner,
            location.bottomLeftCorner,
            location.bottomRightCorner,
        ];

        return corners.every(function (point) {
            return point
                && point.x >= bounds.left && point.x <= bounds.right
                && point.y >= bounds.top && point.y <= bounds.bottom;
        });
    }

    // Pengguna hanya boleh absen (scan QR) setelah lokasi aktif. Ketika QR
    // terdeteksi tapi lokasi belum didapat, minta izin lagi sekarang dan tunda
    // pengiriman sampai posisi tersedia. Kalau belum mengizinkan, prompt
    // muncul lagi setiap kali user scan ulang.
    function dispatchScan(qrCode) {
        if (pendingDispatch) return;
        pendingDispatch = true;

        const send = function (loc) {
            pendingDispatch = false;
            Livewire.dispatch('qrScanned', { qrCode: qrCode, deviceLocation: loc });
        };

        const location = getDeviceLocation();
        if (location) {
            send(location);
            return;
        }

        if (!navigator.geolocation) {
            pendingDispatch = false;
            Livewire.dispatch('notify', {
                type: 'error',
                message: 'Perangkat tidak mendukung lokasi. Aktifkan lokasi untuk melakukan absensi QR.'
            });
            return;
        }

        requestLocation(true);

        const start = Date.now();
        const check = setInterval(function () {
            const loc = getDeviceLocation();
            if (loc) {
                clearInterval(check);
                send(loc);
            } else if (locationDenied || Date.now() - start > 60000) {
                clearInterval(check);
                pendingDispatch = false;
                if (!locationDenied) Livewire.dispatch('notify', {
                    type: 'error',
                    message: 'Lokasi belum aktif. Aktifkan lokasi, lalu scan ulang QR.'
                });
            }
        }, 400);
    }

    // Friendly, non-technical messages for camera errors
    const CAMERA_ERRORS = {
        NotAllowedError: 'Kamera tidak dapat digunakan. Silakan izinkan akses kamera pada pengaturan browser untuk melanjutkan absensi.',
        PermissionDeniedError: 'Kamera tidak dapat digunakan. Silakan izinkan akses kamera pada pengaturan browser untuk melanjutkan absensi.',
        NotFoundError: 'Kamera tidak ditemukan pada perangkat. Pastikan perangkat memiliki kamera.',
        DevicesNotFoundError: 'Kamera tidak ditemukan pada perangkat. Pastikan perangkat memiliki kamera.',
        NotReadableError: 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi lain yang memakai kamera, lalu coba lagi.',
        TrackStartError: 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi lain yang memakai kamera, lalu coba lagi.',
        OverconstrainedError: 'Kamera yang dipilih tidak tersedia pada perangkat ini. Coba pilih kamera lainnya.',
        SecurityError: 'Kamera tidak dapat diakses karena kebijakan keamanan browser. Absensi QR membutuhkan koneksi HTTPS.',
        undefined: 'Kamera tidak dapat diakses. Silakan izinkan akses kamera pada pengaturan browser untuk melanjutkan absensi.'
    };

    function showCameraError(err) {
        const name = err && err.name;
        const message = CAMERA_ERRORS[name] || CAMERA_ERRORS.undefined;
        if (err) console.error('[WeeklyMeetingScan] Camera error:', err);
        Livewire.dispatch('notify', { type: 'error', message: message });
    }

    function stopScanner() {
        if (stream) {
            stream.getTracks().forEach(function (track) { track.stop(); });
            stream = null;
        }
        scanning = false;
    }

    // Samakan rasio kotak preview dengan rasio asli frame kamera, supaya tidak
    // ada area kosong (letterbox) dan tidak ada bagian gambar yang terpotong.
    function syncScannerFrame() {
        const video = document.getElementById('scanner-video');
        const frame = document.getElementById('scanner-frame');
        if (!video || !frame || !video.videoWidth || !video.videoHeight) return;

        frame.style.aspectRatio = video.videoWidth + ' / ' + video.videoHeight;
    }

    // Open the device camera and attach the live stream to the preview video.
    function startScanner(facingMode, token, retries) {
        retries = retries || 0;
        const video = document.getElementById('scanner-video');

        // A newer camera request superseded this one: abort the stale attempt.
        if (token !== cameraRequestId) return;

        // Video element may not be rendered yet right after the Livewire re-render:
        // retry briefly instead of giving up.
        if (!video) {
            if (retries < 20) setTimeout(function () { startScanner(facingMode, token, retries + 1); }, 150);
            return;
        }
        if (scanning) return;

        // Ask browser permission + open the actual device camera.
        // 'user' = kamera depan, 'environment' = kamera belakang.
        const openCamera = function (withFacing) {
            navigator.mediaDevices.getUserMedia({
                video: withFacing ? { facingMode: facingMode } : true
            }).then(function (mediaStream) {
                if (token !== cameraRequestId || !document.getElementById('scanner-video')) {
                    // Scanner switched camera or was closed while permission was pending.
                    mediaStream.getTracks().forEach(function (track) { track.stop(); });
                    return;
                }
                stream = mediaStream;
                video.srcObject = mediaStream;

                // Rasio box preview mengikuti rasio asli kamera (portrait 3:4 /
                // 9:16 di HP, 4:3 di webcam) begitu metadata stream tersedia.
                syncScannerFrame();
                video.addEventListener('loadedmetadata', syncScannerFrame);

                scanning = true;

                // Give the stream a moment to start producing frames, then loop.
                setTimeout(function () { requestAnimationFrame(scanLoop); }, 300);
            }).catch(function (err) {
                if (token !== cameraRequestId) return;
                // Beberapa device/browser menolak constraint facingMode
                // (OverconstrainedError) walau izin kamera sudah diberikan.
                // Coba sekali lagi memakai kamera apa pun yang tersedia.
                if (withFacing && (err.name === 'OverconstrainedError' || err.name === 'ConstraintNotSatisfiedError')) {
                    openCamera(false);
                    return;
                }
                showCameraError(err);
            });
        };

        // Kamera baru dinyalakan setelah jsQR siap, supaya frame pertama yang
        // diproses scanLoop tidak terlewat karena pustaka belum termuat.
        loadScannerLibrary().then(function () {
            if (token !== cameraRequestId) return;
            openCamera(true);
        }).catch(function () {
            if (token !== cameraRequestId) return;
            showCameraError(new Error('Pustaka jsQR gagal dimuat. Periksa koneksi internet Anda.'));
        });
    }

    // Continuously grab frames from the live video and run jsQR on them.
    function scanLoop() {
        if (!scanning) return;

        const video = document.getElementById('scanner-video');
        const now = Date.now();

        // readyState 2 = HAVE_CURRENT_DATA. Avoid HAVE_ENOUGH_DATA (4)
        // which some mobile browsers never reach on the live stream.
        if (video && video.readyState >= 2 && video.videoWidth > 0 && now - lastScanAt >= 200) {
            lastScanAt = now;

            const width = video.videoWidth;
            const height = video.videoHeight;

            if (width > 0 && height > 0 && typeof window.jsQR !== 'undefined') {
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d', { willReadFrequently: true });
                ctx.drawImage(video, 0, 0, width, height);
                const imageData = ctx.getImageData(0, 0, width, height);
                const code = window.jsQR(imageData.data, width, height, { inversionAttempts: 'dontInvert' });

                // Send the detected value to the existing server-side validation.
                // Pause briefly to avoid spamming the server while the user
                // keeps the same QR in front of the camera.
                if (code && code.data && now >= pausedUntil) {
                    if (!isQrInsideGuide(code.location, width, height)) {
                        pausedUntil = now + 1000;
                        if (now - lastGuideNoticeAt >= 4000) {
                            lastGuideNoticeAt = now;
                            Livewire.dispatch('notify', {
                                type: 'warning',
                                message: 'QR belum berada di dalam bingkai. Atur posisi kamera hingga seluruh QR masuk ke kotak.'
                            });
                        }
                    } else {
                        pausedUntil = now + 2500;
                        dispatchScan(code.data);
                    }
                }
            }
        }

        requestAnimationFrame(scanLoop);
    }

    // The location permission prompt is shown right here, when this meeting
    // page (menu Weekly Meeting) opens â€” and again at scan time until the
    // user enables location.
    // (When embedded as a tab inside the admin page, skip the automatic prompt;
    // location is requested on demand when the user actually scans.)
    @if(! $embedded)
    requestLocation(false);
    @endif

    // Jaga agar prompt muncul konsisten: jika panggilan pertama dibuang browser
    // (mis. tab masih loading), coba sekali lagi sesaat kemudian.
    @if(! $embedded)
    setTimeout(function () {
        if (!cachedCoords) requestLocation(false);
    }, 1500);
    @endif

    // Dipicu oleh `$this->dispatch('camera-selected')` dari selectCamera()
    // setelah Livewire selesai merender scanner box, jadi #scanner-video sudah
    // pasti ada di DOM sebelum kamera dinyalakan (tanpa race / tanpa @this).
    Livewire.on('camera-selected', ({ camera }) => {
        // 1. Bump the request id so any older/in-flight camera attempt is discarded,
        //    then always stop any running camera first (prevents double-camera conflict).
        const token = ++cameraRequestId;
        stopScanner();

        // 2. Camera API needs a secure context (HTTPS / localhost).
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Livewire.dispatch('notify', {
                type: 'error',
                message: 'Kamera tidak dapat digunakan. Akses kamera membutuhkan koneksi HTTPS. Gunakan HTTPS atau localhost untuk mengakses fitur ini.'
            });
            console.error('[WeeklyMeetingScan] navigator.mediaDevices.getUserMedia is not available (insecure context).');
            return;
        }

        // 3. Open the requested camera and start scanning.
        startScanner(camera, token);
    });

    // Stop the camera whenever the scanner node leaves the page (success,
    // "Ganti Kamera", component destroyed) so the camera light turns off.
    const scannerObserver = new MutationObserver(function () {
        if (!document.getElementById('scanner-video')) stopScanner();
    });
    if (document.body) scannerObserver.observe(document.body, { childList: true, subtree: true });

    // Stop the camera when leaving the page entirely.
    window.addEventListener('pagehide', stopScanner);
    window.addEventListener('beforeunload', stopScanner);
    Livewire.hook('component.destroyed', function () { stopScanner(); });
})();
</script>
@endscript
