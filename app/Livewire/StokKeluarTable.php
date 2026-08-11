<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\StokItem;
use App\Models\StokKeluar;
use Livewire\Component;
use Livewire\WithPagination;

class StokKeluarTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterDivisi = '';
    public string $filterBulan = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;
    public ?int $editId = null;

    public string $item_id = '';
    public string $tanggal = '';
    public string $jumlah = '';
    public string $tujuan = '';
    public string $keterangan = '';

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'item_id' => ['required', 'exists:stok_items,id'],
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tujuan' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
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

    public function updatingFilterBulan(): void
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
        $item = StokKeluar::findOrFail($id);
        $this->editId = $item->id;
        $this->item_id = (string) $item->item_id;
        $this->tanggal = $item->tanggal->format('Y-m-d');
        $this->jumlah = (string) $item->jumlah;
        $this->tujuan = $item->tujuan ?? '';
        $this->keterangan = $item->keterangan ?? '';
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

        StokKeluar::create([
            'item_id' => $this->item_id,
            'tanggal' => $this->tanggal,
            'jumlah' => $this->jumlah,
            'tujuan' => $this->tujuan ?: null,
            'keterangan' => $this->keterangan ?: null,
            'created_by' => auth()->id(),
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Stok keluar berhasil ditambahkan.');
    }

    public function update(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate();
        $item = StokKeluar::findOrFail($this->editId);
        $item->update([
            'item_id' => $this->item_id,
            'tanggal' => $this->tanggal,
            'jumlah' => $this->jumlah,
            'tujuan' => $this->tujuan ?: null,
            'keterangan' => $this->keterangan ?: null,
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Stok keluar berhasil diperbarui.');
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
        StokKeluar::findOrFail($this->deleteId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Data berhasil dihapus.');
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
        $this->item_id = '';
        $this->tanggal = now()->format('Y-m-d');
        $this->jumlah = '';
        $this->tujuan = '';
        $this->keterangan = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = StokKeluar::with('item.division');

        if ($this->search) {
            $query->whereHas('item', function ($q) {
                $q->where('nama', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterDivisi) {
            $query->whereHas('item', fn ($q) => $q->where('division_id', $this->filterDivisi));
        }

        if ($this->filterBulan) {
            $query->whereYear('tanggal', substr($this->filterBulan, 0, 4))
                  ->whereMonth('tanggal', substr($this->filterBulan, 5, 2));
        }

        $items = $query->latest('tanggal')->latest('id')->paginate(10);

        $totalJumlah = $query->clone()->sum('jumlah');

        $divisions = Division::orderBy('nama')->get();
        $stokItems = StokItem::where('is_active', true)->orderBy('nama')->get();

        return view('livewire.stok-keluar-table', compact('items', 'totalJumlah', 'divisions', 'stokItems'));
    }
}
