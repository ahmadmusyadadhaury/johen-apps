<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusPubg extends Model
{
    protected $fillable = [
        'employee_id',
        'tanggal',
        'nik',
        'nama',
        'sesi',
        'ach_sold',
        'ach_view',
        'peak_view',
        'durasi',
        'insentif',
        'catatan',
        'foto_bukti_stats',
        'foto_bukti_live',
        'feedback_atasan',
        'status',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'ach_sold' => 'decimal:2',
            'ach_view' => 'decimal:2',
            'peak_view' => 'decimal:2',
            'durasi' => 'decimal:2',
            'insentif' => 'decimal:2',
            'status' => 'string',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
