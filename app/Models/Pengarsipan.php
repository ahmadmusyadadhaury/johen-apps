<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengarsipan extends Model
{
    protected $fillable = [
        'jenis',
        'nomor',
        'judul',
        'tanggal_surat',
        'file',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
        ];
    }

    public const JENIS_SURAT_EDARAN = 'surat_edaran';
    public const JENIS_SURAT_KEPUTUSAN = 'surat_keputusan';
    public const JENIS_PEMBERITAHUAN = 'pemberitahuan';

    public const JENIS_LABELS = [
        self::JENIS_SURAT_EDARAN => 'Surat Edaran',
        self::JENIS_SURAT_KEPUTUSAN => 'Surat Keputusan',
        self::JENIS_PEMBERITAHUAN => 'Pemberitahuan',
    ];
}