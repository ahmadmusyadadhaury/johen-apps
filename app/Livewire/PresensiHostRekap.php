<?php

namespace App\Livewire;

use App\Models\AttendanceSession;
use App\Models\Employee;
use Livewire\Component;

class PresensiHostRekap extends Component
{
    public string $tanggal = '';

    public bool $showModal = false;

    public ?int $editEmployeeId = null;

    public int $editSesi = 1;

    public string $editClockIn = '';

    public string $editClockOut = '';

    public string $editStatus = 'izin';

    public string $editNote = '';

    public string $editNama = '';

    public string $editNamaSesi = '';

    public function mount(): void
    {
        $this->tanggal = now()->toDateString();
    }

    public function updatingTanggal(): void
    {
        $this->resetPage();
    }

    public function updatedTanggal(): void
    {
        if ($this->tanggal === '') {
            $this->tanggal = now()->toDateString();
        }
    }

    public function openModal(int $employeeId, int $sesi): void
    {
        $employee = Employee::findOrFail($employeeId);

        $row = AttendanceSession::where('employee_id', $employeeId)
            ->where('tanggal', $this->tanggal)
            ->where('sesi', $sesi)
            ->first();

        $this->editEmployeeId = $employee->id;
        $this->editSesi = $sesi;
        $this->editNama = $employee->nama;
        $this->editNamaSesi = AttendanceSession::sessionConfig($sesi)['label'] ?? 'Sesi '.$sesi;
        $this->editClockIn = $row?->clock_in ? substr($row->clock_in, 0, 5) : '';
        $this->editClockOut = $row?->clock_out ? substr($row->clock_out, 0, 5) : '';
        $this->editStatus = $row?->status ?? 'izin';
        $this->editNote = $row?->note ?? '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editEmployeeId = null;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'editEmployeeId' => 'required',
            'editSesi' => 'required|integer|between:1,4',
            'editClockIn' => 'nullable|date_format:H:i',
            'editClockOut' => 'nullable|date_format:H:i',
            'editStatus' => 'required|in:hadir,izin,sakit,cuti,alpha',
            'editNote' => 'nullable|string|max:255',
        ]);

        $data = [
            'employee_id' => $this->editEmployeeId,
            'tanggal' => $this->tanggal,
            'sesi' => $this->editSesi,
            'clock_in' => $this->editClockIn ? $this->editClockIn.':00' : null,
            'clock_out' => $this->editClockOut ? $this->editClockOut.':00' : null,
            'status' => $this->editStatus,
            'late_minutes' => ($this->editStatus === 'hadir' && $this->editClockIn)
                ? AttendanceSession::hitungTelat($this->editSesi, $this->editClockIn.':00')
                : 0,
            'note' => $this->editNote ?: null,
        ];

        AttendanceSession::updateOrCreate(
            [
                'employee_id' => $this->editEmployeeId,
                'tanggal' => $this->tanggal,
                'sesi' => $this->editSesi,
            ],
            $data
        );

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Presensi sesi berhasil diperbarui.');
    }

    public function render()
    {
        $user = auth()->user();

        if (! $user->isKoordinatorGame() && ! $user->isAnyKoordinator() && ! $user->isManager() && ! $user->isSuperAdminLike() && ! $user->isHeadOfStore()) {
            abort(403);
        }

        $employees = Employee::with('divisions')
            ->where('position', 'like', 'Host%')
            ->where('tipe', 'karyawan_aktif')
            ->orderBy('nama')
            ->get();

        $sessions = AttendanceSession::whereDate('tanggal', $this->tanggal)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy(fn ($s) => $s->employee_id.'-'.$s->sesi);

        $rows = $employees->map(function ($emp) use ($sessions) {
            $cells = [];
            foreach (array_keys(AttendanceSession::sessions()) as $sesi) {
                $cells[$sesi] = $sessions->get($emp->id.'-'.$sesi);
            }
            $row = new \stdClass;
            $row->employee = $emp;
            $row->cells = $cells;

            return $row;
        });

        $hadir = $sessions->where('status', 'hadir')->count();
        $terlambat = $sessions->filter(fn ($s) => $s->status === 'hadir' && $s->isTelat())->count();
        $izin = $sessions->whereIn('status', ['izin', 'sakit', 'cuti'])->count();
        $terisi = $sessions->count();
        $kosong = ($employees->count() * 4) - $terisi;

        $sessionsConfig = AttendanceSession::sessions();

        return view('livewire.presensi-host-rekap', compact('rows', 'sessionsConfig', 'hadir', 'terlambat', 'izin', 'kosong'));
    }
}
