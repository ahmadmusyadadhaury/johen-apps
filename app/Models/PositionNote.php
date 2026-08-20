<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PositionNote extends Model
{
    protected $fillable = [
        'from_position_id',
        'to_position_id',
        'situasi',
        'evaluasi',
        'komitmen',
        'rekomendasi_jenjang',
        'bulan',
        'tahun',
        'created_by',
        'seen_at',
    ];

    public static function unseenCountForPositions(array $positionIds, int $excludeUserId): int
    {
        if (empty($positionIds)) return 0;

        return static::whereIn('to_position_id', $positionIds)
            ->whereNull('seen_at')
            ->where('created_by', '!=', $excludeUserId)
            ->count();
    }

    public static function markSeenForPositions(array $positionIds, int $excludeUserId): int
    {
        if (empty($positionIds)) return 0;

        return static::whereIn('to_position_id', $positionIds)
            ->whereNull('seen_at')
            ->where('created_by', '!=', $excludeUserId)
            ->update(['seen_at' => now()]);
    }

    public function fromPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'from_position_id');
    }

    public function toPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'to_position_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PositionNoteComment::class);
    }

    protected function casts(): array
    {
        return [
            'seen_at' => 'datetime',
        ];
    }
}
