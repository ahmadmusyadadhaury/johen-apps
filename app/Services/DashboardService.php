<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Announcement;
use App\Models\Division;
use App\Models\EmailLog;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\LeaveRequest;
use App\Models\Meeting;
use App\Models\MeetingRequest;
use App\Models\PayrollDetail;
use App\Models\PayrollImport;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'total_payroll' => PayrollImport::sum('total_payroll'),
            'total_employee' => PayrollImport::sum('total_employee'),
            'total_employees' => Employee::count(),
            'total_divisions' => Division::count(),
            'email_sent' => EmailLog::where('status', 'sent')->count(),
            'email_failed' => EmailLog::where('status', 'failed')->count(),
        ];
    }

    public function getAvailableYears(): array
    {
        return PayrollImport::select(DB::raw('CAST(SUBSTR(periode, -4) AS UNSIGNED) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    public function getPayrollsByYear(int $year): mixed
    {
        return PayrollImport::with('uploadedBy')
            ->where('periode', 'LIKE', "%{$year}")
            ->latest()
            ->get();
    }

    public function getDivisionStats(): array
    {
        return Division::withCount('employees')
            ->orderByDesc('employees_count')
            ->get()
            ->map(fn($d) => [
                'id' => $d->id,
                'nama' => $d->nama,
                'total' => $d->employees_count,
            ])
            ->toArray();
    }

    public function getLatestPayroll(): ?PayrollImport
    {
        return PayrollImport::latest()->first();
    }

    public function getPendingLeaveCount($user = null): int
    {
        return $this->applyAtasanFilter(LeaveRequest::query(), $user)
            ->where(function ($q) {
                $q->where('persetujuan_koor', 'menunggu')
                  ->orWhere('persetujuan_atasan2', 'menunggu')
                  ->orWhere('persetujuan_hr', 'menunggu');
            })->count();
    }

    public function getPendingLeaveRequests(int $limit = 3, $user = null): array
    {
        return $this->applyAtasanFilter(LeaveRequest::with('employee'), $user)
            ->where(function ($q) {
                $q->where('persetujuan_koor', 'menunggu')
                  ->orWhere('persetujuan_atasan2', 'menunggu')
                  ->orWhere('persetujuan_hr', 'menunggu');
            })
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn($lr) => [
                'id' => $lr->id,
                'employee' => $lr->employee?->nama ?? '-',
                'jenis' => $lr->jenis === 'cuti_tahunan' ? 'Cuti' : 'Izin',
                'tanggal' => $lr->tanggal_mulai->isoFormat('D MMM') . ' - ' . $lr->tanggal_selesai->isoFormat('D MMM YYYY'),
                'durasi' => $lr->durasi,
            ])
            ->toArray();
    }

    private function applyAtasanFilter($query, $user)
    {
        if (!$user) return $query;

        $userEmployee = $user->employee;
        if (!$userEmployee) return $query;

        if ($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg()) {
            $query->where(function ($q) use ($userEmployee) {
                $q->where('atasan_id', $userEmployee->id)
                  ->orWhere('atasan2_id', $userEmployee->id);
            });

            return $query;
        }

        $lihatSemua = $user->id === 4 || ($user->canViewAll() && !$user->isKoordinator()) || in_array($userEmployee->position, [
            'Human Resource Generalist', 'Admin HR', 'Admin GA', 'OB'
        ]);

        if (!$lihatSemua) {
            $query->where(function ($q) use ($userEmployee) {
                $q->where('atasan_id', $userEmployee->id)
                  ->orWhere('atasan2_id', $userEmployee->id);
            });
        }

        return $query;
    }

    public function getEmployeeStatusBreakdown(): array
    {
        $active = Employee::where('status', 'aktif')->count();
        $nonActive = Employee::where('status', '!=', 'aktif')->count();
        $kontrak = EmployeeContract::whereBetween('tanggal_berakhir', [now(), now()->addDays(10)])
            ->where('status', 'berlaku')
            ->count();
        $expiringSoon = EmployeeContract::whereBetween('tanggal_berakhir', [now(), now()->addDays(30)])
            ->where('status', 'berlaku')
            ->count();

        return [
            'active' => $active,
            'non_active' => $nonActive,
            'kontrak' => $kontrak,
            'expiring_soon' => $expiringSoon,
        ];
    }

    public function getMonthlyMeetingStats(): array
    {
        $divisions = Division::orderBy('nama')->get();
        $totalMeetings = Meeting::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total_meetings' => $totalMeetings,
            'per_division' => $divisions->map(fn($d) => [
                'id' => $d->id,
                'nama' => $d->nama,
                'total' => 0,
            ])->toArray(),
        ];
    }

    public function getExpiringContracts(): array
    {
        return EmployeeContract::with('employee')
            ->whereBetween('tanggal_berakhir', [now(), now()->addDays(3)])
            ->where('status', 'berlaku')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'employee' => $c->employee->nama,
                'posisi' => $c->posisi,
                'tanggal_berakhir' => $c->tanggal_berakhir->isoFormat('D MMM YYYY'),
                'days_remaining' => now()->diffInDays($c->tanggal_berakhir, false),
            ])
            ->toArray();
    }

    public function getKaryawanDashboard(int $employeeId): array
    {
        $now = now();
        $tahunIni = $now->year;
        $employee = Employee::with('division')->find($employeeId);

        if (!$employee) {
            return [];
        }

        $usedCutiQuery = LeaveRequest::where('employee_id', $employeeId)
            ->where('jenis', 'cuti_tahunan')
            ->whereYear('tanggal_mulai', $tahunIni)
            ->where('persetujuan_koor', 'disetujui');

        $isKoordinator = $employee->user && $employee->user->isAnyKoordinator();
        if (!$isKoordinator) {
            $usedCutiQuery->where('persetujuan_atasan2', 'disetujui');
        }

        $skipHrApproval = $employee->user && ($employee->user->isAnyKoordinator() || $employee->user->isStaffAdmin() || $employee->user->isStaffHostPubg() || $employee->user->isStaffHostFf() || $employee->user->isStaffIt() || $employee->user->isStaffHostMlbb() || $employee->user->isStaffHostEfootball() || $employee->user->isStaffHostValorant() || $employee->user->isStaffHostRoblox() || $employee->user->isStaffHostMonkeyPubg());
        if (!$skipHrApproval) {
            $usedCutiQuery->where('persetujuan_hr', 'disetujui');
        }

        $usedCuti = $usedCutiQuery->get()
            ->sum(fn($lr) => (int) filter_var($lr->durasi, FILTER_SANITIZE_NUMBER_INT));

        $jatahCuti = 12;
        $sisaCuti = max(0, $jatahCuti - $usedCuti);

        $pendingCount = LeaveRequest::where('employee_id', $employeeId)
            ->where(function ($q) {
                $q->where('persetujuan_koor', 'menunggu')
                  ->orWhere('persetujuan_atasan2', 'menunggu')
                  ->orWhere('persetujuan_hr', 'menunggu');
            })->count();

        $pendingRequests = LeaveRequest::where('employee_id', $employeeId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($lr) => [
                'id' => $lr->id,
                'jenis' => $lr->jenis === 'cuti_tahunan' ? 'Cuti Tahunan' : 'Izin',
                'tanggal' => $lr->tanggal_mulai->isoFormat('D MMM') . ' - ' . $lr->tanggal_selesai->isoFormat('D MMM YYYY'),
                'durasi' => $lr->durasi,
                'status_koor' => $lr->persetujuan_koor,
                'status_atasan2' => $lr->persetujuan_atasan2,
                'status_hr' => $lr->persetujuan_hr,
                'status_akhir' => $lr->persetujuan_koor === 'disetujui' && $lr->persetujuan_atasan2 === 'disetujui' && $lr->persetujuan_hr === 'disetujui' ? 'disetujui' : ($lr->persetujuan_koor === 'ditolak' || $lr->persetujuan_atasan2 === 'ditolak' || $lr->persetujuan_hr === 'ditolak' ? 'ditolak' : 'menunggu'),
            ]);

        $recentAttendance = Attendance::where('employee_id', $employeeId)
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'date' => $a->date->isoFormat('D MMM YYYY'),
                'status' => $a->display_status,
                'time_in' => $a->time_in ? \Carbon\Carbon::parse($a->time_in)->format('H:i') : '-',
                'time_out' => $a->time_out ? \Carbon\Carbon::parse($a->time_out)->format('H:i') : '-',
                'work_duration' => $a->time_in && $a->time_out
                    ? \Carbon\Carbon::parse($a->time_in)->diff(\Carbon\Carbon::parse($a->time_out))->format('%h Jam %i Menit')
                    : '-',
            ]);

        $totalHadir = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$now->startOfMonth()->format('Y-m-d'), $now->endOfMonth()->format('Y-m-d')])
            ->where(function ($q) {
                $q->where('status', 'hadir')->orWhere('status', 'present');
            })
            ->count();

        $totalTerlambat = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$now->startOfMonth()->format('Y-m-d'), $now->endOfMonth()->format('Y-m-d')])
            ->where('status', 'hadir')
            ->whereNotNull('time_in')
            ->where('time_in', '>', '09:00:00')
            ->count();

        $attendanceToday = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', today())
            ->first();

        $latestPayroll = PayrollDetail::where('employee_id', $employeeId)
            ->latest()
            ->first();

        $meetingRequests = MeetingRequest::where('employee_id', $employeeId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($mr) => [
                'title' => $mr->title,
                'status' => $mr->status,
                'date' => $mr->date->isoFormat('D MMM YYYY'),
            ]);

        try {
            $announcements = Announcement::where('is_published', true)->latest()->take(5)->get()->map(fn($a) => [
                'title' => $a->title,
                'date' => $a->created_at->isoFormat('D MMM YYYY'),
                'summary' => $a->summary ?? $a->content,
                'id' => $a->id,
            ]);

            $upcomingEvents = Announcement::whereNotNull('event_date')
                ->where('event_date', '>=', today())
                ->where('is_published', true)
                ->orderBy('event_date')
                ->take(5)
                ->get()
                ->map(fn($a) => [
                    'title' => $a->title,
                    'date' => $a->event_date->isoFormat('D MMM'),
                    'time' => $a->event_time ?? '',
                ]);
        } catch (\Exception $e) {
            $announcements = collect();
            $upcomingEvents = collect();
        }

        return [
            'employee' => [
                'nama' => $employee->nama,
                'nik' => $employee->nik,
                'position' => $employee->position ?? '-',
                'division' => $employee->division?->nama ?? '-',
                'lokasi_kerja' => $employee->lokasi_kerja ?? '-',
                'foto' => $employee->foto,
                'status' => $employee->status,
            ],
            'sisa_cuti' => $sisaCuti,
            'jatah_cuti' => $jatahCuti,
            'used_cuti' => $usedCuti,
            'pending_count' => $pendingCount,
            'pending_requests' => $pendingRequests,
            'recent_attendance' => $recentAttendance,
            'total_hadir_bulan_ini' => $totalHadir,
            'total_terlambat_bulan_ini' => $totalTerlambat,
            'attendance_today' => $attendanceToday ? [
                'time_in' => $attendanceToday->time_in ? \Carbon\Carbon::parse($attendanceToday->time_in)->format('H:i') : '-',
                'time_out' => $attendanceToday->time_out ? \Carbon\Carbon::parse($attendanceToday->time_out)->format('H:i') : '-',
                'status' => $attendanceToday->display_status,
                'location' => $attendanceToday->location ?? '-',
                'method' => $attendanceToday->method ?? 'GPS',
            ] : null,
            'latest_payroll' => $latestPayroll ? [
                'periode' => $latestPayroll->payrollImport?->periode ?? '-',
                'take_home_pay' => (int) $latestPayroll->take_home_pay,
                'gaji_pokok' => (int) $latestPayroll->gaji_pokok,
            ] : null,
            'meeting_requests' => $meetingRequests,
            'announcements' => $announcements,
            'upcoming_events' => $upcomingEvents,
        ];
    }

    public function getTimeline(int $limit = 10): array
    {
        $imports = PayrollImport::withCount('payrollDetails')
            ->latest()
            ->take($limit)
            ->get();

        $timeline = [];

        foreach ($imports as $import) {
            $sentCount = $import->payrollDetails()->where('status', 'sent')->count();
            $failedCount = $import->payrollDetails()->where('status', 'failed')->count();

            $timeline[] = [
                'time' => $import->created_at,
                'icon' => 'upload',
                'title' => "Payroll {$import->periode} diupload",
                'description' => "File: {$import->file_name}",
            ];

            $timeline[] = [
                'time' => $import->created_at->addMinutes(1),
                'icon' => 'check',
                'title' => "Data berhasil divalidasi",
                'description' => "{$import->total_employee} karyawan",
            ];

            if ($sentCount > 0) {
                $timeline[] = [
                    'time' => $import->created_at->addMinutes(2),
                    'icon' => 'mail',
                    'title' => "{$sentCount} email terkirim",
                    'description' => "Periode {$import->periode}",
                ];
            }

            if ($failedCount > 0) {
                $timeline[] = [
                    'time' => $import->created_at->addMinutes(3),
                    'icon' => 'alert',
                    'title' => "{$failedCount} email gagal",
                    'description' => "Perlu retry",
                ];
            }
        }

        return $timeline;
    }
}
