<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Position;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AbsensiTable extends Component
{
    use WithPagination;

    public string $date = '';
    public string $search = '';
    public string $tab = 'saya';

    public bool $showAbsenModal = false;
    public string $absenStatus = 'hadir';

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openAbsenModal(): void
    {
        $this->showAbsenModal = true;
        $this->absenStatus = 'hadir';
    }

    public function closeAbsenModal(): void
    {
        $this->showAbsenModal = false;
        $this->resetErrorBag();
    }

    public function submitAbsen(): void
    {
        $this->validate([
            'absenStatus' => ['required', 'in:hadir,izin,sakit'],
        ]);

        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            $this->dispatch('notify', type: 'error', message: 'Akun Anda tidak terhubung ke data karyawan.');
            return;
        }

        $cek = Attendance::where('employee_id', $employee->id)->where('date', today())->first();
        if ($cek) {
            $this->dispatch('notify', type: 'error', message: 'Anda sudah melakukan absensi hari ini.');
            $this->closeAbsenModal();
            return;
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => today(),
            'time_in' => now()->format('H:i:s'),
            'status' => $this->absenStatus === 'hadir' ? 'hadir' : $this->absenStatus,
        ]);

        $this->closeAbsenModal();
        $this->dispatch('notify', type: 'success', message: 'Absensi berhasil dicatat.');
    }

    public function render()
    {
        $user = auth()->user();
        $today = $this->date;

        $ownView = $user->isStaff()
            || $user->isStaffIt()
            || $user->isStaffCreative()
            || $user->isStaffHostPubg()
            || $user->isStaffHostFf()
            || $user->isStaffHostMlbb()
            || $user->isStaffHostEfootball()
            || $user->isStaffHostValorant()
            || $user->isStaffAdmin()
            || ($user->isKoordinator() && $this->tab === 'saya')
            || (($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg()) && $this->tab === 'saya')
            || ($user->isHeadOfStore() && $this->tab === 'saya');

        if ($ownView) {
            $employee = $user->employee;

            if (!$employee) {
                $riwayat = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

                return view('livewire.absensi-table', [
                    'karyawanView' => true,
                    'employee' => null,
                    'totalAbsensi' => 0,
                    'tepatWaktu' => 0,
                    'terlambat' => 0,
                    'totalHadir' => 0,
                    'attendances' => collect(),
                    'riwayat' => $riwayat,
                    'attendanceHariIni' => null,
                    'today' => $today,
                ]);
            }

            $riwayat = Attendance::where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->paginate(10);

            $semuaAbsensi = Attendance::with('employee')->where('employee_id', $employee->id)->get();
            $totalAbsensi = $semuaAbsensi->count();
            $cutoff = $employee->jamMasukCutoff();
            $tepatWaktu = $semuaAbsensi->filter(fn($a) =>
                $a->status === 'hadir' && (!$a->time_in || $a->time_in <= $cutoff)
            )->count();
            $terlambat = $semuaAbsensi->filter(fn($a) =>
                $a->status === 'hadir' && $a->time_in && $a->time_in > $cutoff
            )->count();
            $totalHadir = $tepatWaktu + $terlambat;
            $attendanceHariIni = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', today())->first();

            return view('livewire.absensi-table', compact(
                'employee', 'totalAbsensi', 'tepatWaktu', 'terlambat', 'totalHadir',
                'riwayat', 'attendanceHariIni', 'today'
            ))->with('karyawanView', true);
        }

        $attendances = Attendance::with('employee.divisions')
            ->whereDate('date', $today)
            ->get()
            ->keyBy('employee_id');

        $employeeQuery = Employee::with('divisions')->where('status', 'aktif');

        if ($user->isKoordinator() && $this->tab === 'tim') {
            $koordinatorEmployee = $user->employee;
            if ($koordinatorEmployee && $koordinatorEmployee->divisions->isNotEmpty()) {
                $divisionIds = $koordinatorEmployee->divisions->pluck('id')->toArray();
                $employeeQuery->whereHas('divisions', fn($q) => $q->whereIn('divisions.id', $divisionIds))
                    ->where('id', '!=', $koordinatorEmployee->id);
            }
        }

        if (($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg()) && $this->tab === 'tim') {
            $subordinateIds = $this->getSubordinateEmployeeIds();
            if (!empty($subordinateIds)) {
                $employeeQuery->whereIn('id', $subordinateIds);
            } else {
                $employeeQuery->whereRaw('1 = 0');
            }
        }

        if ($user->isHeadOfStore() && $this->tab === 'tim') {
            $subordinateIds = $this->getSubordinateEmployeeIds();
            if (!empty($subordinateIds)) {
                $employeeQuery->whereIn('id', $subordinateIds);
            } else {
                $employeeQuery->whereRaw('1 = 0');
            }
        }

        $totalKaryawan = (clone $employeeQuery)->count();

        $teamIds = (clone $employeeQuery)->pluck('id')->toArray();
        $teamAttendances = $attendances->filter(fn($a) => in_array($a->employee_id, $teamIds));

        $hadir = $teamAttendances->filter(fn($a) =>
            $a->status === 'hadir' && (!$a->time_in || $a->time_in <= ($a->employee?->jamMasukCutoff() ?? '09:00:00'))
        )->count();

        $terlambat = $teamAttendances->filter(fn($a) =>
            $a->status === 'hadir' && $a->time_in && $a->time_in > ($a->employee?->jamMasukCutoff() ?? '09:00:00')
        )->count();

        $totalHadir = $hadir + $terlambat;

        $employees = $employeeQuery
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nik', 'like', "%{$this->search}%")
                      ->orWhere('nama', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.absensi-table', compact(
            'attendances', 'totalKaryawan', 'hadir', 'terlambat', 'totalHadir', 'employees', 'today'
        ))->with('karyawanView', false);
    }

    private function getSubordinateEmployeeIds(): array
    {
        $employee = auth()->user()->employee;
        if (!$employee) return [];

        $mainPosition = $employee->mainPosition();
        if (!$mainPosition) return [];

        $descendantIds = $this->getAllDescendantIds($mainPosition);
        if (empty($descendantIds)) return [];

        return Employee::whereHas('positions', function ($q) use ($descendantIds) {
            $q->whereIn('position_id', $descendantIds)
              ->where('is_main', true);
        })->pluck('id')->toArray();
    }

    private function getAllDescendantIds(Position $position): array
    {
        $ids = [];
        $children = Position::where('parent_id', $position->id)->get();
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllDescendantIds($child));
        }
        return $ids;
    }
}
