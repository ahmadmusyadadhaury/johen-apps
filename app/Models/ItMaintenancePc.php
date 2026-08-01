<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItMaintenancePc extends Model
{
    protected $table = 'it_maintenance_pcs';
    protected $fillable = ['nama', 'keterangan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];

    public function schedules(): HasMany
    {
        return $this->hasMany(ItMaintenanceSchedule::class, 'pc_id');
    }
}
