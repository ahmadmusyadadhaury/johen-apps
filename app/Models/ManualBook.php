<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualBook extends Model
{
    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'thumbnail',
        'file_pdf',
    ];

    public const KATEGORI_OPTIONS = ['Teknologi', 'Operasional', 'Public Speaking'];
}
