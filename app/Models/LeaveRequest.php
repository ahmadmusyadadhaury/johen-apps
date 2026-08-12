<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'selected_position_id',
        'atasan_id',
        'atasan2_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'keterangan',
        'catatan_persetujuan',
        'persetujuan_koor',
        'persetujuan_atasan2',
        'persetujuan_hr',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function atasan(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'atasan_id');
    }

    public function atasan2(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'atasan2_id');
    }

    public function selectedPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'selected_position_id');
    }

    public function syncAttendance(): void
    {
        if ($this->persetujuan_atasan2 !== 'disetujui') {
            return;
        }

        $status = $this->jenis === 'cuti_tahunan' ? 'cuti' : 'izin';

        $start = \Carbon\Carbon::parse($this->tanggal_mulai);
        $end = \Carbon\Carbon::parse($this->tanggal_selesai);

        $dates = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dates[] = $d->toDateString();
        }

        $existingDates = Attendance::where('employee_id', $this->employee_id)
            ->whereIn('date', $dates)
            ->pluck('date')
            ->map(fn($d) => $d instanceof \Carbon\Carbon ? $d->toDateString() : $d)
            ->all();

        foreach ($dates as $date) {
            if (in_array($date, $existingDates)) continue;

            Attendance::create([
                'employee_id' => $this->employee_id,
                'date' => $date,
                'status' => $status,
                'method' => 'manual',
            ]);
        }
    }
}
