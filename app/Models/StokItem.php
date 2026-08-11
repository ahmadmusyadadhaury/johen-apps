<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokItem extends Model
{
    protected $fillable = [
        'nama',
        'satuan',
        'division_id',
        'target_stok',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_stok' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function stokMasuk(): HasMany
    {
        return $this->hasMany(StokMasuk::class, 'item_id');
    }

    public function stokKeluar(): HasMany
    {
        return $this->hasMany(StokKeluar::class, 'item_id');
    }

    public function getTotalMasukAttribute(): int
    {
        return (int) $this->stokMasuk()->sum('jumlah');
    }

    public function getTotalKeluarAttribute(): int
    {
        return (int) $this->stokKeluar()->sum('jumlah');
    }

    public function getTersediaAttribute(): int
    {
        return $this->total_masuk - $this->total_keluar;
    }
}
