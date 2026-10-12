<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScriptVideoTarget extends Model
{
    protected $fillable = [
        'bulan',
        'tahun',
        'target',
    ];

    public static function targetFor(string $bulan, int $tahun): int
    {
        $record = static::where('bulan', $bulan)->where('tahun', $tahun)->first();

        return $record?->target ?? ScriptVideoUpload::DEFAULT_TARGET;
    }

    public static function weeklyQuotaFor(int $target, int $terisiSebelum, int $minggu): int
    {
        $minggu = max(1, min(4, $minggu));
        $sisa = $target - max(0, $terisiSebelum);

        return max(0, (int) ceil($sisa / (5 - $minggu)));
    }

    public static function upsertTarget(string $bulan, int $tahun, int $target): self
    {
        $record = static::firstOrNew(['bulan' => $bulan, 'tahun' => $tahun]);
        $record->target = $target;
        $record->save();

        return $record;
    }
}