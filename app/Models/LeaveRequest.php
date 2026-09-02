<?php

namespace App\Models;

use App\Services\AttendanceSyncService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'selected_position_id',
        'atasan_id',
        'atasan2_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'keterangan',
        'catatan_persetujuan',
        'persetujuan_koor',
        'persetujuan_atasan2',
        'persetujuan_hr',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function atasan(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'atasan_id');
    }

    public function atasan2(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'atasan2_id');
    }

    public function selectedPosition(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'selected_position_id');
    }

    public function syncAttendance(): void
    {
        if ($this->persetujuan_atasan2 !== 'disetujui') {
            return;
        }

        $status = $this->jenis === 'cuti_tahunan' ? 'cuti' : 'izin';

        $existingDates = Attendance::where('employee_id', $this->employee_id)
            ->whereIn('date', $this->dateRange())
            ->pluck('date')
            ->map(fn ($d) => $d instanceof Carbon ? $d->toDateString() : $d)
            ->all();

        foreach ($this->dateRange() as $date) {
            if (in_array($date, $existingDates)) {
                continue;
            }

            Attendance::create([
                'employee_id' => $this->employee_id,
                'date' => $date,
                'status' => $status,
                'method' => 'manual',
            ]);
        }
    }

    /**
     * Rentang tanggal cuti/izin yang dicakup pengajuan ini.
     */
    public function dateRange(): array
    {
        $start = Carbon::parse($this->tanggal_mulai);
        $end = Carbon::parse($this->tanggal_selesai);

        $dates = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $dates[] = $d->toDateString();
        }

        return $dates;
    }

    /**
     * Membatalkan catatan absen (cuti/izin) yang dibuat oleh syncAttendance
     * untuk pengajuan ini, lalu menerapkan ulang punch mesin pada tanggal yang
     * terdampak agar absen masuk karyawan yang sebenarnya tercatat.
     *
     * Tanggal yang masih tercakup pengajuan cuti/izin lain yang disetujui
     * dipertahankan (tidak dihapus).
     */
    public function unsyncAttendance(): void
    {
        if ($this->persetujuan_atasan2 !== 'disetujui') {
            return;
        }

        $status = $this->jenis === 'cuti_tahunan' ? 'cuti' : 'izin';

        $stillCovered = LeaveRequest::where('employee_id', $this->employee_id)
            ->where('id', '!=', $this->id)
            ->where('persetujuan_atasan2', 'disetujui')
            ->get()
            ->flatMap(fn ($lr) => $lr->dateRange())
            ->all();

        $deletedAny = false;
        foreach ($this->dateRange() as $date) {
            if (in_array($date, $stillCovered)) {
                continue;
            }

            $deleted = Attendance::where('employee_id', $this->employee_id)
                ->whereDate('date', $date)
                ->where('status', $status)
                ->where('method', 'manual')
                ->delete();

            if ($deleted) {
                $deletedAny = true;
            }
        }

        if ($deletedAny) {
            app(AttendanceSyncService::class)
                ->rebuildEmployeeAttendance($this->employee, preserveManual: true);
        }
    }
}
