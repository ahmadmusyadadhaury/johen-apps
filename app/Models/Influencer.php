<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Influencer extends Model
{
    protected $fillable = [
        'no_kontrak',
        'nama',
        'divisi',
        'keterangan',
        'mulai_kontrak',
        'habis_kontrak',
        'link_sosmed',
        'biaya',
        'kontrak_file_path',
    ];

    protected function casts(): array
    {
        return [
            'mulai_kontrak' => 'date',
            'habis_kontrak' => 'date',
            'biaya' => 'decimal:2',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InfluencerPembayaran::class, 'influencer_id');
    }

    public function monitorings(): HasMany
    {
        return $this->hasMany(InfluencerMonitoring::class)->orderByDesc('period_month');
    }

    public function latestMonitoring(): HasOne
    {
        return $this->hasOne(InfluencerMonitoring::class)->latestOfMany('period_month');
    }
}
