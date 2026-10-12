<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScriptVideoUpload extends Model
{
    public const DEFAULT_TARGET = 75;

    public const GAME_CATEGORIES = [
        'MLBB',
        'PUBG',
        'Free Fire',
        'Roblox',
        'E-Football',
        'Valorant',
        'FC Mobile',
    ];

    public const BULAN_LIST = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'minggu',
        'judul',
        'keterangan',
        'kategori_game',
        'konten_dibuat',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'minggu' => 'integer',
        'konten_dibuat' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $upload) {
            if ($upload->minggu === null) {
                $upload->minggu = self::mingguUntukTanggal($upload->created_at ?? now());
            }
        });
    }

    public static function mingguUntukTanggal($tanggal): int
    {
        $hari = (int) \Illuminate\Support\Carbon::parse($tanggal)->format('j');

        return min((int) ceil($hari / 7), 4);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForPeriod($query, string $bulan, int $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }

    public static function currentBulanName(): string
    {
        return self::BULAN_LIST[now()->month];
    }

    public static function currentTahun(): int
    {
        return (int) now()->year;
    }
}