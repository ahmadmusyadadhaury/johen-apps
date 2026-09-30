<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerPengajuan extends Model
{
    protected $fillable = [
        'no_kontrak',
        'nama',
        'divisi',
        'rekomendasi_lama_kontrak',
        'is_perpanjangan',
        'mulai_kontrak',
        'habis_kontrak',
        'link_sosmed',
        'biaya',
        'keterangan',
        'monitoring_month',
        'monitoring_followers',
        'monitoring_viewers_last_month',
        'monitoring_duration_hours',
        'monitoring_target_duration_hours',
        'monitoring_notes',
        'monitoring_benefits',
        'status',
        'pengaju_id',
        'influencer_id',
        'assigned_hos_position_id',
        'approved_hos1_by',
        'approved_hos1_at',
        'approved_coordinator_by',
        'approved_coordinator_at',
        'approved_gm_by',
        'approved_gm_at',
        'rejected_by',
        'rejected_at',
        'alasan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'mulai_kontrak' => 'date',
            'habis_kontrak' => 'date',
            'rekomendasi_lama_kontrak' => 'integer',
            'monitoring_month' => 'date',
            'monitoring_followers' => 'integer',
            'monitoring_viewers_last_month' => 'integer',
            'monitoring_duration_hours' => 'decimal:2',
            'monitoring_target_duration_hours' => 'decimal:2',
            'is_perpanjangan' => 'boolean',
            'approved_hos1_at' => 'datetime',
            'approved_coordinator_at' => 'datetime',
            'approved_gm_at' => 'datetime',
            'rejected_at' => 'datetime',
            'biaya' => 'decimal:2',
        ];
    }

    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengaju_id');
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }

    public function assignedHosPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'assigned_hos_position_id');
    }

    public function approverHos1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_hos1_by');
    }

    public function approverCoordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_coordinator_by');
    }

    public function approverGm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_gm_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
