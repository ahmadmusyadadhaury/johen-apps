<?php

namespace App\Http\Controllers;

use App\Models\BirthdayBannerPreference;
use App\Models\BirthdayWish;
use App\Models\Division;
use App\Models\Employee;
use App\Services\DashboardService;
use App\Support\DivisionMenu;
use Illuminate\Http\JsonResponse;
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

        $birthdayEmployees = Employee::whereNotNull('tanggal_lahir')
            ->where('tipe', 'karyawan_aktif')
            ->whereMonth('tanggal_lahir', now()->month)
            ->whereDay('tanggal_lahir', now()->day)
            ->orderBy('nama')
            ->get();

        $birthdayEmployee = null;
        $birthdayWishes = collect();
        if ($user->employee && $user->employee->tanggal_lahir && $user->employee->tanggal_lahir->isBirthday()) {
            $birthdayEmployee = $user->employee;
            $birthdayWishes = $birthdayEmployee->birthdayWishes()
                ->with('user.employee')
                ->latest()
                ->get();
        }

        $hideBirthdayBanner = BirthdayBannerPreference::where('user_id', $user->id)->value('hide_banner') ?? false;

        $alreadySentWish = false;
        if ($birthdayEmployees->isNotEmpty()) {
            $alreadySentWish = BirthdayWish::where('user_id', $user->id)
                ->whereIn('employee_id', $birthdayEmployees->pluck('id'))
                ->whereDate('created_at', now()->toDateString())
                ->exists();
        }

        $bannerData = compact('birthdayEmployee', 'birthdayEmployees', 'birthdayWishes', 'hideBirthdayBanner', 'alreadySentWish');

        // Popup Pengumuman memakai data pengumuman aktif/tayang dari menu
        // Pengumuman (is_published = true), berlaku untuk semua role yang
        // membuka dashboard. Dismiss per-id ditangani via sessionStorage.
        $announcements = $this->dashboardService->getAnnouncements($user->id);

        if ($user->isStaff() || $user->isStaffCreative() || $user->isKoordinatorIt() || $user->isKoordinatorAdmin() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg() || $user->isStaffIt() || $user->isKoordinatorCreative() || $user->isStaffHostPubg() || $user->isStaffHostFf() || $user->isStaffHostMlbb() || $user->isStaffHostEfootball() || $user->isStaffHostValorant() || $user->isStaffHostRoblox() || $user->isStaffHostMonkeyPubg() || $user->isStaffAdmin() || $user->isStaffStock() || $user->isKoordinatorStock()) {
            $employee = $user->employee;

            if (! $employee) {
                return view('dashboard.index', array_merge([
                    'karyawanView' => true,
                    'employee' => null,
                    'karyawanData' => null,
                    'announcements' => $announcements,
                ], $bannerData));
            }

            $karyawanData = $this->dashboardService->getKaryawanDashboard($employee->id);

            return view('dashboard.index', array_merge([
                'karyawanView' => true,
                'employee' => $employee,
                'karyawanData' => $karyawanData,
                'announcements' => $announcements,
            ], $bannerData));
        }

        $stats = $this->dashboardService->getStats();
        $availableYears = $this->dashboardService->getAvailableYears();
        $selectedYear = $request->integer('year', $availableYears[0] ?? now()->year);
        $payrolls = $this->dashboardService->getPayrollsByYear($selectedYear);
        $divisionStats = $this->dashboardService->getDivisionStats();
        $assetStats = $this->dashboardService->getAssetCategoryStats();
        $latestPayroll = $this->dashboardService->getLatestPayroll();
        $pendingLeaveRequests = $this->dashboardService->getPendingLeaveRequests(user: $user);
        $pendingLeaveCount = $this->dashboardService->getPendingLeaveCount(user: $user);
        $expiringContracts = $this->dashboardService->getExpiringContracts();
        $expiringContractCount = count($expiringContracts);
        $meetingStats = $this->dashboardService->getMeetingStats();

        $managerReviewStats = $user->isManager()
            ? $this->dashboardService->getManagerReviewStats($user)
            : null;

        $koordinatorStats = [];
        if ($user->isKoordinator()) {
            $employee = $user->employee;
            if ($employee) {
                $koordinatorStats = $this->dashboardService->getKaryawanDashboard($employee->id);
            }
        }

        $employee = $user->employee;

        return view('dashboard.index', array_merge(compact(
            'stats', 'availableYears', 'selectedYear', 'payrolls', 'divisionStats',
            'latestPayroll', 'pendingLeaveRequests', 'pendingLeaveCount',
            'expiringContracts', 'expiringContractCount', 'meetingStats',
            'assetStats', 'koordinatorStats', 'managerReviewStats', 'employee',
        ), ['announcements' => $announcements], $bannerData));
    }

    public function storeBirthdayWish(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'message' => ['required', 'string', 'max:300'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        if (! $employee->tanggal_lahir || ! $employee->tanggal_lahir->isBirthday()) {
            return back()->with('error', 'Karyawan ini tidak berulang tahun hari ini.');
        }

        BirthdayWish::create([
            'employee_id' => $validated['employee_id'],
            'user_id' => auth()->id(),
            'message' => trim($validated['message']),
        ]);

        return back()->with('success', 'Ucapan ulang tahun terkirim! 🎉');
    }

    public function hideBirthdayBanner(Request $request): JsonResponse
    {
        BirthdayBannerPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            ['hide_banner' => $request->boolean('hide', true)],
        );

        return response()->json(['ok' => true]);
    }

    public function division(Division $division)
    {
        $user = auth()->user();

        abort_unless($user->canViewAll() && ! $user->isKoordinator(), 403);

        session(['division_menu' => $division->id]);

        $division->loadCount('employees');
        $menu = DivisionMenu::for($division->nama);
        $employees = $division->employees()
            ->orderBy('employees.nama')
            ->get(['employees.id', 'employees.nama', 'employees.position', 'employees.foto', 'employees.updated_at']);

        return view('dashboard.division', compact('division', 'menu', 'employees'));
    }
}
