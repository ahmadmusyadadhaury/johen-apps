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
    |     + max_session_hours.
    |
    | Semua waktu diproses sebagai datetime penuh sesuai timezone aplikasi.
    */

    'overnight_buffer_minutes' => (int) env('ATTENDANCE_OVERNIGHT_BUFFER_MINUTES', 60),

    'max_session_hours' => (int) env('ATTENDANCE_MAX_SESSION_HOURS', 16),

];
