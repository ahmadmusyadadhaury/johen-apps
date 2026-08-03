<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\LeaveRequest;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
    ) {}

    public function index(Request $request)
    {
        session()->forget('division_menu');

        $user = auth()->user();

        if ($user->isStaff() || $user->isStaffCreative() || $user->isKoordinatorIt() || $user->isKoordinatorAdmin() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg() || $user->isStaffIt() || $user->isKoordinatorCreative() || $user->isStaffHostPubg() || $user->isStaffHostFf() || $user->isStaffHostMlbb() || $user->isStaffHostEfootball() || $user->isStaffHostValorant() || $user->isStaffHostRoblox() || $user->isStaffHostMonkeyPubg() || $user->isStaffAdmin()) {
            $employee = $user->employee;

            if (!$employee) {
                return view('dashboard.index', [
                    'karyawanView' => true,
                    'employee' => null,
                    'karyawanData' => null,
                ]);
            }

            $karyawanData = $this->dashboardService->getKaryawanDashboard($employee->id);

            return view('dashboard.index', [
                'karyawanView' => true,
                'employee' => $employee,
                'karyawanData' => $karyawanData,
            ]);
        }

        $stats = $this->dashboardService->getStats();
        $availableYears = $this->dashboardService->getAvailableYears();
        $selectedYear = $request->integer('year', $availableYears[0] ?? now()->year);
        $payrolls = $this->dashboardService->getPayrollsByYear($selectedYear);
        $divisionStats = $this->dashboardService->getDivisionStats();
        $latestPayroll = $this->dashboardService->getLatestPayroll();
        $pendingLeaveRequests = $this->dashboardService->getPendingLeaveRequests(user: $user);
        $pendingLeaveCount = $this->dashboardService->getPendingLeaveCount(user: $user);
        $expiringContracts = $this->dashboardService->getExpiringContracts();
        $expiringContractCount = count($expiringContracts);
        $meetingStats = $this->dashboardService->getMonthlyMeetingStats();

        $koordinatorStats = [];
        if ($user->isKoordinator()) {
            $employee = $user->employee;
            if ($employee) {
                $koordinatorStats = $this->dashboardService->getKaryawanDashboard($employee->id);
            }
        }

        return view('dashboard.index', compact(
            'stats', 'availableYears', 'selectedYear', 'payrolls', 'divisionStats',
            'latestPayroll', 'pendingLeaveRequests', 'pendingLeaveCount',
            'expiringContracts', 'expiringContractCount', 'meetingStats',
            'koordinatorStats',
        ));
    }

    public function division(Division $division)
    {
        $user = auth()->user();

        abort_unless($user->canViewAll() && !$user->isKoordinator(), 403);

        session(['division_menu' => $division->id]);

        $division->loadCount('employees');
        $menu = \App\Support\DivisionMenu::for($division->nama);
        $employees = $division->employees()
            ->orderBy('employees.nama')
            ->get(['employees.id', 'employees.nama', 'employees.position', 'employees.foto', 'employees.updated_at']);

        return view('dashboard.division', compact('division', 'menu', 'employees'));
    }
}
