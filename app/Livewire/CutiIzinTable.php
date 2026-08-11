<?php

namespace App\Livewire;

use App\Models\Freelance;
use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\Position;
use Livewire\Component;
use Livewire\WithPagination;

class CutiIzinTable extends Component
{
    use WithPagination;

    public string $tab = 'saya';
    public string $search = '';
    public string $filterJenis = '';
    public string $filterStatus = '';

    public bool $showPengajuanModal = false;
    public ?int $selectedPositionId = null;
    public string $pengajuanJenis = 'cuti_tahunan';
    public string $pengajuanTanggalMulai = '';
    public string $pengajuanTanggalSelesai = '';
    public string $pengajuanKeterangan = '';

    public bool $showPinModal = false;
    public bool $showNoPinModal = false;
    public bool $showAtasan2ErrorModal = false;
    public string $atasan2ErrorMessage = '';
    public bool $showDeleteConfirmModal = false;
    public ?int $deleteId = null;

    public bool $showRekomendasiFreelanceModal = false;
    public string $rekomendasiPositionName = '';
    public $rekomendasiFreelancers = [];
    public string $pin = '';
    public string $catatan = '';
    public ?int $pendingId = null;
    public string $pendingLevel = '';
    public string $pendingAction = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openPengajuanModal(): void
    {
        $this->showPengajuanModal = true;
        $this->pengajuanJenis = 'cuti_tahunan';
        $this->pengajuanTanggalMulai = '';
        $this->pengajuanTanggalSelesai = '';
        $this->pengajuanKeterangan = '';
        $mainPos = auth()->user()->employee?->mainPosition();
        $this->selectedPositionId = $mainPos?->id;
        $this->resetErrorBag();
    }

    public function closePengajuanModal(): void
    {
        $this->showPengajuanModal = false;
        $this->resetErrorBag();
    }

