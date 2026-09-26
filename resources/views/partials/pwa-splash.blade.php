{{--
    Overlay splash dengan logo yang berputar 360deg (efek yang sama dengan
    flipAngle di sidebar). Styling ada di partials/pwa-head.blade.php supaya
    sudah ter-parse sebelum elemen ini digambar.

    Elemen ini SELALU dirender, tapi default-nya tidak terlihat. Script di
    head yang menyalakannya lewat atribut data-splash pada <html>, supaya
    tidak ada kedipan di kunjungan berikutnya.

    Sifat penting: pointer-events ikut nonaktif saat disembunyikan, jadi
    overlay ini tidak akan pernah menghalangi klik user.
--}}
<div id="johen-splash" role="status" aria-label="Memuat Johen Apps">
    <div class="johen-splash-stage">
        <div class="johen-splash-glow"></div>
        <div class="johen-splash-sweep"></div>
        <img
            src="{{ asset('logo.png') }}"
            alt="Johen Sukses Abadi"
            class="johen-splash-logo"
            width="190"
            height="190"
            decoding="sync"
            fetchpriority="high"
        >
        <div class="johen-splash-text">
            <div class="johen-splash-title">JOHEN APPS</div>
            <div class="johen-splash-sub">Management System</div>
        </div>
    </div>

    <div class="johen-splash-bar"></div>
</div>
