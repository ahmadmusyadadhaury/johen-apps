<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ItMaintenancePc extends Model
{
    protected $table = 'it_maintenance_pcs';
    protected $fillable = ['nama', 'asset_code', 'keterangan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function schedules(): HasMany
    {
        return $this->hasMany(ItMaintenanceSchedule::class, 'pc_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_code', 'code');
    }

    public function latestCompletedSchedule(): HasOne
    {
        return $this->hasOne(ItMaintenanceSchedule::class, 'pc_id')
            ->where('status', 'selesai')
            ->orderByRaw('COALESCE(tanggal_selesai, tanggal_mulai, updated_at) DESC')
            ->orderByDesc('id');
    }
}
