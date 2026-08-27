<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItMaintenanceSchedule extends Model
{
    protected $fillable = ['pc_id', 'periode', 'tanggal_mulai', 'tanggal_selesai', 'status', 'catatan', 'foto_sebelum', 'foto_sesudah', 'created_by', 'feedback_atasan', 'feedback_koordinator'];
    protected $casts = ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date', 'periode' => 'integer'];

    public function pc(): BelongsTo
    {
        return $this->belongsTo(ItMaintenancePc::class, 'pc_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
