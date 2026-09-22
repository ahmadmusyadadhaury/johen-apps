<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = [
        'external_id',
        'title',
        'recurring_type',
        'recurring_day',
        'recurring_prev_day',
        'recurring_start_date',
        'date',
        'start_time',
        'end_time',
        'actual_end_time',
        'room',
        'team',
        'status',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'recurring_start_date' => 'date',
            'actual_end_time' => 'datetime',
        ];
    }

    /**
     * Hari efektif meeting berulang untuk tanggal tertentu.
     * Pembatasnya adalah minggu (Senin) yang memuat recurring_start_date:
     * mulai dari minggu tersebut meeting pindah ke recurring_day (hari baru);
     * minggu-minggu sebelumnya masih di recurring_prev_day (hari lama).
     */
    public function effectiveRecurringDay(?\Carbon\CarbonInterface $date = null): ?string
    {
        if (! $this->recurring_type || ! $this->recurring_day) {
            return null;
        }

        $start = $this->recurring_start_date;

        if (! $start) {
            return $this->recurring_day;
        }

        $weekStart = $start->copy()->startOfWeek(\Carbon\CarbonInterface::MONDAY);
        $date = $date ?: now();

        if ($date->gte($weekStart)) {
            return $this->recurring_day;
        }

        return $this->recurring_prev_day ?: $this->recurring_day;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
