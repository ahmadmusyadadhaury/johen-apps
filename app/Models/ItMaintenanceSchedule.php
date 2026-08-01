<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItMaintenanceSchedule extends Model
{
    protected $fillable = ['pc_id', 'jenis', 'jadwal', 'status', 'catatan', 'created_by'];
    protected $casts = ['jadwal' => 'date'];

    public function pc(): BelongsTo
    {
        return $this->belongsTo(ItMaintenancePc::class, 'pc_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
