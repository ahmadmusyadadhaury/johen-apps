<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendancePunch extends Model
{
    protected $fillable = [
        'machine_user_id',
        'employee_id',
        'punch_at',
        'method',
        'machine_serial',
        'raw_data',
    ];

    protected function casts(): array
    {
        return [
            'punch_at' => 'datetime',
            'raw_data' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
