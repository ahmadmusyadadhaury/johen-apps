<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    protected $fillable = [
        'judul',
        'jenis',
        'deskripsi',
        'mulai_pada',
        'selesai_pada',
        'lokasi',
        'tautan',
        'thumbnail',
        'kuota',
        'dibuat_oleh',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'mulai_pada' => 'datetime',
            'selesai_pada' => 'datetime',
            'is_published' => 'boolean',
            'kuota' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class, 'training_session_id');
    }
}
