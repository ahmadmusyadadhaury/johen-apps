<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItTicket extends Model
{
    protected $fillable = [
        'kode', 'requester_id', 'assignee_id', 'judul', 'deskripsi', 'kategori',
        'prioritas', 'status', 'catatan_it', 'mulai_ditangani_at', 'selesai_at',
        'durasi_detik', 'proses_mulai_at',
    ];

    protected function casts(): array
    {
        return [
            'mulai_ditangani_at' => 'datetime',
            'selesai_at' => 'datetime',
            'proses_mulai_at' => 'datetime',
            'durasi_detik' => 'integer',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
