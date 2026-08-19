<?php

namespace App\Models;

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
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, 'contract_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}