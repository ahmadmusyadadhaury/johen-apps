<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoredFile extends Model
{
    protected $table = 'stored_files';

    protected $fillable = [
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'checksum',
        'content',
    ];

    protected $hidden = [
        'content',
    ];

    public function url(): ?string
    {
        if ($this->disk === 'web') {
            return asset(str_replace('\\', '/', $this->path));
        }

        try {
            return Storage::disk($this->disk)->url($this->path);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
