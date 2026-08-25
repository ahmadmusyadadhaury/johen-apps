<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeeContract extends Model
{
    protected $fillable = [
        'employee_id',
        'jenis_kontrak',
        'posisi',
        'atasan',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
        'keterangan',
        'is_addendum',
        'file',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_berakhir' => 'date',
            'is_addendum' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(ContractEvaluation::class, 'contract_id');
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(ContractEvaluation::class, 'contract_id')->latestOfMany();
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ContractApproval::class, 'contract_id');
    }
}
