<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected ?Collection $shiftHistoryCache = null;

    protected $fillable = [
        'nik',
        'device_user_id',
        'nama',
        'email',
        'no_hp',
        'alamat',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
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
        'status',
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
            return $this->foto_is_base64 ? route('hris.employees.photo', $this).'?'.$this->updated_at->timestamp : null;
        }

        if (! $this->foto) {
            return null;
        }

        if (str_starts_with($this->foto, 'base64:')) {
            return route('hris.employees.photo', $this).'?'.$this->updated_at->timestamp;
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

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
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
        if (preg_match('/[-–—]\s*(\d{1,2})[.:](\d{2})\s*$/', $jamKerja, $m)) {
            $hour = (int) $m[1];
            $min = (int) $m[2];

            if ($hour >= 0 && $hour <= 23 && $min <= 59) {
                return $hour * 60 + $min;
            }
        }

        return null;
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
}
