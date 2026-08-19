<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RunningRateHostTarget extends Model
{
    protected $fillable = [
        'period_id',
        'host_id',
        'target',
    ];

    protected function casts(): array
    {
        return [
            'target' => 'decimal:2',
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
}
