<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\StokMasuk;
use Livewire\Component;
use Livewire\WithPagination;

class StokMasukTable extends Component
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

    public string $division_id = '';
    public string $tanggal = '';
    public string $nomor = '';
    public string $id_game = '';
    public string $spek = '';
    public string $sumber = '';
    public string $zimbra = '';

    protected $updatesQueryString = ['search'];

    private const EFUTBOL_DIVISIONS = ['e-football', 'fc mobile'];

    public static function isEfootballDivision(mixed $divisionId): bool
    {
        if (!$divisionId) return false;
        $division = Division::find($divisionId);
        return $division && in_array(strtolower($division->nama), self::EFUTBOL_DIVISIONS, true);
    }

    public function columnType(): string
    {
        if (!$this->filterDivisi) return 'all';
        return self::isEfootballDivision($this->filterDivisi) ? 'efoot' : 'game';
    }

    protected function rules(): array
    {
        return [
            'division_id' => ['required', 'exists:divisions,id'],
            'tanggal' => ['required', 'date'],
            'nomor' => ['required', 'string', 'max:255'],
            'id_game' => ['nullable', 'string', 'max:255'],
            'spek' => ['nullable', 'string', 'max:255'],
            'sumber' => ['nullable', 'string', 'max:255'],
            'zimbra' => ['nullable', 'string', 'max:255'],
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
        $item = StokMasuk::findOrFail($id);
        $this->editId = $item->id;
        $this->division_id = (string) ($item->division_id ?? '');
        $this->tanggal = $item->tanggal->format('Y-m-d');
        $this->nomor = $item->nomor ?? '';
        $this->id_game = $item->id_game ?? '';
        $this->spek = $item->spek ?? '';
        $this->sumber = $item->sumber ?? '';
        $this->zimbra = $item->zimbra ?? '';
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

        StokMasuk::create([
            'division_id' => $this->division_id,
            'tanggal' => $this->tanggal,
            'nomor' => $this->nomor,
            'id_game' => $this->id_game ?: null,
            'spek' => $this->spek ?: null,
            'sumber' => $this->sumber ?: null,
            'zimbra' => $this->zimbra ?: null,
            'created_by' => auth()->id(),
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Stok masuk berhasil ditambahkan.');
    }

    public function update(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate();
        $item = StokMasuk::findOrFail($this->editId);
        $item->update([
            'division_id' => $this->division_id,
            'tanggal' => $this->tanggal,
            'nomor' => $this->nomor,
            'id_game' => $this->id_game ?: null,
            'spek' => $this->spek ?: null,
            'sumber' => $this->sumber ?: null,
            'zimbra' => $this->zimbra ?: null,
        ]);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Stok masuk berhasil diperbarui.');
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
        StokMasuk::findOrFail($this->deleteId)->delete();
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
        $this->division_id = '';
        $this->tanggal = now()->format('Y-m-d');
        $this->nomor = '';
        $this->id_game = '';
        $this->spek = '';
        $this->sumber = '';
        $this->zimbra = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = StokMasuk::with('division');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nomor', 'like', "%{$this->search}%")
                    ->orWhere('id_game', 'like', "%{$this->search}%")
                    ->orWhere('spek', 'like', "%{$this->search}%")
                    ->orWhere('sumber', 'like', "%{$this->search}%")
                    ->orWhere('zimbra', 'like', "%{$this->search}%")
                    ->orWhereHas('division', fn ($q2) => $q2->where('nama', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterDivisi) {
            $query->where('division_id', $this->filterDivisi);
        }

        if ($this->filterBulan) {
            $query->whereYear('tanggal', substr($this->filterBulan, 0, 4))
                  ->whereMonth('tanggal', substr($this->filterBulan, 5, 2));
        }

        $items = $query->latest('tanggal')->latest('id')->paginate(10);

        $totalMasuk = $query->clone()->count();

        $divisions = Division::orderBy('nama')->get();

        $type = $this->columnType();

        return view('livewire.stok-masuk-table', compact('items', 'totalMasuk', 'divisions', 'type'));
    }
}
