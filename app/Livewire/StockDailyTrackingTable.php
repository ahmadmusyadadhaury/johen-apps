<?php

namespace App\Livewire;

use App\Models\BonusPubg;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class StockDailyTrackingTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $bulan = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editId = null;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public string $tanggal = '';
    public string $nik = '';
    public string $nama = '';
    public string $sesi = '';
    public string $ach_sold = '';
    public string $insentif = '';
    public string $catatan = '';

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'nik' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'sesi' => 'required|string|in:Pagi,Malam',
            'ach_sold' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'sesi.required' => 'Sesi wajib diisi.',
            'ach_sold.required' => 'Sold wajib diisi.',
        ];
    }

    public function updatedNik(string $value): void
    {
        if (!$value) return;
        $employee = Employee::where('nik', $value)->first();
        if ($employee) {
            $this->nama = $employee->nama;
        }
    }

    public function updatedAchSold(string $value): void
    {
        $value = str_replace(',', '.', $value);
        if (is_numeric($value) && $value > 0) {
            $this->insentif = (string) ($value * 100000);
        } else {
            $this->insentif = '0';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingBulan(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $item = BonusPubg::findOrFail($id);
        if (!$this->canModify($item)) return;
        $this->editId = $item->id;
        $this->tanggal = $item->tanggal->format('Y-m-d');
        $this->nik = $item->nik;
        $this->nama = $item->nama;
        $this->sesi = $item->sesi ?? '';
        $this->ach_sold = (string) $item->ach_sold;
        $this->insentif = (string) ($item->ach_sold * 100000);
        $this->catatan = $item->catatan ?? '';
        $this->showEditModal = true;
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editId = null;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate();

        $employee = Employee::where('nik', $this->nik)->first();
        if (!$employee) {
            $this->addError('nik', 'Karyawan dengan NIK tersebut tidak ditemukan.');
            return;
        }

        $sold = str_replace(',', '.', $this->ach_sold);

        $user = auth()->user();
        $status = $user->isKoordinatorStock() ? 'disetujui' : 'pending';

        BonusPubg::create([
            'employee_id' => $employee->id,
            'tanggal' => $this->tanggal,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'sesi' => $this->sesi,
            'divisi' => 'Stock',
            'ach_sold' => $sold,
            'insentif' => $sold * 100000,
            'catatan' => $this->catatan ?: null,
            'status' => $status,
        ]);

        $this->closeModal();
        $message = $status === 'pending'
            ? 'Data berhasil ditambahkan. Menunggu persetujuan koordinator stock.'
            : 'Data berhasil ditambahkan.';
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function update(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate();
        $sold = str_replace(',', '.', $this->ach_sold);
        $item = BonusPubg::findOrFail($this->editId);
        if (!$this->canModify($item)) return;
        $item->update([
            'tanggal' => $this->tanggal,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'sesi' => $this->sesi,
            'ach_sold' => $sold,
            'insentif' => $sold * 100000,
            'catatan' => $this->catatan ?: null,
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data berhasil diperbarui.');
    }

    private function canModify(BonusPubg $item): bool
    {
        $user = auth()->user();
        if ($user->isKoordinatorStock()) return true;
        if ($item->status !== 'pending') return false;
        $employee = $user->employee;
        return $employee && $item->employee_id === $employee->id;
    }

    public function confirmDelete(int $id): void
    {
        $item = BonusPubg::findOrFail($id);
        if (!$this->canModify($item)) return;
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (!$this->deleteId) return;
        $item = BonusPubg::findOrFail($this->deleteId);
        if (!$this->canModify($item)) return;
        $item->delete();
        $this->dispatch('notify', type: 'success', message: 'Data berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function setujui(int $id): void
    {
        $user = auth()->user();
        if (!$user->isKoordinatorStock()) return;

        $item = BonusPubg::findOrFail($id);
        $subordinateIds = $this->getSubordinateEmployeeIds();
        if (!in_array($item->employee_id, $subordinateIds)) return;

        $item->update([
            'status' => 'disetujui',
            'approved_by' => $user->employee->id,
        ]);
        $this->dispatch('notify', type: 'success', message: 'Data berhasil disetujui.');
    }

    public function tolak(int $id): void
    {
        $user = auth()->user();
        if (!$user->isKoordinatorStock()) return;

        $item = BonusPubg::findOrFail($id);
        $subordinateIds = $this->getSubordinateEmployeeIds();
        if (!in_array($item->employee_id, $subordinateIds)) return;

        $item->update(['status' => 'ditolak']);
        $this->dispatch('notify', type: 'success', message: 'Data ditolak.');
    }

    public function render()
    {
        $user = auth()->user();
        $userEmployee = $user->employee;

        $query = BonusPubg::query();

        if ($user->isKoordinatorStock()) {
            if ($userEmployee) {
                $employeeIds = [$userEmployee->id];
                $subordinateIds = $this->getSubordinateEmployeeIds();
                if (!empty($subordinateIds)) {
                    $employeeIds = array_merge($employeeIds, $subordinateIds);
                }
                $query->whereIn('employee_id', $employeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($userEmployee) {
            $query->where('employee_id', $userEmployee->id);
        }

        $items = $query->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nik', 'like', "%{$this->search}%");
            });
        })
        ->when($this->bulan, function ($query) {
            $query->whereYear('tanggal', substr($this->bulan, 0, 4))
                  ->whereMonth('tanggal', substr($this->bulan, 5, 2));
        })
        ->latest('tanggal')
        ->paginate(10);

        $totalSold = 0;
        if ($userEmployee) {
            $statsQuery = BonusPubg::query();
            if ($user->isKoordinatorStock()) {
                $employeeIds = [$userEmployee->id];
                $subordinateIds = $this->getSubordinateEmployeeIds();
                if (!empty($subordinateIds)) {
                    $employeeIds = array_merge($employeeIds, $subordinateIds);
                }
                $statsQuery->whereIn('employee_id', $employeeIds);
            } else {
                $statsQuery->where('employee_id', $userEmployee->id);
            }
            $totalSold = $statsQuery->sum('ach_sold');
        }

        if ($user->isKoordinatorStock()) {
            $ids = $userEmployee ? [$userEmployee->id] : [];
            $subordinateIds = $this->getSubordinateEmployeeIds();
            if (!empty($subordinateIds)) {
                $ids = array_merge($ids, $subordinateIds);
            }
            $employees = Employee::whereIn('id', $ids)->orderBy('nama')->get();
        } else {
            $employees = Employee::where('id', $userEmployee?->id)->orderBy('nama')->get();
        }

        return view('livewire.stock-daily-tracking-table', compact('items', 'employees', 'totalSold'));
    }

    private function getSubordinateEmployeeIds(): array
    {
        $employee = auth()->user()->employee;
        if (!$employee) return [];

        $ids = [];

        $mainPosition = $employee->mainPosition();
        if ($mainPosition) {
            $descendantIds = $this->getAllDescendantIds($mainPosition);
            if (!empty($descendantIds)) {
                $ids = Employee::whereHas('positions', function ($q) use ($descendantIds) {
                    $q->whereIn('position_id', $descendantIds)
                      ->where('is_main', true);
                })->pluck('id')->toArray();
            }
        }

        $user = auth()->user();
        if ($user->isKoordinatorStock()) {
            $roleIds = Employee::whereHas('users', function ($q) {
                $q->where('role', User::ROLE_STAFF_STOCK);
            })->pluck('id')->toArray();
            $ids = array_merge($ids, $roleIds);
        }

        return array_values(array_unique($ids));
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

    private function resetForm(): void
    {
        $this->editId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->nik = '';
        $this->nama = '';
        $this->sesi = '';
        $this->ach_sold = '';
        $this->insentif = '';
        $this->catatan = '';
        $this->resetErrorBag();
    }
}