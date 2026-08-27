<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItProject extends Model
{
    const STATUS_MENUNGGU = 'menunggu';
    const STATUS_PROSES = 'proses';
    const STATUS_SELESAI = 'selesai';

    const STATUSES = [
        self::STATUS_MENUNGGU => 'Menunggu',
        self::STATUS_PROSES => 'Proses',
        self::STATUS_SELESAI => 'Selesai',
    ];

    protected $fillable = ['nama', 'deadline', 'status', 'created_by', 'feedback_atasan'];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
