<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\StokItem;
use App\Models\StokKetersediaan;
use Livewire\Component;
use Livewire\WithPagination;

class StokKetersediaanTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterDivisi = '';
    public string $filterStatus = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;
    public string $deleteType = 'barang';
    public ?int $editId = null;

    public bool $showRekamModal = false;
    public bool $showRekamEditModal = false;
    public ?int $rekamEditId = null;

    public string $nama = '';
    public string $satuan = '';
    public string $division_id = '';

    public string $rekam_division_id = '';
    public string $rekam_tanggal = '';
    public string $stok_hari_ini = '';
    public string $stok_sebelum = '';
    public string $stok_setelah = '';
    public string $jumlah_stok = '';
    public string $status = '';

    public const STATUS_OPTIONS = ['kosong', 'kurang', 'cukup', 'over'];

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ];
    }

    protected function rekamRules(): array
    {
        return [
            'rekam_division_id' => ['required', 'exists:divisions,id'],
            'rekam_tanggal' => ['required', 'date'],
            'stok_hari_ini' => ['required', 'integer', 'min:0'],
            'stok_sebelum' => ['nullable', 'integer', 'min:0'],
            'stok_setelah' => ['nullable', 'integer', 'min:0'],
            'jumlah_stok' => ['nullable', 'integer', 'min:0'],
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
        $item = StokItem::findOrFail($id);
        $this->editId = $item->id;
        $this->nama = $item->nama;
        $this->satuan = $item->satuan ?? '';
        $this->division_id = $item->division_id ? (string) $item->division_id : '';
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

        StokItem::create([
            'nama' => $this->nama,
            'satuan' => $this->satuan ?: null,
            'division_id' => $this->division_id ?: null,
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Barang berhasil ditambahkan.');
    }

    public function update(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate();
        $item = StokItem::findOrFail($this->editId);
        $item->update([
            'nama' => $this->nama,
            'satuan' => $this->satuan ?: null,
            'division_id' => $this->division_id ?: null,
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Barang berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteType = 'barang';
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        if (!$this->deleteId) return;
        if ($this->deleteType === 'rekam') {
            StokKetersediaan::findOrFail($this->deleteId)->delete();
        } else {
            $item = StokItem::findOrFail($this->deleteId);
            $item->stokMasuk()->delete();
            $item->stokKeluar()->delete();
            $item->delete();
        }
        $this->dispatch('notify', type: 'success', message: 'Data berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
        $this->deleteType = 'barang';
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama = '';
        $this->satuan = '';
        $this->division_id = '';
        $this->resetErrorBag();
    }

    public function openRekamModal(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->resetRekamForm();
        $this->showRekamModal = true;
    }

    public function openRekamEditModal(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $item = StokKetersediaan::findOrFail($id);
        $this->rekamEditId = $item->id;
        $this->rekam_division_id = (string) ($item->division_id ?? '');
        $this->rekam_tanggal = $item->tanggal->format('Y-m-d');
        $this->stok_hari_ini = (string) $item->stok_hari_ini;
        $this->stok_sebelum = $item->stok_sebelum !== null ? (string) $item->stok_sebelum : '';
        $this->stok_setelah = $item->stok_setelah !== null ? (string) $item->stok_setelah : '';
        $this->jumlah_stok = $item->jumlah_stok !== null ? (string) $item->jumlah_stok : '';
        $this->status = $item->status;
        $this->showRekamEditModal = true;
    }

    public function closeRekamModal(): void
    {
        $this->showRekamModal = false;
        $this->showRekamEditModal = false;
        $this->rekamEditId = null;
        $this->resetErrorBag();
    }

    public function saveRekam(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate($this->rekamRules());

        StokKetersediaan::create([
            'division_id' => $this->rekam_division_id,
            'tanggal' => $this->rekam_tanggal,
            'stok_hari_ini' => $this->stok_hari_ini,
            'stok_sebelum' => $this->stok_sebelum !== '' ? $this->stok_sebelum : null,
            'stok_setelah' => $this->stok_setelah !== '' ? $this->stok_setelah : null,
            'jumlah_stok' => $this->jumlah_stok !== '' ? $this->jumlah_stok : null,
            'status' => $this->status,
            'created_by' => auth()->id(),
        ]);

        $this->closeRekamModal();
        $this->dispatch('notify', type: 'success', message: 'Record ketersediaan berhasil ditambahkan.');
    }

    public function updateRekam(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate($this->rekamRules());
        $item = StokKetersediaan::findOrFail($this->rekamEditId);
        $item->update([
            'division_id' => $this->rekam_division_id,
            'tanggal' => $this->rekam_tanggal,
            'stok_hari_ini' => $this->stok_hari_ini,
            'stok_sebelum' => $this->stok_sebelum !== '' ? $this->stok_sebelum : null,
            'stok_setelah' => $this->stok_setelah !== '' ? $this->stok_setelah : null,
            'jumlah_stok' => $this->jumlah_stok !== '' ? $this->jumlah_stok : null,
            'status' => $this->status,
        ]);

        $this->closeRekamModal();
        $this->dispatch('notify', type: 'success', message: 'Record ketersediaan berhasil diperbarui.');
    }

    public function confirmDeleteRekam(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteType = 'rekam';
        $this->showDeleteConfirm = true;
    }

    private function resetRekamForm(): void
    {
        $this->rekamEditId = null;
        $this->rekam_division_id = '';
        $this->rekam_tanggal = now()->format('Y-m-d');
        $this->stok_hari_ini = '';
        $this->stok_sebelum = '';
        $this->stok_setelah = '';
        $this->jumlah_stok = '';
        $this->status = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $divisions = Division::orderBy('nama')->get();

        $rekamQuery = StokKetersediaan::with('division');

        if ($this->search) {
            $rekamQuery->whereHas('division', fn ($q) => $q->where('nama', 'like', "%{$this->search}%"));
        }

        if ($this->filterDivisi) {
            $rekamQuery->where('division_id', $this->filterDivisi);
        }

        if ($this->filterStatus) {
            $rekamQuery->where('status', $this->filterStatus);
        }

        $rekamItems = $rekamQuery->latest('tanggal')->latest('id')->paginate(10);

        $totalRecord = $rekamQuery->clone()->count();
        $totalKosong = $rekamQuery->clone()->where('status', 'kosong')->count();
        $totalKurang = $rekamQuery->clone()->where('status', 'kurang')->count();
        $totalOver = $rekamQuery->clone()->where('status', 'over')->count();

        $stokItems = StokItem::with('division')->where('is_active', true)->orderBy('nama')->get();

        return view('livewire.stok-ketersediaan-table', compact(
            'divisions',
            'rekamItems',
            'totalRecord',
            'totalKosong',
            'totalKurang',
            'totalOver',
            'stokItems'
        ));
    }
}
