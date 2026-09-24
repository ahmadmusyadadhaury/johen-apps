@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Weekly Meeting</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Scan QR Code untuk absensi rapat mingguan</p>
    </div>
@endpush

<div class="space-y-4 max-w-xl mx-auto">
<style>
    @keyframes qrPop { 0% { transform: scale(0.4); opacity: 0; } 60% { transform: scale(1.08); } 100% { transform: scale(1); opacity: 1; } }
    @keyframes qrCheckDraw { from { stroke-dashoffset: 36; } to { stroke-dashoffset: 0; } }
    @keyframes qrRing { 0% { box-shadow: 0 0 0 0 rgba(16,185,129,0.45); } 70% { box-shadow: 0 0 0 22px rgba(16,185,129,0); } 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); } }
    @keyframes qrPopIn { 0% { transform: scale(0.85); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    @keyframes ringPop { 0% { box-shadow: 0 0 0 0 rgba(16,185,129,0.45); } 100% { box-shadow: 0 0 0 30px rgba(16,185,129,0); } }
    @keyframes confettiBurst {
        0% { transform: translate(-50%, -50%); opacity: 1; }
        100% { transform: translate(calc(-50% + var(--dx)), calc(-50% + var(--dy))) rotate(var(--rot)); opacity: 0; }
    }
    @keyframes modalCardIn { 0% { transform: scale(0.86) translateY(14px); opacity: 0; } 100% { transform: scale(1) translateY(0); opacity: 1; } }
    .animate-scan-ring { animation: qrRing 1.9s ease-out infinite; }
    .animate-scan-check { animation: qrPop 0.5s cubic-bezier(0.34,1.56,0.64,1) both; }
    .animate-scan-check .check-path { stroke-dasharray: 36; stroke-dashoffset: 36; animation: qrCheckDraw 0.55s ease-out 0.22s forwards; }
    .m-success-ring { animation: ringPop 1s cubic-bezier(0.16,1,0.3,1) 0.3s both; }
    .m-success-card { animation: modalCardIn 0.45s cubic-bezier(0.34,1.56,0.64,1) 0.08s both; }
    .scan-confetti {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        animation: confettiBurst 1.6s cubic-bezier(0.22,0.61,0.36,1) forwards;
    }
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
        <div class="p-6">
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

{{-- Popup sukses absen: muncul setelah QR berhasil dipindai --}}
@if($status === 'success' && $currentMeeting)
<div x-cloak x-data="{ show: false }" x-init="setTimeout(() => show = true, 60)" x-show="show" class="fixed inset-0 z-[9998] flex items-center justify-center p-4">
    <div
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="show = false"
    ></div>

    <div class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 shadow-2xl overflow-hidden m-success-card" @click.stop>
        <div class="relative p-8">
            {{-- Lencana centang animasi + konfeti meledak dari pusatnya --}}
            <div class="relative mx-auto mb-5 h-24 w-24">
                <div class="pointer-events-none absolute -inset-8" aria-hidden="true">
                    @php
                        $succColors = ['#34d399', '#10b981', '#fbbf24', '#f472b6', '#38bdf8', '#a78bfa', '#fb7185', '#4ade80', '#f59e0b'];
                    @endphp
                    @for($i = 0; $i < 26; $i++)
                        @php
                            $angle = ($i / 26) * 360;
                            $rad = 55 + ($i % 7) * 13;
                            $dx = cos(deg2rad($angle)) * $rad;
                            $dy = sin(deg2rad($angle)) * $rad + 45;
                            $rot = 360 + ($i % 5) * 90;
                            $delay = 0.1 + ($i % 7) * 0.045;
                            $size = 6 + ($i % 4) * 2;
                            $round = $i % 4 === 0;
                        @endphp
                        <span class="scan-confetti" style="--dx: {{ round($dx) }}px; --dy: {{ round($dy) }}px; --rot: {{ $rot }}deg; animation-delay: {{ $delay }}s; width: {{ $size }}px; height: {{ $round ? $size : round($size * 1.4) }}px; background: {{ $succColors[$i % count($succColors)] }}; border-radius: {{ $round ? '9999px' : '2px' }};"></span>
                    @endfor
                </div>
                <div class="absolute inset-0 flex items-center justify-center rounded-full bg-green-50 dark:bg-green-900/20 m-success-ring">
                    <svg class="w-12 h-12 text-green-600 dark:text-green-400 animate-scan-check" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path class="check-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
            </div>

            <h3 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100 text-center">Absensi Berhasil!</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-2">{{ $message }}</p>

            @if($currentMeeting)
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $currentMeeting->title }}</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $currentMeeting->meeting_date->format('d F Y') }} | {{ $currentMeeting->start_time?->format('H:i') ?? '-' }} - {{ $currentMeeting->end_time?->format('H:i') ?? '-' }}</p>
            </div>
            @endif

            <div class="flex items-center justify-center pt-6 mt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" @click="show = false" class="btn-primary text-xs px-8">Selesai</button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    let stream = null;
    let scanning = false;
    let lastScanAt = 0;
    let cameraRequestId = 0;
    let pausedUntil = 0;
    let cachedCoords = '';
    let cachedName = '';
    let locationInFlight = false;
    let geocodeInFlight = false;
    let pendingDispatch = false;
    let locationDenied = false;

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
    // belum aktif. Panggilan getCurrentPosition dilakukan langsung — itu cara
    // paling andal untuk memunculkan prompt browser.
    function requestLocation(showToast) {
        if (locationInFlight || cachedCoords || !navigator.geolocation) return;

        // Browser baru akan menampilkan prompt lagi jika status masih "belum
        // diputuskan". Setelah diblokir permanen, prompt tidak akan muncul —
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
                    // Buang bagian yang cuma angka (postcode) — bukan nama tempat.
                    return p && !/^\d+$/.test(String(p).trim());
                });

                // Tampilkan nama hanya jika ada label spesifik (tempat/jalan/area).
                // Kalau cuma kota/postcode — terlalu generik — pakai koordinat presisi.
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
                    dispatchScan(code.data);
                }
            }
        }

        requestAnimationFrame(scanLoop);
    }

    // The location permission prompt is shown right here, when this meeting
    // page (menu Weekly Meeting) opens — and again at scan time until the
    // user enables location.
    requestLocation(false);

    // Jaga agar prompt muncul konsisten: jika panggilan pertama dibuang browser
    // (mis. tab masih loading), coba sekali lagi sesaat kemudian.
    setTimeout(function () {
        if (!cachedCoords) requestLocation(false);
    }, 1500);

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
});
</script>

<!-- Load jsQR library for QR code scanning -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
@endpush