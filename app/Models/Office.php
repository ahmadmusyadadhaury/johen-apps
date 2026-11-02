<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    public const CODE_SUMMARECON_BANDUNG = 'SUM-BDG';

    public const CODE_BALEENDAH = 'BALEENDAH';

    public const CODE_JOHEN_STREAMER = 'JHN-STREAMER';

    public const ATTENDANCE_MACHINE = 'machine';

    public const ATTENDANCE_WEB_FACE = 'web_face';

    protected $fillable = [
        'code',
        'name',
        'slug',
        'address',
        'attendance_method',
        'attendance_latitude',
        'attendance_longitude',
        'attendance_radius_meters',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'attendance_latitude' => 'float',
            'attendance_longitude' => 'float',
            'attendance_radius_meters' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function usesAttendanceMachine(): bool
    {
        return $this->attendance_method === self::ATTENDANCE_MACHINE;
    }

    public function usesWebFaceAttendance(): bool
    {
        return $this->attendance_method === self::ATTENDANCE_WEB_FACE;
    }

    public function hasAttendanceLocation(): bool
    {
        return $this->attendance_latitude !== null && $this->attendance_longitude !== null;
    }
}
