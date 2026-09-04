<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    public const TYPE_PROVINSI = 'provinsi';

    public const TYPE_KABUPATEN = 'kabupaten';

    public const TYPE_KECAMATAN = 'kecamatan';

    public const TYPE_KELURAHAN = 'kelurahan';

    public $timestamps = false;

    protected $table = 'indonesia_regions';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'parent_id',
        'type',
        'name',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->orderBy('name');
    }

    public function scopeProvinces($query)
    {
        return $query->where('type', self::TYPE_PROVINSI)->orderBy('name');
    }

    public function scopeWhereProvince($query, string $id)
    {
        return $query->where('type', self::TYPE_KABUPATEN)->where('parent_id', $id)->orderBy('name');
    }

    public function scopeWhereCity($query, string $id)
    {
        return $query->where('type', self::TYPE_KECAMATAN)->where('parent_id', $id)->orderBy('name');
    }

    public function scopeWhereDistrict($query, string $id)
    {
        return $query->where('type', self::TYPE_KELURAHAN)->where('parent_id', $id)->orderBy('name');
    }
}
