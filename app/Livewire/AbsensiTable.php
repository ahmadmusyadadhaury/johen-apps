<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public bool $showJamKerjaModal = false;

    public ?int $jamKerjaEmployeeId = null;

    public string $jam_kerja = '';

    public string $jam_masuk = '';

    public string $effective_date = '';

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDate(): void
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

        if (! $employee) {
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

    public function openJamKerjaModal(int $employeeId): void
    {
        $user = auth()->user();

        if (! $user->isAnyKoordinator() || $this->tab !== 'tim') {
            abort(403);
        }

        $ids = $this->getSubordinateEmployeeIds();
        if (! in_array($employeeId, $ids)) {
            abort(403);
        }

        $emp = Employee::findOrFail($employeeId);
        $this->jamKerjaEmployeeId = $emp->id;
        $this->jam_kerja = $emp->jam_kerja ?? '';
        $this->jam_masuk = $emp->jam_masuk ? substr($emp->jam_masuk, 0, 5) : '';
        $this->effective_date = now()->toDateString();
        $this->showJamKerjaModal = true;
    }

    public function closeJamKerjaModal(): void
    {
        $this->showJamKerjaModal = false;
        $this->jamKerjaEmployeeId = null;
        $this->jam_kerja = '';
        $this->jam_masuk = '';
        $this->effective_date = '';
        $this->resetErrorBag();
    }

    public function saveJamKerja(): void
    {
        $user = auth()->user();

        if (! $user->isAnyKoordinator() || $this->tab !== 'tim') {
            abort(403);
        }

        $ids = $this->getSubordinateEmployeeIds();
        if (! $this->jamKerjaEmployeeId || ! in_array($this->jamKerjaEmployeeId, $ids)) {
            abort(403);
        }

        $this->validate([
            'jam_kerja' => 'nullable|string|max:255',
            'jam_masuk' => 'nullable|date_format:H:i',
            'effective_date' => 'required|date|before_or_equal:today|after_or_equal:2000-01-01',
        ]);

        $emp = Employee::findOrFail($this->jamKerjaEmployeeId);
        $emp->setJamKerja(
            $this->jam_kerja ?: null,
            $this->jam_masuk ? $this->jam_masuk.':00' : null,
            $this->effective_date
        );

        $this->closeJamKerjaModal();
        $this->dispatch('notify', type: 'success', message: 'Jam kerja '.$emp->nama.' berhasil diperbarui.');
    }

    public function render()
    {
        $user = auth()->user();
        $today = $this->date;

        if ($user->isSuperAdminLike() && $this->tab === 'sinkron') {
            return view('livewire.absensi-table', [
                'sinkronView' => true,
                'tab' => $this->tab,
                'today' => $today,
            ]);
        }

        $ownView = $user->isStaff()
            || $user->isStaffIt()
            || $user->isStaffCreative()
            || ($user->isStaffHostPubg() && ! $user->isAnyKoordinator())
            || ($user->isStaffHostFf() && ! $user->isAnyKoordinator())
            || $user->isStaffHostMlbb()
            || $user->isStaffHostEfootball()
            || $user->isStaffHostValorant()
            || $user->isStaffAdmin()
            || ($user->isSuperAdminLike() && $this->tab === 'saya')
            || ($user->isKoordinator() && $this->tab === 'saya')
            || (($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg()) && $this->tab === 'saya')
            || ($user->isHeadOfStore() && $this->tab === 'saya');

        if ($ownView) {
            $employee = $user->employee;

            if (! $employee) {
                $riwayat = new LengthAwarePaginator([], 0, 10);

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
            $tepatWaktu = $semuaAbsensi->filter(fn ($a) => $a->status === 'hadir' && (! $a->time_in || $a->time_in <= ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00'))
            )->count();
            $terlambat = $semuaAbsensi->filter(fn ($a) => $a->status === 'hadir' && $a->time_in && $a->time_in > ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00')
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
                $employeeQuery->whereHas('divisions', fn ($q) => $q->whereIn('divisions.id', $divisionIds))
                    ->where('id', '!=', $koordinatorEmployee->id);
            }
        }

        if (($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg()) && $this->tab === 'tim') {
            $subordinateIds = $this->getSubordinateEmployeeIds();
            if (! empty($subordinateIds)) {
                $employeeQuery->whereIn('id', $subordinateIds);
            } else {
                $employeeQuery->whereRaw('1 = 0');
            }
        }

        if ($user->isHeadOfStore() && $this->tab === 'tim') {
            $subordinateIds = $this->getSubordinateEmployeeIds();
            if (! empty($subordinateIds)) {
                $employeeQuery->whereIn('id', $subordinateIds);
            } else {
                $employeeQuery->whereRaw('1 = 0');
            }
        }

        $totalKaryawan = (clone $employeeQuery)->count();

        $teamIds = (clone $employeeQuery)->pluck('id')->toArray();
        $teamAttendances = $attendances->filter(fn ($a) => in_array($a->employee_id, $teamIds));

        $hadir = $teamAttendances->filter(fn ($a) => $a->status === 'hadir' && (! $a->time_in || $a->time_in <= ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00'))
        )->count();

        $terlambat = $teamAttendances->filter(fn ($a) => $a->status === 'hadir' && $a->time_in && $a->time_in > ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00')
        )->count();

        $totalHadir = $hadir + $terlambat;

        $statsMembers = $this->buildStatsMembers($teamAttendances);

        $employees = $employeeQuery
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nik', 'like', "%{$this->search}%")
                        ->orWhere('nama', 'like', "%{$this->search}%");
                });
            })
            ->orderByRaw('CAST(nik AS UNSIGNED) ASC')
            ->paginate(10);

        return view('livewire.absensi-table', compact(
            'attendances', 'totalKaryawan', 'hadir', 'terlambat', 'totalHadir', 'employees', 'today', 'statsMembers'
        ))->with('karyawanView', false);
    }

    private function buildStatsMembers($teamAttendances): array
    {
        $tepat = [];
        $terlambat = [];

        foreach ($teamAttendances as $a) {
            if ($a->status !== 'hadir') {
                continue;
            }

            $cutoff = $a->employee?->jamMasukCutoff($this->date) ?? '09:00:00';
            $row = [
                'nama' => $a->employee?->nama ?? 'Tidak dikenal',
                'jabatan' => $a->employee?->position ?? '-',
                'time_in' => $a->time_in ? substr($a->time_in, 0, 5) : '-',
                'cutoff' => substr($cutoff, 0, 5),
            ];

            if ($a->time_in && $a->time_in > $cutoff) {
                $terlambat[] = $row;
            } else {
                $tepat[] = $row;
            }
        }

        $sortByTime = fn ($a, $b) => ($a['time_in'] === '-' ? '' : $a['time_in']) <=> ($b['time_in'] === '-' ? '' : $b['time_in']);
        usort($tepat, $sortByTime);
        usort($terlambat, $sortByTime);

        return ['tepat' => $tepat, 'terlambat' => $terlambat];
    }

    private function getSubordinateEmployeeIds(): array
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            return [];
        }

        $mainPosition = $employee->mainPosition();
        if (! $mainPosition) {
            return [];
        }

        $descendantIds = $this->getAllDescendantIds($mainPosition);
        if (empty($descendantIds)) {
            return [];
        }

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
