<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RunningRatePeriod extends Model
{
    protected $fillable = [
        'divisi',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function targets(): HasMany
    {
        return $this->hasMany(RunningRateHostTarget::class, 'period_id');
    }

    public function dailySolds(): HasMany
    {
        return $this->hasMany(RunningRateDailySold::class, 'period_id');
    }

    public function hosts(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'running_rate_host_targets', 'period_id', 'host_id')
            ->withPivot('target')
            ->withTimestamps();
    }
}
