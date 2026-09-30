<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KalenderEvent extends Model
{
    protected $fillable = [
        'tanggal',
        'tanggal_selesai',
        'kegiatan',
        'waktu_mulai',
        'waktu_selesai',
        'keterangan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
