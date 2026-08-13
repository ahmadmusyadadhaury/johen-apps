<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSession extends Model
{
    protected $fillable = [
        'employee_id',
        'tanggal',
        'sesi',
        'clock_in',
        'clock_out',
        'status',
        'late_minutes',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'sesi' => 'integer',
            'clock_in' => 'string',
            'clock_out' => 'string',
            'late_minutes' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Deteksi Sesi dari jam.
     * Jam 00:00-06:59 => Sesi 4, tanggal = HARI KEMARIN (ikut malam sebelumnya).
     * Jam 07:00-12:59 => Sesi 1; 13:00-18:59 => Sesi 2; 19:00-23:59 => Sesi 3.
     */
    public static function detect(Carbon $time): array
    {
        $hour = (int) $time->format('G');

        if ($hour < 7) {
            return ['sesi' => 4, 'tanggal' => $time->copy()->subDay()->toDateString()];
        }
        if ($hour < 13) {
            return ['sesi' => 1, 'tanggal' => $time->toDateString()];
        }
        if ($hour < 19) {
            return ['sesi' => 2, 'tanggal' => $time->toDateString()];
        }

        return ['sesi' => 3, 'tanggal' => $time->toDateString()];
    }

    public static function detectNow(): array
    {
        return static::detect(now());
    }

    public static function sessions(): array
    {
        return config('hostlive.sessions', []);
    }

    public static function sessionConfig(int $sesi): ?array
    {
        return config("hostlive.sessions.{$sesi}");
    }

    public static function sessionConfigByNama(string $nama): ?array
    {
        foreach (static::sessions() as $config) {
            if (($config['nama'] ?? null) === $nama) {
                return $config;
            }
        }

        return null;
    }

    public static function hitungTelat(int $sesi, ?string $clockIn): int
    {
        if (! $clockIn) {
            return 0;
        }

        $mulai = config("hostlive.sessions.{$sesi}.mulai");
        if (! $mulai) {
            return 0;
        }

        $grace = (int) config('hostlive.grace_minutes', 30);
        $batas = Carbon::parse($mulai)->addMinutes($grace);
        $jam = Carbon::parse($clockIn);

        return $jam->greaterThan($batas) ? (int) $batas->diffInMinutes($jam, true) : 0;
    }

    public function jamMulai(): ?string
    {
        return config("hostlive.sessions.{$this->sesi}.mulai");
    }

    public function jamSelesai(): ?string
    {
        return config("hostlive.sessions.{$this->sesi}.selesai");
    }

    public function jamSelesaiDisplay(): ?string
    {
        return config("hostlive.sessions.{$this->sesi}.selesai_display", $this->jamSelesai());
    }

    public function namaSesi(): string
    {
        return config("hostlive.sessions.{$this->sesi}.label", "Sesi {$this->sesi}");
    }

    public function displayStatus(): string
    {
        if ($this->status !== 'hadir') {
            return $this->status;
        }

        return $this->isTelat() ? 'terlambat' : 'tepat waktu';
    }

    public function isTelat(): bool
    {
        return $this->late_minutes > 0;
    }

    public function getDurasiAttribute(): ?string
    {
        if (! $this->clock_in || ! $this->clock_out) {
            return null;
        }

        $in = Carbon::parse($this->clock_in);
        $out = Carbon::parse($this->clock_out);

        // Check-out setelah tengah malam (mis. Sesi 3 pulang jam 00:30) tetap
        // dihitung pada tanggal yang sama dengan check-in, jadi tambah 24 jam
        // saat menghitung durasi agar hasilnya benar (bukan 18j 30m).
        if ($out->lt($in)) {
            $out->addDay();
        }

        $diff = $in->diff($out);

        return $diff->h.'j '.$diff->i.'m';
    }
}
