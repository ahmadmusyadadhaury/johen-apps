<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batas lintas hari (overnight checkout window)
    |--------------------------------------------------------------------------
    | Saat ada sesi presensi yang masih terbuka (sudah ada jam masuk, belum ada
    | jam keluar) untuk karyawan yang sama, scan berikutnya dianggap JAM KELUAR
    | dari sesi tersebut selama masih berada dalam "jendela lintas malam".
    |
    | Jendela dihitung dari konfigurasi jam kerja karyawan (shiftOn):
    |   - Jika jam kerja punya waktu selesai (mis. "22:00-06:00"), batas akhir
    |     = waktu selesai shift + overnight_buffer_minutes. Shift yang melewati
    |     tengah malam otomatis dihitung pada hari berikutnya tanggal masuk.
    |   - Jika tidak ada konfigurasi jam kerja, batas akhir = jam masuk sesi
    |     + max_session_hours, DAN jam scan tidak boleh melewati
    |     overnight_latest_checkout_hour (scan pagi yang lebih telat dianggap
    |     kedatangan baru, bukan jam pulang sesi kemarin).
    |
    | Semua waktu diproses sebagai datetime penuh sesuai timezone aplikasi.
    */

    'overnight_buffer_minutes' => (int) env('ATTENDANCE_OVERNIGHT_BUFFER_MINUTES', 60),

    'max_session_hours' => (int) env('ATTENDANCE_MAX_SESSION_HOURS', 16),

    /*
    |--------------------------------------------------------------------------
    | Batas jam checkout lintas malam (fallback tanpa jam kerja)
    |--------------------------------------------------------------------------
    | Untuk karyawan tanpa konfigurasi jam kerja, scan pada hari berikutnya
    | hanya dianggap JAM KELUAR dari sesi terbuka kemarin bila jamnya masih
    | sebelum nilai ini (jam, 0-23, default 7 = 07:00). Scan yang lebih telat
    | dianggap absen datang hari baru. Konsisten dengan konvensi < 07:00 yang
    | dipakai pada deteksi sesi Subuh dan perintah attendance:fix-malam.
    */

    'overnight_latest_checkout_hour' => (int) env('ATTENDANCE_OVERNIGHT_LATEST_CHECKOUT_HOUR', 7),

    /*
    |--------------------------------------------------------------------------
    | Rentang jam masuk yang masuk akal
    |--------------------------------------------------------------------------
    | Dipakai untuk mendeteksi sesi "palsu": punch yang sebenarnya adalah absen
    | pulang (tanpa absen datang) namun terekam sebagai jam masuk. Sesi terbuka
    | dari hari sebelumnya dianggap PALSU bila jam masuknya berada di luar
    | rentang [jam mulai shift - checkin_early_arrival_minutes,
    | jam mulai shift + checkin_late_tolerance_minutes].
    */

    'checkin_early_arrival_minutes' => (int) env('ATTENDANCE_CHECKIN_EARLY_ARRIVAL_MINUTES', 120),

    'checkin_late_tolerance_minutes' => (int) env('ATTENDANCE_CHECKIN_LATE_TOLERANCE_MINUTES', 240),

    /*
    |--------------------------------------------------------------------------
    | Ambang tap ganda pada modal Detail Absen
    |--------------------------------------------------------------------------
    | Punch berurutan dengan jeda lebih kecil dari nilai ini (detik) dianggap
    | satu rangkaian tap yang sama (mis. dobel tap datang). Hanya punch pertama
    | rangkaian berlabel "Datang" dan pembuka rangkaian terakhir berlabel
    | "Pulang"; anggota rangkaian lain berlabel "Tap".
    */

    'tap_duplicate_window_seconds' => (int) env('ATTENDANCE_TAP_DUPLICATE_WINDOW_SECONDS', 180),

];
