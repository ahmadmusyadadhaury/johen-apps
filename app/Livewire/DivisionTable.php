<?php

namespace App\Livewire;

use App\Models\Division;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class DivisionTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $sortField = 'nama';
    public string $sortDirection = 'asc';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editId = null;

    public string $nama = '';
    public string $koordinator = '';
    public string $deskripsi = '';
    public bool $is_active = true;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    protected $updatesQueryString = ['search', 'filterStatus'];

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'koordinator' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama divisi wajib diisi.',
        ];
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

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->authorizeWrite('create-data');
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->authorizeWrite('update-data');
        $div = Division::findOrFail($id);
        $this->editId = $div->id;
        $this->nama = $div->nama;
        $this->koordinator = $div->koordinator ?? '';
        $this->deskripsi = $div->deskripsi ?? '';
        $this->is_active = $div->is_active;
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
        $this->authorizeWrite('create-data');
        $this->validate($this->rules());

        Division::create($this->buildData());

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Divisi berhasil ditambahkan.');
    }

    public function update(): void
    {
        $this->authorizeWrite('update-data');
        $div = Division::findOrFail($this->editId);

        $this->validate($this->rules());

        $div->update($this->buildData());

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Divisi berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorizeWrite('delete-data');
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (!$this->deleteId) return;
        $this->authorizeWrite('delete-data');
        $div = Division::withCount('employees')->findOrFail($this->deleteId);

        if ($div->employees_count > 0) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat menghapus divisi yang masih memiliki karyawan.');
            $this->cancelDelete();
            return;
        }

        $div->delete();
        $this->dispatch('notify', type: 'success', message: 'Divisi berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    private function authorizeWrite(string $ability): void
    {
        if (auth()->user()->isGmCeo()) {
            abort(403);
        }

        Gate::authorize($ability);
    }

    public function toggleActive(int $id): void
    {
        $this->authorizeWrite('update-data');
        $div = Division::findOrFail($id);
        $div->update(['is_active' => !$div->is_active]);
        $this->dispatch('notify', type: 'success', message: 'Status divisi berhasil diubah.');
    }

    public function render()
    {
        $divisions = Division::withCount('employees')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === 'aktif');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.division-table', compact('divisions'));
    }

    private function buildData(): array
    {
        return [
            'nama' => $this->nama,
            'koordinator' => $this->koordinator ?: null,
            'deskripsi' => $this->deskripsi ?: null,
            'is_active' => $this->is_active,
        ];
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nama = '';
        $this->koordinator = '';
        $this->deskripsi = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }
}
