<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollImport extends Model
{
    protected $fillable = [
        'periode',
        'file_name',
        'total_employee',
        'total_payroll',
        'errors',
        'invalid_rows',
        'uploaded_by',
    ];

    protected $casts = [
        'errors' => 'array',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
