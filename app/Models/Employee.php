<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'nik',
        'nama',
        'email',
        'no_hp',
        'alamat',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'division_id',
        'position',
        'atasan',
        'atasan2',
        'jenis_karyawan',
        'lokasi_kerja',
        'jenis_kerja',
        'jam_kerja',
        'jobdesk',
        'no_kontak_darurat1',
        'hubungan_darurat1',
        'no_kontak_darurat2',
        'hubungan_darurat2',
        'no_bpjs',
        'status',
        'tanggal_masuk',
        'tanggal_resign',
        'foto',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'tanggal_resign' => 'date',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'employee_id');
    }

    public function getUserAttribute()
    {
        return $this->users->first();
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) return null;

        if (str_starts_with($this->foto, 'base64:')) {
            return route('hris.employees.photo', $this) . '?' . $this->updated_at->timestamp;
        }

        return asset('storage/employees/' . $this->foto);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function positionHistories(): HasMany
    {
        return $this->hasMany(PositionHistory::class);
    }

    public function payrollDetails(): HasMany
    {
        return $this->hasMany(PayrollDetail::class, 'employee_id');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function meetingRequests(): HasMany
    {
        return $this->hasMany(MeetingRequest::class);
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(Position::class, 'employee_position')
            ->withPivot('is_main')
            ->withTimestamps();
    }

    public function mainPosition(): ?Position
    {
        return $this->positions()->wherePivot('is_main', true)->first();
    }

    public function hasMultiplePositions(): bool
    {
        return $this->positions()->count() > 1;
    }

    public function positionNames(): string
    {
        return $this->positions->pluck('nama')->implode(' & ');
    }
}
