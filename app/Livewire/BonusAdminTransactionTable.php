<?php

namespace App\Livewire;

use App\Models\BonusAdminTransaction;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class BonusAdminTransactionTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $bulan = '';
    public string $divisiFilter = '';
    public string $sortField = 'tanggal';
    public string $sortDirection = 'desc';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editId = null;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public string $tanggal = '';
    public string $nik = '';
    public string $nama = '';
    public string $jabatan = '';
    public string $divisi = '';
    public string $sesi = '';
    public string $ach_sold = '';

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'nik' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'sesi' => 'required|in:Day Shift,Night Shift',
            'ach_sold' => 'required|numeric|min:1',
        ];
    }

    protected function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'nik.required' => 'NIK wajib dipilih.',
            'nama.required' => 'Nama wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'divisi.required' => 'Divisi wajib diisi.',
            'sesi.required' => 'Sesi wajib diisi.',
            'ach_sold.required' => 'Sold wajib diisi.',
            'ach_sold.min' => 'Sold harus lebih dari 0.',
        ];
    }

    public function updatedNik(string $value): void
    {
        if (!$value) return;
        $employee = Employee::with('divisions')->where('nik', $value)->first();
        if ($employee) {
            $this->nama = $employee->nama;
            $this->jabatan = $employee->position;
            $this->divisi = $employee->divisions->first()?->nama ?? '';
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
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

    public function updatingDivisiFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        Gate::authorize('create-data');
        $this->resetForm();

        $user = auth()->user();
        if ($user->employee && ($user->isStaffHostPubg() || $user->isStaffHostFf() || $user->isStaffHostMlbb() || $user->isStaffHostEfootball() || $user->isStaffHostValorant() || $user->isStaffHostRoblox() || $user->isStaffHostMonkeyPubg())) {
            $this->nik = $user->employee->nik;
            $this->nama = $user->employee->nama;
            $this->jabatan = $user->employee->position;
            $this->divisi = $user->employee->divisions->first()?->nama ?? '';
        }

        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('update-data');
        $item = BonusAdminTransaction::findOrFail($id);
        $this->editId = $item->id;
        $this->tanggal = $item->tanggal->format('Y-m-d');
        $this->nik = $item->nik;
        $this->nama = $item->nama;
        $this->jabatan = $item->jabatan;
        $this->divisi = $item->divisi;
        $this->sesi = $item->sesi;
        $this->ach_sold = (string) $item->ach_sold;
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
        Gate::authorize('create-data');
        $this->validate();

        BonusAdminTransaction::create($this->buildData());

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data Admin Transaksi berhasil ditambahkan.');
    }

    public function update(): void
    {
        Gate::authorize('update-data');
        $item = BonusAdminTransaction::findOrFail($this->editId);

        $this->validate();

        $item->update($this->buildData());

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data Admin Transaksi berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('delete-data');
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (!$this->deleteId) return;
        Gate::authorize('delete-data');
        $item = BonusAdminTransaction::findOrFail($this->deleteId);
        $item->delete();
        $this->dispatch('notify', type: 'success', message: 'Data Admin Transaksi berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $items = BonusAdminTransaction::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nik', 'like', "%{$this->search}%")
                  ->orWhere('sesi', 'like', "%{$this->search}%");
            });
        })
        ->when($this->bulan, function ($query) {
            $query->whereYear('tanggal', substr($this->bulan, 0, 4))
                  ->whereMonth('tanggal', substr($this->bulan, 5, 2));
        })
        ->when($this->divisiFilter, function ($query) {
            $query->where('divisi', $this->divisiFilter);
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate(10);

        $employees = Employee::with('divisions')
            ->whereHas('divisions', fn($q) => $q->where('nama', 'Admin Transaksi'))
            ->orderBy('nama')
            ->get();

        $divisiList = ['Johen PUBG', 'Monkey PUBG', 'Mobile Legend', 'Free Fire', 'Roblox', 'e-Football', 'Valorant'];
        $divisions = Division::whereIn('nama', $divisiList)->orderBy('nama')->get();

        return view('livewire.bonus-admin-transaction-table', compact('items', 'employees', 'divisions'));
    }

    private function buildData(): array
    {
        return [
            'tanggal' => $this->tanggal,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'jabatan' => $this->jabatan,
            'divisi' => $this->divisi,
            'sesi' => $this->sesi,
            'ach_sold' => str_replace(',', '.', $this->ach_sold),
        ];
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->nik = '';
        $this->nama = '';
        $this->jabatan = '';
        $this->divisi = '';
        $this->sesi = '';
        $this->ach_sold = '';
        $this->resetErrorBag();
    }
}
