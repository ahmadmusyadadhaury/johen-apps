@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Weekly Meeting - Absen</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Scan QR Code untuk absensi rapat mingguan</p>
    </div>
@endpush

<style>
    @keyframes qrPop { 0% { transform: scale(0.4); opacity: 0; } 60% { transform: scale(1.08); } 100% { transform: scale(1); opacity: 1; } }
    @keyframes qrCheckDraw { from { stroke-dashoffset: 36; } to { stroke-dashoffset: 0; } }
    @keyframes qrRing { 0% { box-shadow: 0 0 0 0 rgba(16,185,129,0.45); } 70% { box-shadow: 0 0 0 22px rgba(16,185,129,0); } 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); } }
    @keyframes qrPopIn { 0% { transform: scale(0.85); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    .animate-scan-ring { animation: qrRing 1.9s ease-out infinite; }
    .animate-scan-check { animation: qrPop 0.5s cubic-bezier(0.34,1.56,0.64,1) both; }
    .animate-scan-check .check-path { stroke-dasharray: 36; stroke-dashoffset: 36; animation: qrCheckDraw 0.55s ease-out 0.22s forwards; }
</style>
<div class="space-y-4 max-w-xl mx-auto">
    @if(!$currentMeeting)
    {{-- No Active Meeting --}}
    <div class="card">
        <div class="p-12 text-center">
            <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tidak Ada Rapat Aktif</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Saat ini tidak ada rapat mingguan yang dijadwalkan (7 hari terakhir).</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Silakan hubungi Admin Master untuk membuat jadwal rapat baru.</p>
        </div>
    </div>

    @elseif($alreadyAttended)
    {{-- Already Attended --}}
    <div class="card">
        <div class="p-12 text-center">
            <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-green-50 dark:bg-green-900/20 mx-auto mb-4 animate-scan-ring">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400 animate-scan-check" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path class="check-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
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
        <div class="p-6">
            {{-- Meeting Info --}}
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-900/30 text-center">
                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $currentMeeting->title }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $currentMeeting->meeting_date->format('d F Y') }}</p>
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

            {{-- Camera Selection --}}
            @if(!$selectedCamera)
            <div class="mb-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pilih kamera untuk mulai memindai QR Code absensi:</p>
                <div class="grid grid-cols-2 gap-3 max-w-sm mx-auto">
                    <button type="button" onclick="window.openScanCamera && window.openScanCamera('user')" class="flex flex-col items-center gap-2 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all p-5">
                        <svg class="w-8 h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Kamera Depan</span>
                    </button>
                    <button type="button" onclick="window.openScanCamera && window.openScanCamera('environment')" class="flex flex-col items-center gap-2 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all p-5">
                        <svg class="w-8 h-8 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Kamera Belakang</span>
                    </button>
                </div>
            </div>

            @else
            {{-- Camera Scanner --}}
            <div class="space-y-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Arahkan kamera ke QR Code yang dibagikan oleh Admin Master</p>
                </div>

                <div id="scanner-container" class="relative">
                    <div class="aspect-video bg-gray-900 rounded-xl overflow-hidden relative">
                        <video id="scanner-video" class="w-full h-full object-cover" autoplay playsinline muted></video>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-48 h-48 border-2 border-primary-400 rounded-lg relative">
                                <div class="absolute -top-3 -left-3 w-6 h-6 border-t-2 border-l-2 border-primary-400"></div>
                                <div class="absolute -top-3 -right-3 w-6 h-6 border-t-2 border-r-2 border-primary-400"></div>
                                <div class="absolute -bottom-3 -left-3 w-6 h-6 border-b-2 border-l-2 border-primary-400"></div>
                                <div class="absolute -bottom-3 -right-3 w-6 h-6 border-b-2 border-r-2 border-primary-400"></div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-center">
                        <p class="text-xs text-white/80 bg-black/50 px-3 py-1 rounded-full">Posisikan QR code di dalam kotak</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-3">
                    <button wire:click="resetScanner" class="btn-secondary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Ganti Kamera
                    </button>
                    <button wire:click="resetScanner" class="btn-secondary text-xs" id="stop-scanner-btn" style="display: none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Hentikan Scanner
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    let stream = null;
    let scanning = false;
    let lastScanAt = 0;
    let cameraRequestId = 0;
    let pausedUntil = 0;
    let cachedLocation = '';

    // Best-effort device geolocation for the "Lokasi" column in the
    // admin attendance table. Resolves asynchronously; we always return
    // whatever we have (possibly empty) so scanning is never blocked.
    function getDeviceLocation() {
        if (!cachedLocation && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (pos) {
                cachedLocation = (pos.coords.latitude).toFixed(6) + ', ' + (pos.coords.longitude).toFixed(6);
            }, function () {
                cachedLocation = '';
            }, { timeout: 4000, maximumAge: 120000 });
        }
        return cachedLocation;
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
        const stopBtn = document.getElementById('stop-scanner-btn');
        if (stopBtn) stopBtn.style.display = 'none';
    }

    // Open the device camera and attach the live stream to the preview video.
    function startScanner(facingMode, token, retries) {
        retries = retries || 0;
        const video = document.getElementById('scanner-video');
        const stopBtn = document.getElementById('stop-scanner-btn');

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
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: facingMode }
        }).then(function (mediaStream) {
            if (token !== cameraRequestId || !document.getElementById('scanner-video')) {
                // Scanner switched camera or was closed while permission was pending.
                mediaStream.getTracks().forEach(function (track) { track.stop(); });
                return;
            }
            stream = mediaStream;
            video.srcObject = mediaStream;
            scanning = true;
            if (stopBtn) stopBtn.style.display = 'inline-flex';

            // Give the stream a moment to start producing frames, then loop.
            setTimeout(function () { requestAnimationFrame(scanLoop); }, 300);
        }).catch(function (err) {
            if (token !== cameraRequestId) return;
            showCameraError(err);
            if (stopBtn) stopBtn.style.display = 'none';
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

            if (width > 0 && height > 0 && typeof jsQR !== 'undefined') {
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d', { willReadFrequently: true });
                ctx.drawImage(video, 0, 0, width, height);
                const imageData = ctx.getImageData(0, 0, width, height);
                const code = jsQR(imageData.data, width, height, { inversionAttempts: 'dontInvert' });

                // Send the detected value to the existing server-side validation.
                // Pause briefly to avoid spamming the server while the user
                // keeps the same QR in front of the camera.
                if (code && code.data && now >= pausedUntil) {
                    pausedUntil = now + 2500;
                    @this.call('handleQrScan', code.data, getDeviceLocation());
                }
            }
        }

        requestAnimationFrame(scanLoop);
    }

    // Called when the user taps "Kamera Depan" / "Kamera Belakang".
    window.openScanCamera = function (facingMode) {
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

        // 3. Sync server state so the scanner box renders, wait for the DOM
        //    to be updated, then start the real camera.
        var request = @this.call('selectCamera', facingMode);
        if (request && typeof request.then === 'function') {
            request.then(function () {
                setTimeout(function () { startScanner(facingMode, token); }, 100);
            }).catch(function (err) {
                console.error('[WeeklyMeetingScan] selectCamera failed:', err);
            });
        } else {
            setTimeout(function () { startScanner(facingMode, token); }, 100);
        }
    };

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
});
</script>

<!-- Load jsQR library for QR code scanning -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
@endpush