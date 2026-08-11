<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokKetersediaan extends Model
{
    protected $table = 'stok_ketersediaan';

    protected $fillable = [
        'division_id',
        'tanggal',
        'stok_hari_ini',
        'stok_sebelum',
        'stok_setelah',
        'jumlah_stok',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'stok_hari_ini' => 'integer',
            'stok_sebelum' => 'integer',
            'stok_setelah' => 'integer',
            'jumlah_stok' => 'integer',
        ];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
