<div id="daily-face-card" class="mb-5 rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-4 dark:border-blue-900 dark:from-blue-950/40 dark:to-indigo-950/30 sm:p-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 11a3 3 0 116 0c0 1.5-1.3 2.5-3 2.5S9 12.5 9 11zm-2.5 6c.8-1.5 2.5-2.3 5.5-2.3s4.7.8 5.5 2.3M4 8V5a1 1 0 011-1h3m8 0h3a1 1 0 011 1v3M4 16v3a1 1 0 001 1h3m8 0h3a1 1 0 001-1v-3"/></svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100">Presensi Harian Face ID</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->office?->name }}</p>
                </div>
            </div>
            <p id="daily-face-message" class="mt-3 text-xs font-medium text-blue-700 dark:text-blue-300">Memeriksa status Face ID...</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            <button type="button" data-daily-face-register class="btn-secondary hidden min-h-11 justify-center text-xs">Daftarkan Wajah</button>
            <button type="button" data-daily-face-attend class="btn-primary min-h-11 justify-center text-xs" disabled>Memuat...</button>
        </div>
    </div>
</div>

@script
<script>
(() => {
    if (window.__dailyFaceAttendanceBound) return;
    window.__dailyFaceAttendanceBound = true;

    const routes = {
        status: @js(route('hris.absensi.face.status')),
        register: @js(route('hris.absensi.face.register')),
        attend: @js(route('hris.absensi.face.attend')),
    };
    let status = null;
    let humanPromise = null;

    const message = (text, error = false) => {
        const el = document.getElementById('daily-face-message');
        if (!el) return;
        el.textContent = text;
        el.className = `mt-3 text-xs font-medium ${error ? 'text-red-600 dark:text-red-400' : 'text-blue-700 dark:text-blue-300'}`;
    };
    const api = async (url, body = null) => {
        const response = await fetch(url, {
            method: body ? 'POST' : 'GET',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
            body: body ? JSON.stringify(body) : null,
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || 'Permintaan gagal.');
        return data;
    };
    const loadHuman = () => {
        if (window.Human?.Human || window.Human) return Promise.resolve(window.Human);
        if (!humanPromise) humanPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@vladmandic/human@3.3.6/dist/human.js';
            script.onload = () => resolve(window.Human);
            script.onerror = () => reject(new Error('Modul Face ID gagal dimuat. Periksa koneksi internet.'));
            document.head.appendChild(script);
        });
        return humanPromise;
    };
    const capture = async (title) => {
        if (!navigator.mediaDevices?.getUserMedia) throw new Error('Kamera memerlukan HTTPS dan browser yang mendukung kamera.');
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 z-[10000] flex items-center justify-center overflow-y-auto bg-slate-950/85 p-4';
        overlay.innerHTML = `<div class="w-full max-w-md rounded-2xl bg-white p-4 shadow-2xl dark:bg-gray-900"><div class="mb-3 flex items-center justify-between gap-3"><div><h3 class="font-bold text-gray-900 dark:text-gray-100">${title}</h3><p data-face-label class="text-xs text-gray-500 dark:text-gray-400">Meminta akses kamera...</p></div><button type="button" data-face-close class="btn-secondary text-xs">Tutup</button></div><div class="overflow-hidden rounded-xl bg-black" style="aspect-ratio:3/4"><video autoplay playsinline muted class="h-full w-full object-cover"></video></div></div>`;
        document.body.appendChild(overlay);
        const video = overlay.querySelector('video');
        const label = overlay.querySelector('[data-face-label]');
        let stream;
        let closed = false;
        const close = () => { closed = true; stream?.getTracks().forEach(track => track.stop()); overlay.remove(); };
        overlay.querySelector('[data-face-close]').onclick = close;

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            if (closed) throw new DOMException('Kamera ditutup.', 'AbortError');
            video.srcObject = stream;
            await new Promise(resolve => video.onloadedmetadata = resolve);
            await video.play();
            label.textContent = 'Memuat pengenalan wajah...';
            const HumanCtor = await loadHuman();
            const HumanClass = HumanCtor?.Human || HumanCtor?.default || HumanCtor;
            const human = new HumanClass({ modelBasePath: 'https://cdn.jsdelivr.net/npm/@vladmandic/human@3.3.6/models', face: { enabled: true, detector: { maxDetected: 2 }, mesh: { enabled: true }, description: { enabled: true }, antispoof: { enabled: true }, liveness: { enabled: true } }, body: { enabled: false }, hand: { enabled: false }, object: { enabled: false }, gesture: { enabled: false } });
            await human.load();
            await human.warmup();
            const samples = [];
            label.textContent = 'Lihat lurus ke kamera dan berkedip.';
            while (!closed && samples.length < 2) {
                const result = await human.detect(video);
                const face = result.face?.length === 1 ? result.face[0] : null;
                if (face?.embedding?.length === 1024 && Number(face.real || 0) > 0.5 && Number(face.live || 0) > 0.5) {
                    samples.push(face.embedding.map(Number));
                    label.textContent = samples.length < 2 ? 'Wajah terbaca. Tahan posisi sebentar...' : 'Wajah berhasil ditangkap.';
                } else {
                    label.textContent = face ? 'Berkedip dan pastikan pencahayaan cukup.' : 'Pastikan hanya satu wajah terlihat di kamera.';
                }
                await new Promise(resolve => setTimeout(resolve, 150));
            }
            if (closed) throw new DOMException('Kamera ditutup.', 'AbortError');
            close();
            return samples;
        } catch (error) {
            close();
            throw error;
        }
    };
    const render = () => {
        const register = document.querySelector('[data-daily-face-register]');
        const attend = document.querySelector('[data-daily-face-attend]');
        if (!register || !attend || !status) return;
        register.classList.toggle('hidden', status.registered);
        attend.disabled = !status.registered || !status.location_configured || status.action === 'completed';
        attend.textContent = status.action === 'check_out' ? 'Absen Pulang dengan Face ID' : status.action === 'completed' ? 'Presensi Hari Ini Selesai' : 'Absen Masuk dengan Face ID';
        const detail = !status.location_configured ? 'Titik koordinat kantor belum diatur. Hubungi Super Admin.' : status.action === 'completed' ? `Masuk ${status.time_in} · Pulang ${status.time_out}` : status.action === 'check_out' ? `Sudah masuk ${status.time_in}. Silakan absen pulang setelah bekerja.` : status.registered ? `Face ID dan lokasi siap digunakan (radius ${status.radius_meters} meter).` : 'Daftarkan wajah satu kali sebelum melakukan presensi.';
        message(detail);
    };
    const refresh = async () => { status = await api(routes.status); render(); };

    document.addEventListener('click', async event => {
        const register = event.target.closest('[data-daily-face-register]');
        const attend = event.target.closest('[data-daily-face-attend]');
        if (!register && !attend) return;
        const button = register || attend;
        button.disabled = true;
        try {
            message('Menyiapkan kamera...');
            let location = null;
            if (attend) {
                if (!navigator.geolocation) throw new Error('Browser tidak mendukung pembacaan lokasi.');
                message('Membaca lokasi presensi...');
                location = await new Promise((resolve, reject) => navigator.geolocation.getCurrentPosition(resolve, reject, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }));
            }
            const embeddings = await capture(register ? 'Pendaftaran Face ID' : 'Verifikasi Presensi');
            const payload = register ? { embeddings } : {
                embeddings,
                latitude: location.coords.latitude,
                longitude: location.coords.longitude,
                accuracy: location.coords.accuracy,
            };
            const result = await api(register ? routes.register : routes.attend, payload);
            message(result.message || 'Berhasil.');
            await refresh();
            if (attend) setTimeout(() => window.location.reload(), 900);
        } catch (error) {
            if (error.name !== 'AbortError') message(error.message || 'Face ID gagal diproses.', true);
            render();
        }
    });

    refresh().catch(error => message(error.message || 'Status Face ID gagal dimuat.', true));
})();
</script>
@endscript
