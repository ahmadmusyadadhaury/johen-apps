<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyMeeting extends Model
{
    protected $fillable = [
        'title',
        'meeting_date',
        'start_time',
        'end_time',
        'location',
        'description',
        'qr_code',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(WeeklyMeetingAttendance::class);
    }

    public function attendancesCount(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WeeklyMeetingAttendance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function generateQrCode(): string
    {
        $this->qr_code = 'WM_' . $this->id . '_' . bin2hex(random_bytes(16));
        $this->save();
        return $this->qr_code;
    }
}