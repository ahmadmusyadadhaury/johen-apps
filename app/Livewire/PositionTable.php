<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class PositionTable extends Component
{
    public string $search = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editId = null;

    public string $nama = '';
    public ?int $parent_id = null;
    public ?int $division_id = null;
    public string $deskripsi = '';
    public bool $is_active = true;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public ?int $selectedDivision = null;

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:positions,id',
            'division_id' => 'nullable|exists:divisions,id',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama jabatan wajib diisi.',
            'parent_id.exists' => 'Atasan tidak valid.',
            'division_id.exists' => 'Divisi tidak valid.',
        ];
    }

    public function openCreateModal(): void
    {
        abort_unless(auth()->user()->isSuperAdminLike(), 403);
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        abort_unless(auth()->user()->isSuperAdminLike(), 403);
        $pos = Position::findOrFail($id);
        $this->editId = $pos->id;
        $this->nama = $pos->nama;
        $this->parent_id = $pos->parent_id;
        $this->division_id = $pos->division_id;
        $this->deskripsi = $pos->deskripsi ?? '';
        $this->is_active = $pos->is_active;
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
        abort_unless(auth()->user()->isSuperAdminLike(), 403);
        $this->validate($this->rules());

        Position::create($this->buildData());

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Jabatan berhasil ditambahkan.');
    }

    public function update(): void
    {
        abort_unless(auth()->user()->isSuperAdminLike(), 403);
        $pos = Position::findOrFail($this->editId);

        $this->validate($this->rules());

        $pos->update($this->buildData());

        Employee::syncSnapshotsForPosition($pos->id);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Jabatan berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->isSuperAdminLike(), 403);
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (!$this->deleteId) return;
        abort_unless(auth()->user()->isSuperAdminLike(), 403);
        $pos = Position::withCount('employees', 'children')->findOrFail($this->deleteId);

        if ($pos->employees_count > 0) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat menghapus jabatan yang masih memiliki karyawan.');
            $this->cancelDelete();
            return;
        }

        if ($pos->children_count > 0) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat menghapus jabatan yang masih memiliki bawahan. Pindahkan atau hapus bawahan terlebih dahulu.');
            $this->cancelDelete();
            return;
        }

        $pos->delete();
        $this->dispatch('notify', type: 'success', message: 'Jabatan berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()->isSuperAdminLike(), 403);
        $pos = Position::findOrFail($id);
        $pos->update(['is_active' => !$pos->is_active]);
        $this->dispatch('notify', type: 'success', message: 'Status jabatan berhasil diubah.');
    }

    public function selectDivision(?int $divisionId): void
    {
        $this->selectedDivision = $divisionId;
    }

    private array $colorPalette = [
        'primary',
        'purple',
        'blue',
        'green',
        'orange',
        'pink',
        'indigo',
        'teal',
        'cyan',
        'rose',
        'amber',
        'lime',
        'emerald',
    ];

    public function render()
    {
        $positions = Position::with('parent', 'children', 'division')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', "%{$this->search}%");
                });
            })
            ->when($this->selectedDivision, function ($query) {
                $query->where('division_id', $this->selectedDivision);
            })
            ->orderBy('nama')
            ->get();

        $parentOptions = Position::where('is_active', true)
            ->where(function ($q) {
                if ($this->editId) {
                    $q->where('id', '!=', $this->editId);
                }
            })
            ->orderBy('nama')
            ->get();

        $divisions = Division::where('is_active', true)->orderBy('nama')->get()
            ->map(function ($division, $index) {
                $division->color = $this->colorPalette[$index % count($this->colorPalette)];
                return $division;
            });

        $colorMap = $divisions->pluck('color', 'id');

        return view('livewire.position-table', compact('positions', 'parentOptions', 'divisions', 'colorMap'));
    }

    private function buildData(): array
    {
        return [
            'nama' => $this->nama,
            'parent_id' => $this->parent_id ?: null,
            'division_id' => $this->division_id ?: null,
            'deskripsi' => $this->deskripsi ?: null,
            'is_active' => $this->is_active,
        ];
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama = '';
        $this->parent_id = null;
        $this->division_id = null;
        $this->deskripsi = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }
}
