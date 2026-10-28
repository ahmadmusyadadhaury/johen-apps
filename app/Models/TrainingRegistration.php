<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingRegistration extends Model
{
    protected $fillable = [
        'training_session_id',
        'employee_id',
        'status',
        'terdaftar_pada',
        'dihadiri_pada',
        'mom',
        'mom_diisi_pada',
        'dicatat_oleh',
        'certificate_path',
        'certificate_name',
        'certificate_mime',
        'certificate_size',
        'certificate_issued_by',
        'certificate_issued_at',
    ];

    protected function casts(): array
    {
        return [
            'terdaftar_pada' => 'datetime',
            'dihadiri_pada' => 'datetime',
            'mom_diisi_pada' => 'datetime',
            'certificate_issued_at' => 'datetime',
            'certificate_size' => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function certificateIssuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'certificate_issued_by');
    }
}
