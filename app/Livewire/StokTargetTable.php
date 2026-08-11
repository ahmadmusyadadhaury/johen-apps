<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\StokTarget;
use Livewire\Component;
use Livewire\WithPagination;

class StokTargetTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterDivisi = '';
    public string $filterStatus = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;
    public ?int $editId = null;

    public string $division_id = '';
    public string $stok_harian = '';
    public string $stok_mingguan = '';
    public string $stok_bulanan = '';
    public string $status = '';

    public const STATUS_OPTIONS = ['kosong', 'kurang', 'cukup', 'over'];

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'division_id' => ['required', 'exists:divisions,id'],
            'stok_harian' => ['required', 'integer', 'min:0'],
            'stok_mingguan' => ['required', 'integer', 'min:0'],
            'stok_bulanan' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:' . implode(',', self::STATUS_OPTIONS)],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDivisi(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
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
        $item = StokTarget::findOrFail($id);
        $this->editId = $item->id;
        $this->division_id = (string) ($item->division_id ?? '');
        $this->stok_harian = (string) $item->stok_harian;
        $this->stok_mingguan = (string) $item->stok_mingguan;
        $this->stok_bulanan = (string) $item->stok_bulanan;
        $this->status = $item->status;
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

        StokTarget::create([
            'division_id' => $this->division_id,
            'stok_harian' => $this->stok_harian,
            'stok_mingguan' => $this->stok_mingguan,
            'stok_bulanan' => $this->stok_bulanan,
            'status' => $this->status,
            'created_by' => auth()->id(),
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Target stok berhasil ditambahkan.');
    }

    public function update(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate();
        $item = StokTarget::findOrFail($this->editId);
        $item->update([
            'division_id' => $this->division_id,
            'stok_harian' => $this->stok_harian,
            'stok_mingguan' => $this->stok_mingguan,
            'stok_bulanan' => $this->stok_bulanan,
            'status' => $this->status,
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Target stok berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        if (!$this->deleteId) return;
        StokTarget::findOrFail($this->deleteId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Target stok berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->division_id = '';
        $this->stok_harian = '';
        $this->stok_mingguan = '';
        $this->stok_bulanan = '';
        $this->status = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = StokTarget::with('division');

        if ($this->search) {
            $query->whereHas('division', fn ($q) => $q->where('nama', 'like', "%{$this->search}%"));
        }

        if ($this->filterDivisi) {
            $query->where('division_id', $this->filterDivisi);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->latest('id')->paginate(10);

        $totalKosong = $query->clone()->where('status', 'kosong')->count();
        $totalKurang = $query->clone()->where('status', 'kurang')->count();
        $totalCukup = $query->clone()->where('status', 'cukup')->count();
        $totalOver = $query->clone()->where('status', 'over')->count();

        $divisions = Division::orderBy('nama')->get();

        return view('livewire.stok-target-table', compact('items', 'divisions', 'totalKosong', 'totalKurang', 'totalCukup', 'totalOver'));
    }
}
