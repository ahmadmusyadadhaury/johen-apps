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

    /**
     * Deteksi karyawan yang datanya terkena bug "tap pulang saja": ada rekap
     * dengan jam masuk yang tidak masuk akal sebagai absen datang (mis. sore
     * hari) namun jam keluar sudah terisi — hasil tap pulang yang keliru
     * terekam sebagai masuk lalu scan keesokan harinya tertelan jadi jam
     * pulang.
     *
     * Selain itu deteksi pola sebaliknya pada karyawan shift malam: tap
     * pulang dini hari terekam sebagai absen masuk (karena malam
     * sebelumnya dia tidak tap masuk sehingga tidak ada sesi terbuka yang
     * bisa ditutup), lalu absen masuk malam harinya menutup rekaman itu
     * sebagai jam keluar — menghasilkan durasi tidak wajar (mis.
     * 00:58 -> 18:59 = 18 jam).
     */
    public function hasCheckoutOnlyMisPairing(Employee $employee): bool
    {
        $records = Attendance::where('employee_id', $employee->id)
            ->where('status', 'hadir')
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->get();

        return $records->contains(function (Attendance $a) use ($employee) {
            $isMalamPosition = str_contains((string) $employee->position, '(Malam)');

            if ($a->time_out < $a->time_in) {
                // Pola lama: jam keluar lebih awal dari jam masuk. Karyawan
                // shift Malam dikecualikan karena sesinya memang lintas
                // tengah malam (pulang lebih awal dari masuk).
                return ! $isMalamPosition
                    && ! $this->isPlausibleCheckInForShift($employee, $a->date, $a->time_in);
            }

            // Pola baru: jam masuk dini hari (< batas jam checkout) dengan
            // durasi lebih dari 8 jam sampai jam keluar di sore/malam hari —
            // ciri khas tap pulang yang tertukar menjadi absen masuk.
            return $this->isOvernightCheckoutShift($employee, $a->date)
                && $a->time_in < sprintf('%02d:00:00', (int) config('attendance.overnight_latest_checkout_hour', 7))
                && $this->sessionMinutes($a->time_in, $a->time_out) > 8 * 60;
        });
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

        // 2. Jika sesi terbuka berasal dari tanggal lebih awal, tentukan dulu
        //    apakah sesi itu "palsu" (hasil tap pulang saja yang keliru terekam
        //    sebagai absen datang, mis. pulang 17-08 16:06 tanpa absen datang).
        //    Sesi palsu diubah menjadi rekap "absen pulang saja" (time_in kosong)
        //    dan scan baru dilanjutkan sebagai absen datang hari baru. Karyawan
        //    shift Malam dikecualikan: tap malam hari bagi mereka adalah absen
        //    datang yang sah.
        //
        //    Baru setelah itu, sesi yang sah (masuknya masuk akal) dicek ke
        //    "jendela lintas malam": scan pagi dini hari yang masih dalam
        //    jendela itu adalah JAM KELUAR dari sesi tersebut (mis. masuk
        //    01-08 22:00, pulang 02-08 02:00), bukan absen masuk baru hanya
        //    karena tanggal kalendernya sudah berganti.
        if ($open && $open->date->toDateString() < $punchDate) {
            $isFabricated = ! str_contains((string) $employee->position, '(Malam)')
                && ! $this->isPlausibleCheckInForShift($employee, $open->date, $open->time_in);

            if ($isFabricated) {
                $pulang = $open->time_in;
                $open->time_in = null;
                $open->time_out = $pulang;
                $open->method = $method;
                $open->save();
            } elseif ($this->isWithinOvernightWindow($employee, $open, $punchAt)) {
                $open->time_out = $time;
                $open->method = $method;
                $open->save();

                return;
            }
        }

        // 2b. Punch dini hari (< batas jam checkout) dari karyawan shift yang
        //     melewati tengah malam, ketika tidak ada sesi terbuka yang masih
        //     relevan untuk ditutupnya (biasanya karena dia lupa tap masuk
        //     malam sebelumnya), adalah absen PULANG dari sesi malam
        //     sebelumnya. Rekap pada tanggal sebelumnya sebagai jam keluar —
        //     bukan absen masuk baru pada tanggal kalender punch. Contoh:
        //     host lupa tap masuk 18-08, tap pulang 00:58 19-08 menjadi jam
        //     keluar rekap tanggal 18.
        //
        //     Sesi terbuka TUA (tanggal lebih lawas dari kemarin) diabaikan:
        //     sesi seperti itu sudah tidak mungkin ditutup oleh punch ini
        //     (jendela waktunya jauh terlewat) dan tidak boleh memblokir
        //     atribusi checkout ke tanggal kemarin.
        $yesterdayDate = $punchAt->copy()->subDay()->toDateString();
        $hasRelevantOpen = $open !== null && $open->date->toDateString() >= $yesterdayDate;

        if (! $isSubuhPunch
            && ! $hasRelevantOpen
            && (int) $punchAt->format('G') < (int) config('attendance.overnight_latest_checkout_hour', 7)
            && $this->isOvernightCheckoutShift($employee, $punchAt)) {
            $prev = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $yesterdayDate)
                ->first();

            // Hari sebelumnya tercatat izin/sakit/alpha manual: pertahankan,
            // lanjutkan dengan perilaku lama.
            if (! $prev || $prev->status === 'hadir') {
                if (! $prev) {
                    $prev = new Attendance([
                        'employee_id' => $employee->id,
                        'date' => $yesterdayDate,
                        'status' => 'hadir',
                    ]);
                }

                if ($prev->time_out === null || $time > $prev->time_out) {
                    $prev->time_out = $time;
                    $prev->method = $method;
                }

                $prev->save();

                return;
            }
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

        if ($attendance->time_in === null && $attendance->time_out !== null) {
            // Rekap "absen pulang saja" (time_in kosong): scan yang lebih awal
            // dianggap absen datang, scan yang lebih telat memperbarui jam pulang.
            if ($time < $attendance->time_out) {
                $attendance->time_in = $time;
            } else {
                $attendance->time_out = $time;
            }
        } elseif ($attendance->time_in === null) {
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
     * Apakah shift karyawan pada tanggal tertentu berprofil melewati (atau
     * berakhir tepat di) tengah malam — mis. "Shift Malam (19.00-24.00)"
     * maupun "Shift Admin Malam (19.00-06.00)" — sehingga punch dini hari
     * mereka adalah absen pulang, bukan absen masuk.
     *
     * Shift Subuh tidak masuk di sini karena punch dini hari mereka adalah
     * absen MASUK (ditangani atribusi tanggal kemarin oleh isSubuhPunch).
     */
    private function isOvernightCheckoutShift(Employee $employee, Carbon $date): bool
    {
        $shift = $employee->shiftOn($date->toDateString());
        $startMinutes = Employee::shiftStartFrom(
            $shift['jam_kerja'],
            $shift['jam_masuk'],
            str_contains((string) $employee->position, '(Malam)'),
        );
        $endMinutes = Employee::shiftEndFrom($shift['jam_kerja']);

        if ($startMinutes === null || $endMinutes === null) {
            return false;
        }

        return $endMinutes >= 24 * 60 || $endMinutes < $startMinutes;
    }

    private function sessionMinutes(string $timeIn, string $timeOut): int
    {
        $toMinutes = fn (string $t) => ((int) substr($t, 0, 2) * 60) + (int) substr($t, 3, 2);

        return abs($toMinutes($timeOut) - $toMinutes($timeIn));
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

            // Shift sore/malam (mulai siang ke atas) maupun yang selesai
            // tengah malam: checkout yang melebihi hari tetap menutup sesi
            // pada tanggal masuk, selama scan-nya tidak melewati batas dini
            // hari (overnight_latest_checkout_hour, default 07:00). Contoh:
            // masuk 19.05 tanggal 21, pulang 03.00 tanggal 22 tetap direkap
            // sebagai jam keluar presensi tanggal 21.
            if ($startMinutes >= 12 * 60 || $endMinutes >= 24 * 60 || $endMinutes < $startMinutes) {
                $latest = Carbon::parse($open->date->toDateString().' 00:00:00')
                    ->addDay()
                    ->setTime((int) config('attendance.overnight_latest_checkout_hour', 7), 0);

                if ($latest->gt($maxEnd)) {
                    $maxEnd = $latest;
                }
            }
        } else {
            // Tanpa konfigurasi jam kerja, gunakan batas aman yang mudah
            // diubah lewat config('attendance.max_session_hours'). Scan pada
            // hari berikutnya yang jamnya sudah melewati batas jam checkout
            // (overnight_latest_checkout_hour, default 07:00) dianggap absen
            // datang baru, bukan jam pulang sesi kemarin.
            if ((int) $punchAt->format('G') >= (int) config('attendance.overnight_latest_checkout_hour', 7)) {
                return false;
            }

            $maxEnd = $sessionStart->copy()->addHours((int) config('attendance.max_session_hours', 16));
        }

        return $punchAt->lte($maxEnd);
    }

    /**
     * Menentukan apakah jam masuk sebuah sesi masuk akal sebagai ABSEN DATANG
     * untuk karyawan tersebut (berdasarkan shift yang berlaku pada tanggal
     * sesi itu dimulai). Dipakai untuk mendeteksi sesi "palsu" yang terbentuk
     * dari tap pulang saja (tanpa absen datang).
     *
     * Jam masuk sebelum 07:00 dianggap selalu masuk akal (bisa absen datang
     * shift Subuh maupun jam pulang lintas malam). Jam masuk 18:00 ke atas
     * juga dianggap masuk akal karena tidak bisa dibedakan dari absen datang
     * shift malam/sore. Hanya jam di antara keduanya yang dicek ke rentang
     * jam mulai shift karyawan.
     */
    private function isPlausibleCheckInForShift(Employee $employee, Carbon $sessionDate, string $timeIn): bool
    {
        $parts = explode(':', $timeIn);
        $minutes = ((int) ($parts[0] ?? 0) * 60) + (int) ($parts[1] ?? 0);

        if ($minutes < 7 * 60 || $minutes >= 18 * 60) {
            return true;
        }

        $shift = $employee->shiftOn($sessionDate->toDateString());
        $isMalam = str_contains((string) $employee->position, '(Malam)');
        $start = Employee::shiftStartFrom($shift['jam_kerja'], $shift['jam_masuk'], $isMalam);

        $min = $start - (int) config('attendance.checkin_early_arrival_minutes', 120);
        $max = $start + (int) config('attendance.checkin_late_tolerance_minutes', 240);

        return $minutes >= $min && $minutes <= $max;
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

        // Mulai sebelum 05:00 = shift Subuh (mis. "Shift Subuh (01.00-06.00)").
        // Shift Pagi yang mulai 06.00 TIDAK termasuk agar punch paginya tidak
        // salah tercatat pada hari sebelumnya.
        if ($startMinutes < 5 * 60) {
            return true;
        }

        // Karyawan dengan shift malam/lintas tengah malam yang jelas tidak
        // boleh dianggap Subuh oleh fallback di bawah: punch dini hari mereka
        // adalah absen PULANG, bukan absen masuk subuh. Tanpa ini, duplikasi
        // punch dini hari membuat fraksi scan <07:00 melampaui ambang dan
        // seluruh sesi malam mereka tertukar menjadi pasangan
        // [pulang-dini-hari -> masuk-sore].
        if ($this->isOvernightCheckoutShift($employee, $punchAt)) {
            return false;
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
     *
     * Bila $preserveManual true, record yang bukan berasal dari mesin
     * (method != 'mesin', atau status selain hadir) TIDAK dihapus, sehingga
     * absen manual/izin/sakit/alpha/cuti tetap dipertahankan.
     */
    public function rebuildEmployeeAttendance(Employee $employee, bool $preserveManual = false): int
    {
        $query = Attendance::where('employee_id', $employee->id);

        if ($preserveManual) {
            $query->where('status', 'hadir')->where('method', 'mesin')->delete();
        } else {
            $query->delete();
        }

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
