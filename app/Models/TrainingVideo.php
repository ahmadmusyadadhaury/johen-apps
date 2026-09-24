<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingVideo extends Model
{
    protected $fillable = [
        'nama',
        'kategori',
        'url',
    ];

    public const KATEGORI_OPTIONS = ['Teknologi', 'Operasional', 'Public Speaking'];

    public static function toEmbedUrl(string $url): string
    {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        return $url;
    }
}