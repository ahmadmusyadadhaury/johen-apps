<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyMeeting extends Model
{
    public const DEFAULT_FACE_LATITUDE = -6.9596113;
    public const DEFAULT_FACE_LONGITUDE = 107.6990410;
    public const DEFAULT_FACE_RADIUS_METERS = 100;

    protected $fillable = [
        'title',
        'meeting_date',
        'start_time',
        'end_time',
        'location',
        'description',
        'qr_code',
        'is_active',
        'latitude',
        'longitude',
        'radius_meters',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
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

    public function faceAttendanceLatitude(): float
    {
        return (float) ($this->latitude ?? self::DEFAULT_FACE_LATITUDE);
    }

    public function faceAttendanceLongitude(): float
    {
        return (float) ($this->longitude ?? self::DEFAULT_FACE_LONGITUDE);
    }

    public function faceAttendanceRadiusMeters(): int
    {
        return (int) ($this->radius_meters ?: self::DEFAULT_FACE_RADIUS_METERS);
    }

    public function generateQrCode(): string
    {
        $this->qr_code = 'WM_' . $this->id . '_' . bin2hex(random_bytes(16));
        $this->save();
        return $this->qr_code;
    }
}
