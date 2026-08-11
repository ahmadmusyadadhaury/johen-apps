<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokTarget extends Model
{
    protected $table = 'stok_target';

    protected $fillable = [
        'division_id',
        'stok_harian',
        'stok_mingguan',
        'stok_bulanan',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'stok_harian' => 'integer',
            'stok_mingguan' => 'integer',
            'stok_bulanan' => 'integer',
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
