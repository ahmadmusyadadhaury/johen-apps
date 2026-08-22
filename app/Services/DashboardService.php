<?php

namespace App\Services;

use App\Models\ActivityCompetitor;
use App\Models\Announcement;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Attendance;
use App\Models\BonusPubg;
use App\Models\DigitalAssetRegistry;
use App\Models\Division;
use App\Models\EmailLog;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\LeaveRequest;
use App\Models\Meeting;
use App\Models\MeetingRequest;
use App\Models\PayrollDetail;
use App\Models\PayrollImport;
use App\Models\Position;
use App\Models\WeeklyPlanReport;
use Carbon\Carbon;
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
            'total_assets' => Asset::count() + DigitalAssetRegistry::count(),
            'email_sent' => EmailLog::where('status', 'sent')->count(),
            'email_failed' => EmailLog::where('status', 'failed')->count(),
        ];
    }

    public function getAssetCategoryStats(): array
    {
        return AssetCategory::withCount('assets')
            ->orderByDesc('assets_count')
            ->get()
            ->map(fn ($cat) => [
                'id' => $cat->id,
                'nama' => $cat->name,
                'total' => $cat->assets_count,
            ])
            ->toArray();
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
            ->map(fn ($d) => [
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
        return $this->applyPendingLeaveFilter(LeaveRequest::query(), $user)->count();
    }

    public function getPendingLeaveRequests(int $limit = 3, $user = null): array
    {
        return $this->applyPendingLeaveFilter(LeaveRequest::with('employee'), $user)
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn ($lr) => [
                'id' => $lr->id,
                'employee' => $lr->employee?->nama ?? '-',
                'jenis' => $lr->jenis === 'cuti_tahunan' ? 'Cuti' : 'Izin',
                'tanggal' => $lr->tanggal_mulai->isoFormat('D MMM').' - '.$lr->tanggal_selesai->isoFormat('D MMM YYYY'),
                'durasi' => $lr->durasi,
            ])
            ->toArray();
    }

    private function applyPendingLeaveFilter($query, $user)
    {
        if (! $user || ! $user->employee) {
            return $query->where(function ($q) {
                $q->where('persetujuan_koor', 'menunggu')
                    ->orWhere('persetujuan_atasan2', 'menunggu')
                    ->orWhere('persetujuan_hr', 'menunggu');
            });
        }

        $userEmployee = $user->employee;

        $isKoordinatorRole = $user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg();

        if ($user->isManager()) {
            return $query->where(function ($q) use ($userEmployee) {
                $q->where('atasan_id', $userEmployee->id)
                    ->where('persetujuan_koor', 'menunggu')
                    ->orWhere(function ($q2) use ($userEmployee) {
                        $q2->where('atasan2_id', $userEmployee->id)
                            ->where('persetujuan_atasan2', 'menunggu');
                    });
            });
        }

        $lihatSemua = $user->id === 4 || ($user->canViewAll() && ! $user->isKoordinator()) || in_array($userEmployee->position, [
            'Human Resource Generalist', 'Admin HR', 'Admin GA', 'OB',
        ]);

        if (! $isKoordinatorRole && $lihatSemua) {
            return $query->where(function ($q) {
                $q->where('persetujuan_koor', 'menunggu')
                    ->orWhere('persetujuan_atasan2', 'menunggu')
                    ->orWhere('persetujuan_hr', 'menunggu');
            });
        }

        return $query->where(function ($q) use ($userEmployee) {
            $q->where('atasan_id', $userEmployee->id)
                ->where('persetujuan_koor', 'menunggu')
                ->orWhere(function ($q2) use ($userEmployee) {
                    $q2->where('atasan2_id', $userEmployee->id)
                        ->where('persetujuan_atasan2', 'menunggu');
                });
        });
    }

    public function getEmployeeStatusBreakdown(): array
    {
        $active = Employee::where('status', 'aktif')->count();
        $nonActive = Employee::where('status', '!=', 'aktif')->count();
        $kontrak = EmployeeContract::whereBetween('tanggal_berakhir', [now(), now()->addDays(14)])
            ->where('status', 'berlaku')
            ->count();
        $expiringSoon = EmployeeContract::whereBetween('tanggal_berakhir', [now(), now()->addDays(14)])
            ->where('status', 'berlaku')
            ->count();

        return [
            'active' => $active,
            'non_active' => $nonActive,
            'kontrak' => $kontrak,
            'expiring_soon' => $expiringSoon,
        ];
    }

    public function getMeetingStats(): array
    {
        $divisionNames = Division::whereNotNull('nama')->get('nama')->pluck('nama');

        $perDivision = Meeting::query()
            ->get(['team'])
            ->reject(fn ($m) => in_array(trim((string) $m->team), ['', '-'], true))
            ->groupBy(function ($m) use ($divisionNames) {
                $team = trim((string) $m->team);
                $match = $divisionNames
                    ->filter(fn ($name) => $name !== '' && mb_stripos($team, $name) !== false)
                    ->sortByDesc(fn ($name) => mb_strlen($name))
                    ->first();

                return $match ?? $team;
            })
            ->map(fn ($group, $key) => ['nama' => $key, 'total' => $group->count()])
            ->sortByDesc('total')
            ->values()
            ->toArray();

        return [
            'total_meetings' => Meeting::query()->whereNotNull('team')->where('team', '<>', '')->where('team', '<>', '-')->count(),
            'per_division' => $perDivision,
        ];
    }

    public function getExpiringContracts(): array
    {
        return EmployeeContract::with('employee')
            ->whereBetween('tanggal_berakhir', [now(), now()->addDays(14)])
            ->where('status', 'berlaku')
            ->orderBy('tanggal_berakhir')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'employee' => $c->employee->nama,
                'posisi' => $c->posisi,
                'tanggal_berakhir' => $c->tanggal_berakhir->isoFormat('D MMM YYYY'),
                'days_remaining' => now()->diffInDays($c->tanggal_berakhir, false),
            ])
            ->toArray();
    }

    public function getManagerReviewStats($user): array
    {
        $empty = [
            'daily_tracking' => ['count' => 0, 'items' => []],
            'weekly_report' => ['count' => 0, 'items' => []],
            'activity_competitor' => ['count' => 0, 'items' => []],
        ];

        $employee = $user?->employee;
        if (! $employee || ! $user?->isManager()) {
            return $empty;
        }

        $position = $employee->mainPosition();
        if (! $position) {
            return $empty;
        }

        $subordinateIds = $this->getSubordinateIds($position);
        $subordinateIds = array_diff($subordinateIds, [$position->id]);

        if (empty($subordinateIds)) {
            return $empty;
        }

        $dailySubordinateIds = $subordinateIds;
        if (str_contains(strtolower($position->nama), 'head of store 2')) {
            $efootball = Position::where('nama', 'Koordinator E-football')->first();
            if ($efootball) {
                $efootballPositionIds = $this->getDescendantPositionIds($efootball->id);
                $efootballIds = Employee::whereHas('positions', fn ($q) => $q->whereIn('position_id', $efootballPositionIds))
                    ->pluck('id')->toArray();
                $dailySubordinateIds = array_diff($dailySubordinateIds, $efootballIds);
            }
        }

        $divisionNames = $this->getManagerDivisionNames($position);

        $dailyQuery = BonusPubg::whereIn('employee_id', $dailySubordinateIds)
            ->where('status', 'disetujui')
            ->whereNotNull('approved_by')
            ->whereIn('divisi', $divisionNames)
            ->where(fn ($q) => $q->whereNull('feedback_atasan')->orWhere('feedback_atasan', ''))
            ->with('employee');

        $dailyCount = (clone $dailyQuery)->count();
        $dailyItems = (clone $dailyQuery)->latest('tanggal')->take(3)->get()->map(fn ($b) => [
            'id' => $b->id,
            'employee' => $b->employee?->nama ?? $b->nama,
            'subtitle' => ($b->divisi ?: '-').' • '.$b->tanggal->isoFormat('D MMM'),
        ])->toArray();

        $weeklyQuery = WeeklyPlanReport::whereIn('employee_id', $subordinateIds)
            ->where(fn ($q) => $q->whereNull('feedback_atasan')->orWhere('feedback_atasan', ''))
            ->with('employee');

        $weeklyCount = (clone $weeklyQuery)->count();
        $weeklyItems = (clone $weeklyQuery)->latest('tanggal')->take(3)->get()->map(fn ($w) => [
            'id' => $w->id,
            'employee' => $w->employee?->nama ?? '-',
            'subtitle' => ($w->kategori ?: '-').' • '.$w->tanggal->isoFormat('D MMM'),
        ])->toArray();

        $activityQuery = ActivityCompetitor::whereIn('employee_id', $subordinateIds)
            ->where(fn ($q) => $q->whereNull('feedback_atasan')->orWhere('feedback_atasan', ''))
            ->with('employee');

        $activityCount = (clone $activityQuery)->count();
        $activityItems = (clone $activityQuery)->latest('tanggal_analysis')->take(3)->get()->map(fn ($a) => [
            'id' => $a->id,
            'employee' => $a->employee?->nama ?? '-',
            'subtitle' => ($a->jenis ?: '-').' • '.$a->tanggal_analysis->isoFormat('D MMM'),
        ])->toArray();

        return [
            'daily_tracking' => ['count' => $dailyCount, 'items' => $dailyItems],
            'weekly_report' => ['count' => $weeklyCount, 'items' => $weeklyItems],
            'activity_competitor' => ['count' => $activityCount, 'items' => $activityItems],
        ];
    }

    private function getSubordinateIds(Position $position): array
    {
        $descendantIds = $this->getDescendantPositionIds($position->id);
        $descendantIds = array_diff($descendantIds, [$position->id]);

        if (empty($descendantIds)) {
            return [];
        }

        return Employee::whereIn('id', function ($q) use ($descendantIds) {
            $q->select('employee_id')
                ->from('employee_position')
                ->whereIn('position_id', $descendantIds);
        })->pluck('id')->toArray();
    }

    private function getDescendantPositionIds(int $positionId): array
    {
        $ids = [$positionId];
        $children = Position::where('parent_id', $positionId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getDescendantPositionIds($childId));
        }

        return $ids;
    }

    private function getManagerDivisionNames(Position $position): array
    {
        $descendantIds = $this->getDescendantPositionIds($position->id);
        $names = Position::whereIn('id', $descendantIds)->pluck('nama')
            ->map(fn ($n) => strtolower($n))->toArray();

        $map = [
            'PUBG' => 'koordinator johen pubg',
            'Free Fire' => 'koordinator free fire',
            'MLBB' => 'koordinator mlbb',
            'E-football' => 'koordinator e-football',
            'Valorant' => 'koordinator valorant',
            'Roblox' => 'koordinator roblox',
            'Monkey PUBG' => 'koordinator monkey pubg',
            'FC Mobile' => 'koordinator fc mobile',
            'Admin' => 'koordinator admin',
        ];

        $divisions = [];
        foreach ($map as $divisi => $posName) {
            foreach ($names as $name) {
                if (str_contains($name, $posName)) {
                    $divisions[] = $divisi;
                    break;
                }
            }
        }

        return array_values(array_unique($divisions));
    }

    public function getKaryawanDashboard(int $employeeId): array
    {
        $now = now();
        $employee = Employee::with('divisions')->find($employeeId);

        if (! $employee) {
            return [];
        }

        $accrual = $employee->cutiAccrual($now);
        $cutiAktif = $accrual['eligible'];
        $cutiAktifDate = $employee->cutiEligibleDate();
        $terakumulasiCuti = $accrual['earned'];

        $usedCuti = 0;
        if ($cutiAktif && $accrual['cycle_start']) {
            $usedCutiQuery = LeaveRequest::where('employee_id', $employeeId)
                ->where('jenis', 'cuti_tahunan')
                ->where('tanggal_mulai', '>=', $accrual['cycle_start'])
                ->where('persetujuan_koor', 'disetujui');

            $isKoordinator = $employee->user && $employee->user->isAnyKoordinator();
            if (! $isKoordinator) {
                $usedCutiQuery->where('persetujuan_atasan2', 'disetujui');
            }

            $skipHrApproval = $employee->user && ($employee->user->isAnyKoordinator() || $employee->user->isStaffAdmin() || $employee->user->isStaffHostPubg() || $employee->user->isStaffHostFf() || $employee->user->isStaffIt() || $employee->user->isStaffHostMlbb() || $employee->user->isStaffHostEfootball() || $employee->user->isStaffHostValorant() || $employee->user->isStaffHostRoblox() || $employee->user->isStaffHostMonkeyPubg());
            if (! $skipHrApproval) {
                $usedCutiQuery->where('persetujuan_hr', 'disetujui');
            }

            $usedCuti = $usedCutiQuery->get()
                ->sum(fn ($lr) => (int) filter_var($lr->durasi, FILTER_SANITIZE_NUMBER_INT));
        }

        $jatahCuti = 12;
        $sisaCuti = max(0, $terakumulasiCuti - $usedCuti);

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
            ->map(fn ($lr) => [
                'id' => $lr->id,
                'jenis' => $lr->jenis === 'cuti_tahunan' ? 'Cuti Tahunan' : 'Izin',
                'tanggal' => $lr->tanggal_mulai->isoFormat('D MMM').' - '.$lr->tanggal_selesai->isoFormat('D MMM YYYY'),
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
            ->map(fn ($a) => [
                'date' => $a->date->isoFormat('D MMM YYYY'),
                'status' => $a->display_status,
                'time_in' => $a->time_in ? Carbon::parse($a->time_in)->format('H:i') : '-',
                'time_out' => $a->time_out ? Carbon::parse($a->time_out)->format('H:i') : '-',
                'work_duration' => $a->time_in && $a->time_out
                    ? (function () use ($a) {
                        $in = Carbon::parse($a->time_in);
                        $out = Carbon::parse($a->time_out);
                        if ($out->lt($in)) {
                            $out->addDay();
                        }

                        return $in->diff($out)->format('%h Jam %i Menit');
                    })()
                    : '-',
            ]);

        $totalHadir = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$now->startOfMonth()->format('Y-m-d'), $now->endOfMonth()->format('Y-m-d')])
            ->where(function ($q) {
                $q->where('status', 'hadir')->orWhere('status', 'present');
            })
            ->count();

        $totalTerlambat = Attendance::with('employee')
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$now->startOfMonth()->format('Y-m-d'), $now->endOfMonth()->format('Y-m-d')])
            ->where('status', 'hadir')
            ->whereNotNull('time_in')
            ->get()
            ->filter(fn ($a) => $a->time_in > ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00'))
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
            ->map(fn ($mr) => [
                'title' => $mr->title,
                'status' => $mr->status,
                'date' => $mr->date->isoFormat('D MMM YYYY'),
            ]);

        $userId = $employee->user?->id;

        try {
            $latestAnnouncement = Announcement::where('is_published', true)->latest()->first();
            $announcements = collect();
            if ($latestAnnouncement) {
                $isRead = $userId && $latestAnnouncement->readByUsers()->where('users.id', $userId)->exists();
                $announcements = collect([
                    [
                        'title' => $latestAnnouncement->title,
                        'date' => $latestAnnouncement->created_at->isoFormat('D MMM YYYY'),
                        'summary' => $latestAnnouncement->summary ?? $latestAnnouncement->content,
                        'content' => $latestAnnouncement->content,
                        'id' => $latestAnnouncement->id,
                        'is_read' => (bool) $isRead,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            $announcements = collect();
        }

        return [
            'employee' => [
                'nama' => $employee->nama,
                'nik' => $employee->nik,
                'position' => $employee->position ?? '-',
                'division' => $employee->divisionNames() ?: '-',
                'lokasi_kerja' => $employee->lokasi_kerja ?? '-',
                'foto' => $employee->foto,
                'status' => $employee->status,
            ],
            'sisa_cuti' => $sisaCuti,
            'jatah_cuti' => $jatahCuti,
            'used_cuti' => $usedCuti,
            'terakumulasi_cuti' => $terakumulasiCuti,
            'cuti_aktif' => $cutiAktif,
            'cuti_aktif_date' => $cutiAktifDate?->toDateString(),
            'pending_count' => $pendingCount,
            'pending_requests' => $pendingRequests,
            'recent_attendance' => $recentAttendance,
            'total_hadir_bulan_ini' => $totalHadir,
            'total_terlambat_bulan_ini' => $totalTerlambat,
            'attendance_today' => $attendanceToday ? [
                'time_in' => $attendanceToday->time_in ? Carbon::parse($attendanceToday->time_in)->format('H:i') : '-',
                'time_out' => $attendanceToday->time_out ? Carbon::parse($attendanceToday->time_out)->format('H:i') : '-',
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
                'title' => 'Data berhasil divalidasi',
                'description' => "{$import->total_employee} karyawan",
            ];

            if ($sentCount > 0) {
                $timeline[] = [
                    'time' => $import->created_at->addMinutes(2),
                    'icon' => 'check',
                    'title' => "{$sentCount} slip terkirim",
                    'description' => "Periode {$import->periode}",
                ];
            }

            if ($failedCount > 0) {
                $timeline[] = [
                    'time' => $import->created_at->addMinutes(3),
                    'icon' => 'alert',
                    'title' => "{$failedCount} slip gagal",
                    'description' => 'Perlu generate ulang',
                ];
            }
        }

        return $timeline;
    }
}