    public function submitPengajuan(): void
    {
        $rules = [
            'pengajuanJenis' => ['required', 'in:cuti_tahunan,izin'],
            'pengajuanTanggalMulai' => ['required', 'date'],
            'pengajuanTanggalSelesai' => ['required', 'date', 'after_or_equal:pengajuanTanggalMulai'],
            'pengajuanKeterangan' => ['required', 'string', 'max:1000'],
        ];

        $employee = auth()->user()->employee;

        if ($employee && $employee->hasMultiplePositions()) {
            $rules['selectedPositionId'] = ['required', 'exists:positions,id'];
        }

        $this->validate($rules);

        $user = auth()->user();

        if (!$employee) {
            $this->dispatch('notify', type: 'error', message: 'Akun Anda tidak terhubung ke data karyawan.');
            return;
        }

        $mulai = \Carbon\Carbon::parse($this->pengajuanTanggalMulai);
        $selesai = \Carbon\Carbon::parse($this->pengajuanTanggalSelesai);
        $durasi = $mulai->diffInDays($selesai) + 1;

        $selectedPosition = $this->selectedPositionId
            ? Position::find($this->selectedPositionId)
            : $employee->mainPosition();

        $atasan = $this->getAtasan($employee, $selectedPosition);
        $atasan2 = $this->getAtasan2($employee, $atasan, $selectedPosition);

        $hasAtasan2 = $atasan2 !== null;

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'selected_position_id' => $selectedPosition?->id,
            'atasan_id' => $atasan?->id,
            'atasan2_id' => $atasan2?->id,
            'jenis' => $this->pengajuanJenis,
            'tanggal_mulai' => $this->pengajuanTanggalMulai,
            'tanggal_selesai' => $this->pengajuanTanggalSelesai,
            'durasi' => $durasi . ' hari',
            'keterangan' => $this->pengajuanKeterangan,
            'persetujuan_koor' => $atasan ? 'menunggu' : 'disetujui',
            'persetujuan_atasan2' => $hasAtasan2 ? 'menunggu' : 'disetujui',
            'persetujuan_hr' => 'menunggu',
        ]);

        $this->closePengajuanModal();
        $this->dispatch('notify', type: 'success', message: 'Pengajuan cuti/izin berhasil dikirim.');
    }

    public function setujui(int $id, string $level): void
    {
        $lr = LeaveRequest::with('employee')->findOrFail($id);
        $this->authorizeApproval($lr, $level);

        if ($level === 'persetujuan_atasan2' && $lr->persetujuan_koor !== 'disetujui') {
            $this->atasan2ErrorMessage = 'Atasan 1 belum menyetujui pengajuan ini. Atasan 2 hanya dapat menyetujui setelah Atasan 1 menyetujui.';
            $this->showAtasan2ErrorModal = true;
            return;
        }

        if ($level === 'persetujuan_hr') {
            if ($lr->persetujuan_atasan2 !== 'disetujui') {
                $this->dispatch('notify', type: 'error', message: 'Persetujuan HR hanya dapat diberikan setelah Atasan 2 menyetujui.');
                return;
            }
            if (!$lr->tanggal_selesai->isPast() && !auth()->user()->isSuperAdmin()) {
                $this->dispatch('notify', type: 'error', message: 'Persetujuan HR hanya dapat diberikan setelah masa cuti/izin selesai.');
                return;
            }
        }

        $user = auth()->user();
        if ($user->requiresPinApproval()) {
            if (!$user->hasPin()) {
                $this->showNoPinModal = true;
                return;
            }
            $this->pendingId = $id;
            $this->pendingLevel = $level;
            $this->pendingAction = 'setujui';
            $this->pin = '';
            $this->catatan = '';
            $this->showPinModal = true;
            return;
        }

        $lr->update([$level => 'disetujui']);
        $this->showRekomendasiIfHost($lr, $level);
        $this->dispatch('leave-request-updated');
        $this->dispatch('notify', type: 'success', message: 'Pengajuan disetujui.');
    }

    public function tolak(int $id, string $level): void
    {
        $lr = LeaveRequest::with('employee')->findOrFail($id);
        $this->authorizeApproval($lr, $level);

        $user = auth()->user();
        if ($user->requiresPinApproval()) {
            if (!$user->hasPin()) {
                $this->showNoPinModal = true;
                return;
            }
            $this->pendingId = $id;
            $this->pendingLevel = $level;
            $this->pendingAction = 'tolak';
            $this->pin = '';
            $this->catatan = '';
            $this->showPinModal = true;
            return;
        }

        $lr->update([$level => 'ditolak']);
        $this->dispatch('leave-request-updated');
        $this->dispatch('notify', type: 'success', message: 'Pengajuan ditolak.');
    }

    public function cancelPinModal(): void
    {
        $this->showPinModal = false;
        $this->reset(['pin', 'catatan', 'pendingId', 'pendingLevel', 'pendingAction']);
    }

    public function submitPinApproval(): void
    {
        $this->validate([
            'pin' => ['required', 'string', 'size:6'],
        ]);

        $user = auth()->user();
        if (!$user->verifyPin($this->pin)) {
            $this->addError('pin', 'PIN Persetujuan yang Anda masukkan tidak sesuai.');
            return;
        }

        $lr = LeaveRequest::findOrFail($this->pendingId);
        $this->authorizeApproval($lr, $this->pendingLevel);

        if ($this->pendingLevel === 'persetujuan_atasan2' && $this->pendingAction === 'setujui' && $lr->persetujuan_koor !== 'disetujui') {
            $this->showPinModal = false;
            $this->reset(['pin', 'catatan', 'pendingId', 'pendingLevel', 'pendingAction']);
            $this->atasan2ErrorMessage = 'Atasan 1 belum menyetujui pengajuan ini. Atasan 2 hanya dapat menyetujui setelah Atasan 1 menyetujui.';
            $this->showAtasan2ErrorModal = true;
            return;
        }

        if ($this->pendingLevel === 'persetujuan_hr' && $this->pendingAction === 'setujui') {
            if ($lr->persetujuan_atasan2 !== 'disetujui') {
                $this->showPinModal = false;
                $this->reset(['pin', 'catatan', 'pendingId', 'pendingLevel', 'pendingAction']);
                $this->dispatch('notify', type: 'error', message: 'Persetujuan HR hanya dapat diberikan setelah Atasan 2 menyetujui.');
                return;
            }
            if (!$lr->tanggal_selesai->isPast() && !auth()->user()->isSuperAdmin()) {
                $this->showPinModal = false;
                $this->reset(['pin', 'catatan', 'pendingId', 'pendingLevel', 'pendingAction']);
                $this->dispatch('notify', type: 'error', message: 'Persetujuan HR hanya dapat diberikan setelah masa cuti/izin selesai.');
                return;
            }
        }

        $status = $this->pendingAction === 'setujui' ? 'disetujui' : 'ditolak';
        $updateData = [$this->pendingLevel => $status];

        if ($this->catatan) {
            $updateData['catatan_persetujuan'] = $this->catatan;
        }

        $lr->update($updateData);

        if ($this->pendingAction === 'setujui') {
            $this->showRekomendasiIfHost($lr, $this->pendingLevel);
        }

        $this->dispatch('leave-request-updated');

        $message = 'Pengajuan ' . ($this->pendingAction === 'setujui' ? 'disetujui' : 'ditolak') . '.';

        $this->showPinModal = false;
        $this->reset(['pin', 'catatan', 'pendingId', 'pendingLevel', 'pendingAction']);

        $this->dispatch('notify', type: 'success', message: $message);
    }

    private function authorizeApproval(LeaveRequest $lr, string $level): void
    {
        $user = auth()->user();

        if ($level === 'persetujuan_koor') {
            $userEmployee = $user->employee;
            if (!$userEmployee || $userEmployee->id !== $lr->atasan_id) {
                abort(403, 'Hanya atasan 1 yang dapat menyetujui pengajuan ini.');
            }
        } elseif ($level === 'persetujuan_atasan2') {
            $userEmployee = $user->employee;
            if (!$userEmployee || $userEmployee->id !== $lr->atasan2_id) {
                abort(403, 'Hanya atasan 2 yang dapat menyetujui pengajuan ini.');
            }
        } elseif ($level === 'persetujuan_hr') {
            if ($user->isKoordinatorIt() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg() || (!$user->isSuperAdmin() && !$user->isGmCeo() && $user->id !== 4 && !$this->isHr($user))) {
                abort(403, 'Hanya HR yang dapat menyetujui persetujuan HR.');
            }
        } else {
            abort(403);
        }
    }

    public function confirmDelete(int $id): void
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $this->deleteId = $id;
        $this->showDeleteConfirmModal = true;
    }

    public function executeDelete(): void
    {
        if (!$this->deleteId) return;

        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $lr = LeaveRequest::findOrFail($this->deleteId);
        $lr->delete();

        $this->showDeleteConfirmModal = false;
        $this->dispatch('leave-request-updated');
        $this->dispatch('notify', type: 'success', message: 'Pengajuan berhasil dihapus.');
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirmModal = false;
        $this->deleteId = null;
    }

    public function closeRekomendasiFreelanceModal(): void
    {
        $this->showRekomendasiFreelanceModal = false;
        $this->rekomendasiPositionName = '';
        $this->rekomendasiFreelancers = [];
    }

    private function showRekomendasiIfHost(LeaveRequest $lr, string $level): void
    {
        if ($level === 'persetujuan_hr') return;

        $position = $lr->selectedPosition;
        if (!$position || !str_starts_with($position->nama, 'Host')) return;

        $gameName = preg_replace('/^Host\s+/', '', $position->nama);
        $gameName = preg_replace('/\s*\((Subuh|Pagi|Siang|Malam)\)$/i', '', $gameName);

        $freelancers = Freelance::where('host_position', 'like', '%' . $gameName . '%')->get();
        if ($freelancers->isEmpty()) return;

        $this->rekomendasiPositionName = $position->nama;
        $this->rekomendasiFreelancers = $freelancers;
        $this->showRekomendasiFreelanceModal = true;
    }

    public function getSelectedPositionAtasan(): ?string
    {
        if (!$this->selectedPositionId) return null;
        $position = Position::find($this->selectedPositionId);
        if (!$position) return null;
        $atasan = $this->getAtasan(auth()->user()->employee, $position);
        return $atasan?->nama;
    }

    public function getSelectedPositionAtasan2(): ?string
    {
        if (!$this->selectedPositionId) return null;
        $position = Position::find($this->selectedPositionId);
        if (!$position) return null;
        $atasan1 = $this->getAtasan(auth()->user()->employee, $position);
        $atasan2 = $this->getAtasan2(auth()->user()->employee, $atasan1, $position);
        return $atasan2?->nama;
    }

    public function getTimMenungguCountProperty(): int
    {
        $user = auth()->user();

        if ($user->isSuperAdmin() || $user->isGmCeo()) {
            return LeaveRequest::where('persetujuan_hr', 'menunggu')->count();
        }

        $userEmployee = $user->employee;
        if (!$userEmployee) return 0;

        $query = LeaveRequest::query();

        if ($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg() || $user->isKoordinatorFcMobile()) {
            $query->where('atasan_id', $userEmployee->id);

            $positionName = $this->getRolePositionName();
            if ($positionName) {
                $position = Position::where('nama', $positionName)->first();
                if ($position) {
                    $descendantIds = $this->getAllDescendantIds($position);
                    if (!empty($descendantIds)) {
                        $query->whereIn('selected_position_id', $descendantIds);
                    }
                }
            }

            $query->where('persetujuan_koor', 'menunggu');
        } elseif ($user->isHeadOfStore()) {
            $query->where(function ($q) use ($userEmployee) {
                $q->where('atasan_id', $userEmployee->id)
                  ->orWhere('atasan2_id', $userEmployee->id);
            });

            if ($user->isAnyKoordinator()) {
                $query->where('persetujuan_koor', 'menunggu');
            } elseif ($user->isManager()) {
                $query->where('persetujuan_atasan2', 'menunggu');
            } elseif ($user->isSuperAdmin() || $user->isGmCeo()) {
                $query->where('persetujuan_hr', 'menunggu');
            } else {
                $query->where(function ($q) {
                    $q->where('persetujuan_koor', 'menunggu')
                      ->orWhere('persetujuan_atasan2', 'menunggu');
                });
            }
        } else {
            return 0;
        }

        return $query->count();
    }

    public function render()
    {
        $user = auth()->user();
        $userEmployee = $user->employee;

        $isHr = $userEmployee && $userEmployee->positions()->whereIn('nama', [
            'Human Resource Generalist', 'Admin HR', 'Admin GA', 'Office Boy'
        ])->exists();
        $lihatSemua = $user->isSuperAdmin() || $user->isGmCeo() || $user->id === 4 || $isHr;

        $baseQuery = LeaveRequest::query();

        if ($user->isGmCeo()) {
            // GM/CEO sees all cuti/izin data from every employee
        } elseif ($user->isSuperAdmin()) {
            if ($this->tab === 'saya' && $userEmployee) {
                $baseQuery->where('employee_id', $userEmployee->id);
            }
        } elseif ($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg() || $user->isKoordinatorFcMobile()) {
            if ($userEmployee) {
                if ($this->tab === 'saya') {
                    $baseQuery->where('employee_id', $userEmployee->id);
                } else {
                    $baseQuery->where('atasan_id', $userEmployee->id);

                    $positionName = $this->getRolePositionName();
                    if ($positionName) {
                        $position = Position::where('nama', $positionName)->first();
                        if ($position) {
                            $descendantIds = $this->getAllDescendantIds($position);
                            if (!empty($descendantIds)) {
                                $baseQuery->whereIn('selected_position_id', $descendantIds);
                            }
                        }
                    }
                }
            } else {
                $baseQuery->whereRaw('1 = 0');
            }
        } elseif ($user->isHeadOfStore()) {
            if ($userEmployee) {
                if ($this->tab === 'saya') {
                    $baseQuery->where('employee_id', $userEmployee->id);
                } else {
                    $baseQuery->where(function ($q) use ($userEmployee) {
                        $q->where('atasan_id', $userEmployee->id)
                          ->orWhere('atasan2_id', $userEmployee->id);
                    });
                }
            } else {
                $baseQuery->whereRaw('1 = 0');
            }
        } elseif ($userEmployee && !$lihatSemua) {
            $baseQuery->where(function ($q) use ($userEmployee) {
                $q->where('atasan_id', $userEmployee->id)
                  ->orWhere('atasan2_id', $userEmployee->id)
                  ->orWhere('employee_id', $userEmployee->id);
            });
        } elseif (!$lihatSemua) {
            $baseQuery->whereRaw('1 = 0');
        }

        $totalPengajuan = (clone $baseQuery)->count();
        $totalCuti = (clone $baseQuery)->where('jenis', 'cuti_tahunan')->count();
        $totalIzin = (clone $baseQuery)->where('jenis', 'izin')->count();
        $menunggu = (clone $baseQuery)->where(function ($q) {
            $q->where('persetujuan_koor', 'menunggu')
              ->orWhere('persetujuan_atasan2', 'menunggu')
              ->orWhere('persetujuan_hr', 'menunggu');
        })->count();

        $leaveRequests = (clone $baseQuery)
            ->with('employee', 'atasan', 'atasan2', 'selectedPosition')
            ->when($this->search, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('nama', 'like', "%{$this->search}%")
                      ->orWhere('nik', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterJenis, function ($query) {
                $query->where('jenis', $this->filterJenis);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where(function ($q) {
                    $q->where('persetujuan_koor', $this->filterStatus)
                      ->orWhere('persetujuan_atasan2', $this->filterStatus)
                      ->orWhere('persetujuan_hr', $this->filterStatus);
                });
            })
            ->latest()
            ->paginate(10);

        $jatahCuti = 12;
        $usedCuti = 0;
        if ($userEmployee) {
            $usedCutiQuery = LeaveRequest::where('employee_id', $userEmployee->id)
                ->where('jenis', 'cuti_tahunan')
                ->whereYear('tanggal_mulai', now()->year)
                ->where('persetujuan_koor', 'disetujui');

            if (!$user->isAnyKoordinator()) {
                $usedCutiQuery->where('persetujuan_atasan2', 'disetujui');
            }

            if (!$user->isAnyKoordinator() && !$user->isStaffAdmin() && !$user->isStaffHostPubg() && !$user->isStaffHostFf() && !$user->isStaffIt() && !$user->isStaffHostMlbb() && !$user->isStaffHostEfootball() && !$user->isStaffHostValorant() && !$user->isStaffHostRoblox() && !$user->isStaffHostMonkeyPubg() && !$user->isStaffStock()) {
                $usedCutiQuery->where('persetujuan_hr', 'disetujui');
            }

            $usedCuti = $usedCutiQuery->get()
                ->sum(fn($lr) => (int) filter_var($lr->durasi, FILTER_SANITIZE_NUMBER_INT));
        }
        $sisaCuti = max(0, $jatahCuti - $usedCuti);

        $timMenungguCount = $this->getTimMenungguCountProperty();

        $userPositions = $userEmployee?->positions()->with('division')->get() ?? collect();

        $showPositionDropdown = $userPositions->count() > 1;

        if ($userEmployee && in_array($userEmployee->id, [9, 14, 22])) {
            $showPositionDropdown = $userPositions->pluck('division_id')->unique()->count() > 1;
        }

        $hideAksi = $user->isAnyKoordinator()
            || $user->isStaff()
            || $user->isStaffIt()
            || $user->isStaffCreative()
            || $user->isStaffAdmin()
            || $user->isStaffStock()
            || $user->isStaffHostPubg()
            || $user->isStaffHostFf()
            || $user->isStaffHostMlbb()
            || $user->isStaffHostEfootball()
            || $user->isStaffHostValorant()
            || $user->isStaffHostRoblox()
            || $user->isStaffHostMonkeyPubg();

        return view('livewire.cuti-izin-table', compact(
            'leaveRequests', 'totalPengajuan', 'totalCuti', 'totalIzin', 'menunggu', 'userEmployee', 'isHr', 'user', 'sisaCuti', 'jatahCuti', 'lihatSemua', 'timMenungguCount', 'userPositions', 'showPositionDropdown', 'hideAksi'
        ))->with('karyawanView', false);
    }

    private function getAtasan(Employee $employee, ?Position $position = null): ?Employee
    {
        $pos = $position ?? $employee->mainPosition();
        if (!$pos || !$pos->parent_id) return null;

        if (str_starts_with($pos->nama, 'Koordinator')) {
            return null;
        }

        $current = $pos->parent;
        while ($current) {
            $atasan = $current->employees()->first();
            if ($atasan) return $atasan;
            $current = $current->parent;
        }

        return null;
    }

    private function getAtasan2(Employee $employee, ?Employee $atasan1 = null, ?Position $position = null): ?Employee
    {
        $pos = $position ?? $employee->mainPosition();
        if (!$pos) return null;

        if (str_starts_with($pos->nama, 'Koordinator')) {
            $current = $pos->parent;
            while ($current) {
                $atasan = $current->employees()->first();
                if ($atasan) return $atasan;
                $current = $current->parent;
            }
            return null;
        }

        $atasan1 = $atasan1 ?? $this->getAtasan($employee, $position);
        if (!$atasan1) return null;

        $current = $pos->parent;
        while ($current) {
            $emp = $current->employees()->first();
            if ($emp && $emp->id === $atasan1->id) {
                $current = $current->parent;
                while ($current) {
                    $atasan2 = $current->employees()->first();
                    if ($atasan2) return $atasan2;
                    $current = $current->parent;
                }
                break;
            }
            $current = $current->parent;
        }

        $atasan2Field = trim($employee->atasan2 ?? '');
        if ($atasan2Field !== '' && $atasan2Field !== '-') {
            $atasan2 = Employee::where('nama', $atasan2Field)->first();
            if ($atasan2) return $atasan2;
        }

        return null;
    }

    private function getRolePositionName(): ?string
    {
        $user = auth()->user();
        return match (true) {
            $user->isKoordinatorPubg() => 'Koordinator Johen PUBG',
            $user->isKoordinatorFf() => 'Koordinator Free Fire',
            $user->isKoordinatorMlbb() => 'Koordinator MLBB',
            $user->isKoordinatorEfootball() => 'Koordinator E-football',
            $user->isKoordinatorValorant() => 'Koordinator Valorant',
            $user->isKoordinatorRoblox() => 'Koordinator Roblox',
            $user->isKoordinatorMonkeyPubg() => 'Koordinator Monkey PUBG',
            $user->isKoordinatorIt() => 'Koordinator IT',
            $user->isKoordinatorCreative() => 'Koordinator Creative',
            $user->isKoordinatorAdmin() => 'Koordinator Admin',
            $user->isKoordinatorStock() => 'Koordinator Stock',
            $user->isKoordinatorFcMobile() => 'Koordinator FC Mobile',
            default => null,
        };
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

    private function isHr($user): bool
    {
        $emp = $user->employee;
        if (!$emp) return false;
        return $emp->positions()->whereIn('nama', [
            'Human Resource Generalist', 'Admin HR', 'Admin GA', 'Office Boy'
        ])->exists();
    }
}
