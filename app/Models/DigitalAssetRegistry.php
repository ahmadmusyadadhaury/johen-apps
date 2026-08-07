<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalAssetRegistry extends Model
{
    protected $fillable = [
        'source_id',
        'nama_aset',
        'email',
        'mulai',
        'berakhir',
        'biaya',
        'pic',
        'jabatan',
        'keperluan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'mulai' => 'date',
            'berakhir' => 'date',
            'biaya' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
