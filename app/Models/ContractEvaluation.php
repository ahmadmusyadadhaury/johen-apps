<?php

namespace App\Models;

use App\Support\ContractEvaluationConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractEvaluation extends Model
{
    protected $fillable = [
        'contract_id',
        'evaluator_id',
        'kinerja',
        'disiplin',
        'kerjasama',
        'kepatuhan',
        'keterampilan',
        'catatan',
        'rekomendasi',
        'i_kehadiran',
        'i_ketepatan_waktu',
        'i_kepatuhan_peraturan',
        'i_tanggung_jawab',
        'i_kualitas_kerja',
        'i_produktivitas',
        'i_penyelesaian_tugas',
        'i_komunikasi',
        'i_kerja_sama_tim',
        'i_inisiatif',
        'i_pencapaian_target',
        'i_penghargaan_sanksi',
        'catatan_kelebihan',
        'catatan_kekurangan',
        'rekomendasi_pengembangan',
        'perpanjangan_bulan',
        'perpanjangan_mulai',
        'perpanjangan_berakhir',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'perpanjangan_mulai' => 'date',
        'perpanjangan_berakhir' => 'date',
        'rekomendasi_pengembangan' => 'array',
    ];

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function isNewFormat(): bool
    {
        return collect(array_keys(ContractEvaluationConfig::indicators()))
            ->contains(fn ($field) => $this->{$field} !== null);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, 'contract_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}