<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollDetail extends Model
{
    protected $fillable = [
        'payroll_import_id',
        'employee_id',
        'nik',
        'nama',
        'email',
        'jabatan',
        'divisi',
        'gaji_pokok',
        'tambahan_upah',
        'bonus',
        'thr',
        'apresiasi',
        'tunjangan_jabatan',
        'premi_bpjs_kesehatan',
        'thr_dibayarkan',
        'potongan_pinjaman',
        'potongan_absensi',
        'potongan_bpjs_kesehatan_4',
        'potongan_bpjs_kesehatan_1',
        'take_home_pay',
        'pdf_password',
        'pdf_path',
        'status',
    ];

    public function payrollImport(): BelongsTo
    {
        return $this->belongsTo(PayrollImport::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function emailLog(): HasOne
    {
        return $this->hasOne(EmailLog::class);
    }

    public function getTotalPenghasilanBrutoAttribute(): float
    {
        return (float) ($this->gaji_pokok + $this->tambahan_upah + $this->bonus + $this->thr + $this->apresiasi + $this->tunjangan_jabatan + $this->premi_bpjs_kesehatan);
    }

    public function getTotalPengeluaranAttribute(): float
    {
        return (float) ($this->thr_dibayarkan + $this->potongan_pinjaman + $this->potongan_absensi + $this->potongan_bpjs_kesehatan_4 + $this->potongan_bpjs_kesehatan_1);
    }
}
