<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RunningRateDailySold extends Model
{
    protected $fillable = [
        'period_id',
        'host_id',
        'tanggal',
        'sold',
        'input_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'sold' => 'decimal:2',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(RunningRatePeriod::class, 'period_id');
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'host_id');
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
