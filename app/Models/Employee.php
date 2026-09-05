<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    public const SHIFT_PAGI = 'Shift Pagi (07.00-12.00)';

    public const SHIFT_SIANG = 'Shift Siang (13.00-18.00)';

    public const SHIFT_MALAM = 'Shift Malam (19.00-24.00)';

    public const SHIFT_SUBUH = 'Shift Subuh (01.00-06.00)';

    public const SHIFT_ADMIN_PAGI = 'Shift Admin Pagi (07.00-18.00)';

    public const SHIFT_ADMIN_MALAM = 'Shift Admin Malam (19.00-06.00)';

    public const NON_SHIFT = 'Non Shift (08.00-17.00)';

    public const JENIS_KERJA_OPERASIONAL = 'Operasional';

    public const JENIS_KERJA_OFFICE = 'Office';

    public const TIPE_KARYAWAN_AKTIF = 'karyawan_aktif';

    public const TIPE_CALON_KARYAWAN = 'calon_karyawan';

    public const TIPE_MANTAN_KARYAWAN = 'mantan_karyawan';

    public const TIPE_OPTIONS = [
        self::TIPE_KARYAWAN_AKTIF => 'Karyawan Aktif',
        self::TIPE_CALON_KARYAWAN => 'Calon Karyawan',
        self::TIPE_MANTAN_KARYAWAN => 'Mantan Karyawan',
    ];

    public const STATUS_MENIKAH = 'sudah menikah';

    public const STATUS_BELUM_MENIKAH = 'belum menikah';

    /**
     * Opsi status pernikahan (dropdown tab Informasi Pribadi).
     */
    public const STATUS_PERKAWINAN_OPTIONS = [
        self::STATUS_MENIKAH => 'Sudah Menikah',
        self::STATUS_BELUM_MENIKAH => 'Belum Menikah',
    ];

    /**
     * Daftar nama atasan yang bisa dipilih (dropdown Atasan 1 & Atasan 2).
     */
    public const ATASAN_OPTIONS = [
        'Gonzaga Gogo Silalahi',
        'Pamungkas Chris Hermanto',
        'Rinaldo Pardomuan Sinaga',
        'Novena Novri',
        'Yuliana Sventy Yasmine',
        'Ahmad Musyadad Haury',
        'Zulfa Rahmani',
        'Rizky Fahmi Hidayat',
        'Tasya Lutfiah Nur Azizah',
        'Kornelius Adrian',
        'Fiki Sugiana',
        'Fathan Muhamad Fauzan',
        'Mohamad Rafli Bahtiar',
        'Mochamad Rizal Hanapi',
        'Albert Christian Simanungkalit',
        'Ridwan Hasan Maulana',
        'Muhamad Rafly Firdaus',
        'Yogi Ginanjar',
        'Dhika Andara',
    ];

    /**
     * Opsi jenis kerja: label => keterangan pola hari kerja mingguan.
     * Menjadi acuan hari libur: Operasional masuk Senin-Minggu sesuai jam
     * kerja (tanpa libur mingguan), Office libur setiap hari Minggu.
     */
    public const JENIS_KERJA_OPTIONS = [
        self::JENIS_KERJA_OPERASIONAL => 'Masuk Senin-Minggu sesuai jam kerja (tanpa libur mingguan)',
        self::JENIS_KERJA_OFFICE => 'Libur mingguan setiap hari Minggu',
    ];

    /**
     * Opsi jam kerja baku (dropdown): label => jam mulai (acuan status telat).
     * Semua shift diberi toleransi keterlambatan +5 menit (lihat jamMasukCutoff).
     */
    public const SHIFT_OPTIONS = [
        self::SHIFT_PAGI => '07:00',
        self::SHIFT_SIANG => '13:00',
        self::SHIFT_MALAM => '19:00',
        self::SHIFT_SUBUH => '01:00',
        self::SHIFT_ADMIN_PAGI => '07:00',
        self::SHIFT_ADMIN_MALAM => '19:00',
        self::NON_SHIFT => '08:00',
    ];

    protected ?Collection $shiftHistoryCache = null;

    /**
     * Kolom ringan untuk query daftar/tabel: mengecualikan kolom foto karena
     * bisa berisi gambar base64 berukuran besar (megabyte per baris) sehingga
     * memuat banyak karyawan sekaligus dapat menghabiskan memori.
     * Flag foto_is_base64 dipakai accessor fotoUrl agar foto tetap tampil
     * melalui route streaming hris.employees.photo.
     */
    public const LIST_COLUMNS = [
        'id',
        'nik',
        'nama',
        'email',
        'no_hp',
        'position',
        'position_id',
        'atasan',
        'atasan2',
        'jenis_karyawan',
        'lokasi_kerja',
        'jenis_kerja',
        'jam_kerja',
        'jam_masuk',
        'jobdesk',
        'tipe',
        'status_bpjs',
        'tanggal_masuk',
        'tanggal_resign',
        'updated_at',
    ];

    public function scopeListSelect($query)
    {
        return $query
            ->select(array_map(fn (string $col) => "employees.{$col}", self::LIST_COLUMNS))
            ->selectRaw("CASE WHEN employees.foto LIKE 'base64:%' THEN 1 ELSE 0 END AS foto_is_base64");
    }

    protected $fillable = [
        'nik',
        'nik_ktp',
        'device_user_id',
        'nama',
        'email',
        'no_hp',
        'alamat',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'rt_rw',
        'kode_pos',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'ukuran_baju',
        'agama',
        'pendidikan_terakhir',
        'asal_sekolah',
        'informasi_lowongan',
        'position',
        'atasan',
        'atasan2',
        'jenis_karyawan',
        'lokasi_kerja',
        'jenis_kerja',
        'jam_kerja',
        'jam_masuk',
        'jobdesk',
        'no_kontak_darurat1',
        'hubungan_darurat1',
        'no_kontak_darurat2',
        'hubungan_darurat2',
        'no_bpjs',
        'status_bpjs',
        'tipe',
        'status_pernikahan',
        'tanggal_masuk',
        'tanggal_resign',
        'foto',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'tanggal_resign' => 'date',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'employee_id');
    }

    public function machineUserMappings(): HasMany
    {
        return $this->hasMany(EmployeeMachineUser::class);
    }

    public function allMachineUserIds(): array
    {
        $ids = $this->device_user_id ? [$this->device_user_id] : [];
        $ids = array_merge($ids, $this->machineUserMappings()->pluck('machine_user_id')->all());

        return array_values(array_unique($ids));
    }

    public static function findByMachineUserId(string $machineUserId): ?self
    {
        $employee = static::where('device_user_id', $machineUserId)->first();
        if ($employee) {
            return $employee;
        }

        return EmployeeMachineUser::query()
            ->where('machine_user_id', $machineUserId)
            ->first()
            ?->employee;
    }

    public function birthdayWishes(): HasMany
    {
        return $this->hasMany(BirthdayWish::class, 'employee_id');
    }

    public function getUserAttribute()
    {
        return $this->users->first();
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (! array_key_exists('foto', $this->attributes) && array_key_exists('foto_is_base64', $this->attributes)) {
            return $this->foto_is_base64 ? route('hris.employees.photo', $this).'?'.($this->updated_at?->timestamp ?? '') : null;
        }

        if (! $this->foto) {
            return null;
        }

        if (str_starts_with($this->foto, 'base64:')) {
            return route('hris.employees.photo', $this).'?'.($this->updated_at?->timestamp ?? '');
        }

        return asset('storage/employees/'.$this->foto);
    }

    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class, 'employee_division')
            ->withTimestamps();
    }

    public function divisionNames(): string
    {
        return $this->divisions->pluck('nama')->implode(' & ');
    }

    public function fullAddress(): string
    {
        $parts = [];

        if ($this->alamat) {
            $parts[] = $this->alamat;
        }
        if ($this->rt_rw) {
            $parts[] = 'RT/RW '.$this->rt_rw;
        }

        foreach (['kelurahan', 'kecamatan', 'kota', 'provinsi'] as $field) {
            if ($this->{$field}) {
                $parts[] = ucwords(strtolower($this->{$field}));
            }
        }

        if ($this->kode_pos) {
            $parts[] = $this->kode_pos;
        }

        return implode(', ', $parts);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function firstContractStart(): ?\Illuminate\Support\Carbon
    {
        return $this->contracts()->orderBy('tanggal_mulai', 'asc')->first()?->tanggal_mulai;
    }

    public function cutiEligibleDate(): ?\Illuminate\Support\Carbon
    {
        $start = $this->firstContractStart();

        return $start?->copy()->addYear()->startOfDay();
    }

    public function isCutiEligible(): bool
    {
        $eligibleDate = $this->cutiEligibleDate();

        return $eligibleDate !== null && $eligibleDate->lte(now());
    }

    /**
     * Akumulasi cuti tahunan: mulai 1 pada tanggal cuti aktif (bulan pertama),
     * bertambah 1 hari per bulan mengikuti tanggal cuti aktif, cap 12,
     * dan reset ke 1 tiap ulang tahun cuti.
     *
     * @return array{eligible: bool, cycle_start: ?\Illuminate\Support\Carbon, earned: int}
     */
    public function cutiAccrual(?\Illuminate\Support\Carbon $now = null): array
    {
        $now = $now ?? now();
        $eligibleDate = $this->cutiEligibleDate();

        if ($eligibleDate === null) {
            return ['eligible' => false, 'cycle_start' => null, 'earned' => 0];
        }

        if ($now->lt($eligibleDate)) {
            return ['eligible' => false, 'cycle_start' => $eligibleDate, 'earned' => 0];
        }

        $years = (int) $eligibleDate->diffInYears($now);
        $cycleStart = $eligibleDate->copy()->addYears($years)->startOfDay();
        $earned = min(12, (int) $cycleStart->diffInMonths($now) + 1);

        return ['eligible' => true, 'cycle_start' => $cycleStart, 'earned' => $earned];
    }

    public function positionHistories(): HasMany
    {
        return $this->hasMany(PositionHistory::class);
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class, 'employee_id');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function shiftHistories(): HasMany
    {
        return $this->hasMany(EmployeeShiftHistory::class)->orderBy('effective_date');
    }

    public function jamMasukCutoff(?string $date = null): string
    {
        return $this->minutesToTime($this->shiftStartMinutes($date) + 5);
    }

    /**
     * Shift yang berlaku pada tanggal tertentu: ['jam_kerja', 'jam_masuk'].
     * Memakai catatan pergantian shift terakhir yang berlaku, fallback ke nilai kini.
     *
     * @return array{jam_kerja: ?string, jam_masuk: ?string}
     */
    public function shiftOn(?string $date = null): array
    {
        $history = $this->shiftForDate($date);

        return $history
            ? ['jam_kerja' => $history->jam_kerja, 'jam_masuk' => $history->jam_masuk]
            : ['jam_kerja' => $this->jam_kerja, 'jam_masuk' => $this->jam_masuk];
    }

    public function recordShiftHistory(?string $jamKerja, ?string $jamMasuk, string $effectiveDate): EmployeeShiftHistory
    {
        $history = EmployeeShiftHistory::updateOrCreate(
            ['employee_id' => $this->id, 'effective_date' => $effectiveDate],
            ['jam_kerja' => $jamKerja ?: null, 'jam_masuk' => $jamMasuk ?: null]
        );

        $this->shiftHistoryCache = null;

        return $history;
    }

    public function setJamKerja(?string $jamKerja, ?string $jamMasuk, ?string $effectiveDate = null): void
    {
        $effectiveDate = $effectiveDate ?: now()->toDateString();

        $this->recordShiftHistory($jamKerja, $jamMasuk, $effectiveDate);

        if ($effectiveDate <= now()->toDateString()) {
            $this->jam_kerja = $jamKerja ?: null;
            $this->jam_masuk = $jamMasuk ?: null;
            $this->save();
        }
    }

    private function shiftStartMinutes(?string $date = null): int
    {
        $shift = $this->shiftOn($date);
        $isMalam = str_contains((string) ($this->position ?? ''), '(Malam)');

        return self::shiftStartFrom($shift['jam_kerja'], $shift['jam_masuk'], $isMalam);
    }

    public static function shiftStartFrom(?string $jamKerja, ?string $jamMasuk, bool $isMalamPosition): int
    {
        $jamKerja = (string) ($jamKerja ?? '');

        // Label shift baku dari dropdown jam kerja, mis. "Shift Pagi (07.00-12.00)".
        if (isset(self::SHIFT_OPTIONS[$jamKerja])) {
            $parts = explode(':', self::SHIFT_OPTIONS[$jamKerja]);

            return ((int) $parts[0] * 60) + (int) $parts[1];
        }

        // Rentang jam di mana pun dalam string: mencakup format lama yang
        // dimulai angka ("08.00-17.00") maupun label berformat
        // "Shift Pagi (07.00-12.00)" / "Senin - Jumat 08.00-17.00, ...".
        if (preg_match('/(\d{1,2})[.:](\d{2})\s*[-–—]\s*(\d{1,2})[.:](\d{2})/', $jamKerja, $m)) {
            $hour = (int) $m[1];
            $min = (int) $m[2];

            if ($hour >= 0 && $hour <= 23 && $min <= 59) {
                return $hour * 60 + $min;
            }
        }

        if (preg_match('/^\s*(\d{1,2})[.:](\d{2})\s*[-–—]/', $jamKerja, $m)) {
            $hour = (int) $m[1];
            $min = (int) $m[2];

            if ($hour >= 0 && $hour <= 23 && $min <= 59) {
                return $hour * 60 + $min;
            }
        }

        if ($jamMasuk) {
            $parts = explode(':', $jamMasuk);

            return ((int) ($parts[0] ?? 0) * 60) + (int) ($parts[1] ?? 0);
        }

        if ($isMalamPosition) {
            return 14 * 60;
        }

        return 9 * 60;
    }

    public static function shiftEndFrom(?string $jamKerja): ?int
    {
        $jamKerja = (string) ($jamKerja ?? '');

        // Rentang jam di mana pun dalam string; jam 24 (24.00/24:00) sah
        // sebagai tengah malam (= 1440 menit).
        if (preg_match('/(\d{1,2})[.:](\d{2})\s*[-–—]\s*(\d{1,2})[.:](\d{2})/', $jamKerja, $m)) {
            $hour = (int) $m[3];
            $min = (int) $m[4];

            if ($hour >= 0 && $hour <= 24 && $min <= 59 && ($hour < 24 || $min === 0)) {
                return $hour * 60 + $min;
            }
        }

        if (preg_match('/[-–—]\s*(\d{1,2})[.:](\d{2})\s*$/', $jamKerja, $m)) {
            $hour = (int) $m[1];
            $min = (int) $m[2];

            if ($hour >= 0 && $hour <= 24 && $min <= 59 && ($hour < 24 || $min === 0)) {
                return $hour * 60 + $min;
            }
        }

        return null;
    }

    /**
     * Apakah tanggal tertentu adalah hari libur mingguan karyawan,
     * berdasarkan jenis kerja:
     * - Operasional: tidak ada libur mingguan (masuk Senin-Minggu sesuai
     *   jam kerja).
     * - Office: libur setiap hari Minggu.
     */
    public function isWeeklyDayOff(?Carbon $date = null): bool
    {
        if (($this->jenis_kerja ?? '') !== self::JENIS_KERJA_OFFICE) {
            return false;
        }

        $date = $date ?: now();

        return (int) $date->format('N') === 7; // 7 = Minggu
    }

    public function isWorkday(?Carbon $date = null): bool
    {
        return ! $this->isWeeklyDayOff($date);
    }

    /**
     * Tanggal kerja untuk sebuah saat punch/absen.
     *
     * Karyawan shift Subuh (mulai sebelum 05:00, mis. "Shift Subuh
     * (01.00-06.00)") yang absen pada pukul 00:00-06:59 direkap pada tanggal
     * HARI SEBELUMNYA (ikut malam sebelumnya), konsisten dengan konvensi
     * sesi host live (config/hostlive.php) dan AttendanceSyncService.
     * Contoh: masuk-pulang dini hari 22 Agustus tercatat sebagai presensi
     * 21 Agustus.
     */
    public function resolveWorkDate(?Carbon $at = null): string
    {
        $at = $at ?: now();

        $isSubuh = str_contains((string) $this->position, '(Subuh)')
            || $this->shiftStartMinutes($at->toDateString()) < 5 * 60;

        if ($isSubuh && (int) $at->format('G') < 7) {
            return $at->copy()->subDay()->toDateString();
        }

        return $at->toDateString();
    }

    private function shiftForDate(?string $date): ?EmployeeShiftHistory
    {
        $histories = $this->loadedShiftHistories();

        if ($histories->isEmpty()) {
            return null;
        }

        $date = $date ?: now()->toDateString();

        $applicable = null;
        foreach ($histories as $history) {
            if ($history->effective_date->toDateString() <= $date) {
                $applicable = $history;
            } else {
                break;
            }
        }

        return $applicable;
    }

    private function loadedShiftHistories(): Collection
    {
        if ($this->shiftHistoryCache === null) {
            $this->shiftHistoryCache = $this->shiftHistories()->get();
        }

        return $this->shiftHistoryCache;
    }

    private function minutesToTime(int $minutes): string
    {
        $minutes = $minutes % (24 * 60);
        $h = str_pad((int) floor($minutes / 60), 2, '0', STR_PAD_LEFT);
        $i = str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);

        return $h.':'.$i.':00';
    }

    public function meetingRequests(): HasMany
    {
        return $this->hasMany(MeetingRequest::class);
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'employee_position')
            ->withPivot('is_main')
            ->withTimestamps();
    }

    public function mainPosition(): ?Position
    {
        return $this->positions()->wherePivot('is_main', true)->first();
    }

    public function hasMultiplePositions(): bool
    {
        return $this->positions()->count() > 1;
    }

    public function positionNames(): string
    {
        return $this->positions->pluck('nama')->implode(' & ');
    }

    public static function composePositionString(array $positionIds): string
    {
        if (empty($positionIds)) {
            return '';
        }

        return Position::whereIn('id', $positionIds)->pluck('nama')->implode(' & ');
    }

    public static function composeJobdesk(array $positionIds, ?int $mainPositionId): string
    {
        if (empty($positionIds)) {
            return '';
        }

        $positions = Position::whereIn('id', $positionIds)->get()
            ->sortBy('nama')
            ->sortBy(fn ($pos) => $pos->id == (int) $mainPositionId ? 0 : 1);

        $blocks = $positions->map(function ($pos) {
            $judul = strtoupper($pos->nama);
            $deskripsi = trim(strip_tags($pos->deskripsi ?: ''));

            return $judul.':'.PHP_EOL.($deskripsi === '' ? '-' : $deskripsi);
        });

        return $blocks->implode(PHP_EOL.PHP_EOL);
    }

    public static function syncSnapshotsForPosition(int $positionId): int
    {
        $employeeIds = DB::table('employee_position')
            ->where('position_id', $positionId)
            ->pluck('employee_id');

        if ($employeeIds->isEmpty()) {
            return 0;
        }

        $grouped = DB::table('employee_position')
            ->whereIn('employee_id', $employeeIds->unique())
            ->get()
            ->groupBy('employee_id');

        $updated = 0;

        foreach ($grouped as $employeeId => $rows) {
            $ids = $rows->pluck('position_id')->all();
            $mainRow = $rows->firstWhere('is_main', true);
            $positionStr = self::composePositionString($ids) ?: null;
            $jobdesk = self::composeJobdesk($ids, $mainRow?->position_id) ?: null;

            $current = DB::table('employees')
                ->where('id', $employeeId)
                ->first(['position', 'jobdesk']);

            if (! $current || ($current->position === $positionStr && $current->jobdesk === $jobdesk)) {
                continue;
            }

            DB::table('employees')->where('id', $employeeId)->update([
                'position' => $positionStr,
                'jobdesk' => $jobdesk,
            ]);

            $updated++;
        }

        return $updated;
    }

    public function evaluationLevel(): int
    {
        $positionName = $this->mainPosition()?->nama ?: $this->position;
        $name = strtolower((string) $positionName);

        if ($name === 'ceo') {
            return 5;
        }
        if (str_contains($name, 'general manager')) {
            return 4;
        }
        if (str_starts_with($name, 'head of store')) {
            return 3;
        }
        if (str_starts_with($name, 'koordinator')) {
            return 2;
        }

        return 1;
    }
}
