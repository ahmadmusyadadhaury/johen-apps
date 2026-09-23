<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyMeetingAttendance extends Model
{
    protected $fillable = [
        'weekly_meeting_id',
        'employee_id',
        'attended_at',
        'method',
        'device_location',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(WeeklyMeeting::class, 'weekly_meeting_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}