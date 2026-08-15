<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceSyncService
{
    public function recordPunch(string $machineUserId, string $punchAt, string $method = 'finger', ?string $machineSerial = null, ?array $raw = null): array
    {
        $punchAt = Carbon::parse($punchAt);
        $employee = Employee::findByMachineUserId($machineUserId);

        $duplicate = AttendancePunch::where('machine_user_id', $machineUserId)
            ->where('punch_at', $punchAt->format('Y-m-d H:i:s'))
            ->first();

        if ($duplicate) {
            if ($duplicate->employee_id === null && $employee) {
                $duplicate->employee_id = $employee->id;
                $duplicate->save();
                $this->applyToAttendance($employee, $punchAt, $method);

                return ['status' => 'ok', 'employee_id' => $employee->id, 'machine_user_id' => $machineUserId];
            }

            return ['status' => 'duplicate', 'machine_user_id' => $machineUserId];
        }

        AttendancePunch::create([
            'machine_user_id' => $machineUserId,
            'employee_id' => $employee?->id,
            'punch_at' => $punchAt,
            'method' => $method,
            'machine_serial' => $machineSerial,
            'raw_data' => $raw,
        ]);

        if (! $employee) {
            return ['status' => 'unmatched', 'machine_user_id' => $machineUserId];
        }

        $this->applyToAttendance($employee, $punchAt, $method);

        return ['status' => 'ok', 'employee_id' => $employee->id, 'machine_user_id' => $machineUserId];
    }

    public function backfillForUser(string $machineUserId): array
    {
        $employee = Employee::findByMachineUserId($machineUserId);
        if (! $employee) {
            return ['processed' => 0, 'unmatched' => 1];
        }

        $punches = AttendancePunch::where('machine_user_id', $machineUserId)
            ->whereNull('employee_id')
            ->orderBy('punch_at')
            ->get();

        foreach ($punches as $punch) {
            $this->recordPunch(
                $punch->machine_user_id,
                $punch->punch_at->format('Y-m-d H:i:s'),
                $punch->method,
                $punch->machine_serial,
                $punch->raw_data,
            );
        }

        return ['processed' => $punches->count(), 'unmatched' => 0];
    }

    private function applyToAttendance(Employee $employee, Carbon $punchAt, string $method): void
    {
        $time = $punchAt->format('H:i:s');

        // Dobel tap: punch yang datang <90 detik setelah punch terakhir
        // karyawan ini diabaikan. Mencakup dobel tap saat masuk, maupun saat
        // pulang lintas malam (mis. scan 00:44:52 menutup sesi tgl-14, lalu
        // scan dobel 00:44:55 tidak boleh membuat rekap palsu di tgl-15).
        if ($this->isDoubleTapFromLastPunch($employee, $punchAt)) {
            return;
        }

        // Sesi Subuh (punch 00:00-06:59) untuk karyawan shift Subuh tercatat
        // pada tanggal HARI SEBELUMNYA (ikut malam sebelumnya), mengikuti
        // konvensi sesi host live (config/hostlive.php). Contoh: masuk 00:24
        // tanggal 15 tercatat sebagai absen tanggal 14, bukan tanggal 15.
        $isSubuhPunch = $this->isSubuhShift($employee, $punchAt)
            && (int) $punchAt->format('G') < 7;

        $punchDate = $isSubuhPunch
            ? $punchAt->copy()->subDay()->toDateString()
            : $punchAt->toDateString();

        // 1. Cari sesi presensi yang masih terbuka (sudah ada jam masuk, belum
        //    ada jam keluar) untuk karyawan ini.
        $open = Attendance::where('employee_id', $employee->id)
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->where('status', 'hadir')
            ->orderByDesc('date')
            ->first();

        // 2. Jika sesi terbuka berasal dari tanggal lebih awal dan scan baru
        //    masih dalam "jendela lintas malam", scan itu adalah JAM KELUAR dari
        //    sesi tersebut (mis. masuk 01-08 22:00, pulang 02-08 02:00), bukan
        //    absen masuk baru hanya karena tanggal kalendernya sudah berganti.
        if ($open && $open->date->toDateString() < $punchDate && $this->isWithinOvernightWindow($employee, $open, $punchAt)) {
            $open->time_out = $time;
            $open->method = $method;
            $open->save();

            return;
        }

        // 3. Logic hari yang sama (perilaku lama dipertahankan).
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $punchDate)
            ->first();

        if (! $attendance) {
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $punchDate,
                'time_in' => $time,
                'time_out' => null,
                'status' => 'hadir',
                'method' => $method,
            ]);

            return;
        }

        if ($attendance->status !== 'hadir') {
            return;
        }

        if ($attendance->time_in === null) {
            $attendance->time_in = $time;
        } elseif ($time < $attendance->time_in) {
            $attendance->time_in = $time;
        } elseif ($attendance->time_out === null) {
            $attendance->time_out = $time;
        } elseif ($time > $attendance->time_out) {
            $attendance->time_out = $time;
        }

        $attendance->method = $method;
        $attendance->save();
    }

    /**
     * Menentukan apakah scan baru masih wajar menjadi JAM KELUAR dari sesi
     * terbuka yang dimulai pada tanggal/ jam masuk sebelumnya.
     *
     * Memakai datetime penuh (tanggal + jam), bukan hanya jam atau tanggal,
     * dan memakai konfigurasi jam kerja/shift karyawan yang berlaku pada
     * tanggal sesi tersebut dimulai.
     */
    private function isWithinOvernightWindow(Employee $employee, Attendance $open, Carbon $punchAt): bool
    {
        $sessionStart = Carbon::parse($open->date->toDateString().' '.$open->time_in);

        // Scan sebelum sesi dimulai bukan jam keluar dari sesi tersebut.
        if ($punchAt->lte($sessionStart)) {
            return false;
        }

        $shift = $employee->shiftOn($open->date->toDateString());
        $endMinutes = Employee::shiftEndFrom($shift['jam_kerja']);

        if ($endMinutes !== null) {
            $maxEnd = Carbon::parse($open->date->toDateString().' 00:00:00')
                ->addMinutes($endMinutes)
                ->addMinutes((int) config('attendance.overnight_buffer_minutes', 60));

            // Shift yang melewati tengah malam (mis. "22:00-06:00"): waktu
            // selesainya berada pada hari berikutnya tanggal masuk.
            $startMinutes = Employee::shiftStartFrom(
                $shift['jam_kerja'],
                $shift['jam_masuk'],
                str_contains((string) $employee->position, '(Malam)'),
            );
            if ($endMinutes < $startMinutes) {
                $maxEnd->addDay();
            }
        } else {
            // Tanpa konfigurasi jam kerja, gunakan batas aman yang mudah
            // diubah lewat config('attendance.max_session_hours').
            $maxEnd = $sessionStart->copy()->addHours((int) config('attendance.max_session_hours', 16));
        }

        return $punchAt->lte($maxEnd);
    }

    /**
     * Menentukan apakah karyawan bekerja pada shift Subuh (pagi buta setelah
     * tengah malam). Sesi Subuh dianggap ikut malam sebelumnya, sehingga
     * punch 00:00-06:59 milik mereka tercatat pada tanggal hari sebelumnya.
     */
    private function isSubuhShift(Employee $employee, Carbon $punchAt): bool
    {
        if (str_contains((string) $employee->position, '(Subuh)')) {
            return true;
        }

        $shift = $employee->shiftOn($punchAt->toDateString());
        $startMinutes = Employee::shiftStartFrom(
            $shift['jam_kerja'],
            $shift['jam_masuk'],
            str_contains((string) $employee->position, '(Malam)'),
        );

        if ($startMinutes < 7 * 60) {
            return true;
        }

        // Fallback pola punch: karyawan yang secara konsisten scan hanya pada
        // 00:00-06:59 (sesi Subuh) tetapi jabatan/jam kerjanya tidak
        // mencantumkan "(Subuh)" harus tetap dianggap shift Subuh. Karyawan
        // Malam biasa punya punch sore (in) + dini hari (out), sehingga
        // fraksi scan <07:00 tidak sampai sebesar ini.
        $from = $punchAt->copy()->subDays((int) config('attendance.subuh_pattern_days', 45))->startOfDay();
        $total = AttendancePunch::where('employee_id', $employee->id)
            ->where('punch_at', '>=', $from->toDateTimeString())
            ->where('punch_at', '<', $punchAt->toDateTimeString())
            ->count();

        if ($total >= (int) config('attendance.subuh_pattern_min_punches', 10)) {
            $early = AttendancePunch::where('employee_id', $employee->id)
                ->where('punch_at', '>=', $from->toDateTimeString())
                ->where('punch_at', '<', $punchAt->toDateTimeString())
                ->whereRaw('TIME(punch_at) < "07:00:00"')
                ->count();

            if ($early / $total >= (float) config('attendance.subuh_pattern_threshold', 0.65)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rekonstruksi ulang seluruh catatan presensi seorang karyawan dari
     * punch mesin secara kronologis. Dipakai untuk memperbaiki data lama
     * setelah ada perubahan aturan atribusi tanggal (mis. sesi Subuh).
     */
    public function rebuildEmployeeAttendance(Employee $employee): int
    {
        Attendance::where('employee_id', $employee->id)->delete();

        $punches = AttendancePunch::where('employee_id', $employee->id)
            ->orderBy('punch_at')
            ->get();

        foreach ($punches as $punch) {
            $this->applyToAttendance(
                $employee,
                Carbon::parse($punch->punch_at),
                $punch->method,
            );
        }

        return $punches->count();
    }

    /**
     * Menentukan apakah scan baru adalah dobel tap dari punch terakhir
     * karyawan yang sama (selisih <90 detik). Punch seperti itu diabaikan
     * agar tidak menciptakan sesi rekap palsu atau durasi 0j 0m.
     */
    private function isDoubleTapFromLastPunch(Employee $employee, Carbon $punchAt): bool
    {
        $lastPunch = AttendancePunch::where('employee_id', $employee->id)
            ->where('punch_at', '<', $punchAt->toDateTimeString())
            ->orderByDesc('punch_at')
            ->first();

        if (! $lastPunch) {
            return false;
        }

        return abs($punchAt->getTimestamp() - $lastPunch->punch_at->getTimestamp()) < 90;
    }
}
