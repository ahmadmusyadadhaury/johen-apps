<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'method',
    ];

    protected $appends = ['duration', 'display_status'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function getDurationAttribute(): ?string
    {
        if (! $this->time_in || ! $this->time_out) {
            return null;
        }

        $in = Carbon::parse($this->time_in);
        $out = Carbon::parse($this->time_out);

        if ($out->lt($in)) {
            $out->addDay();
        }

        $diff = $in->diff($out);

        return $diff->h.'j '.$diff->i.'m';
    }

    public function getDisplayStatusAttribute(): string
    {
        $cutoff = $this->employee?->jamMasukCutoff($this->date?->toDateString()) ?? '09:00:00';

        return match ($this->status) {
            'hadir' => $this->time_in && $this->time_in > $cutoff ? 'terlambat' : 'tepat waktu',
            'izin' => 'izin',
            'sakit' => 'sakit',
            'alpha' => 'tidak hadir',
            'cuti' => 'cuti',
            default => $this->status,
        };
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
