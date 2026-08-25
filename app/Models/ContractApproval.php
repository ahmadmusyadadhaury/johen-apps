<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractApproval extends Model
{
    public const DECISION_SETUJU = 'disetujui';

    public const DECISION_TIDAK = 'tidak_disetujui';

    protected $fillable = [
        'contract_id',
        'approver_id',
        'decision',
        'catatan',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, 'contract_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
