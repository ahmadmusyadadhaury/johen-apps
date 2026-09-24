<?php

namespace App\Livewire;

use App\Models\BonusCreative;
use App\Models\Employee;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BonusCreativeTable extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $bulan = '';
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
    public string $keterangan = '';
    public $dokumentasi;
    public string $dokumentasiPath = '';
    public string $insentif = '';
    public string $pencapaian = '';
    public string $total = '';

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'nik' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'insentif' => 'required|numeric|min:0',
            'pencapaian' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ];
    }

    protected function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'nik.required' => 'NIK wajib dipilih.',
            'nama.required' => 'Nama wajib diisi.',
            'insentif.required' => 'Insentif wajib diisi.',
            'pencapaian.required' => 'Pencapaian wajib diisi.',
            'total.required' => 'Total wajib diisi.',
            'dokumentasi.image' => 'Dokumentasi harus berupa gambar.',
            'dokumentasi.mimes' => 'Dokumentasi harus format JPG/PNG.',
            'dokumentasi.max' => 'Ukuran dokumentasi maksimal 10MB.',
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

    public function updatedDokumentasi(): void
    {
        $this->validate(['dokumentasi' => 'image|mimes:jpg,jpeg,png|max:10240']);
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

    public function openCreateModal(): void
    {
        Gate::authorize('create-data');
        $this->resetForm();

        $user = auth()->user();
        if ($user->employee && ($user->isStaffHostPubg() || $user->isStaffHostFf() || $user->isStaffHostMlbb() || $user->isStaffHostEfootball() || $user->isStaffHostValorant() || $user->isStaffHostRoblox() || $user->isStaffHostMonkeyPubg() || $user->isStaffHostFcMobile())) {
            $this->nik = $user->employee->nik;
            $this->nama = $user->employee->nama;
        }

        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('update-data');
        $item = BonusCreative::findOrFail($id);
        $this->editId = $item->id;
        $this->tanggal = $item->tanggal->format('Y-m-d');
        $this->nik = $item->nik;
        $this->nama = $item->nama;
        $this->keterangan = $item->keterangan ?? '';
        $this->dokumentasiPath = $item->dokumentasi ?? '';
        $this->insentif = (string) $item->insentif;
        $this->pencapaian = (string) $item->pencapaian;
        $this->total = (string) $item->total;
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

        $data = $this->buildData();

        if ($this->dokumentasi) {
            $data['dokumentasi'] = $this->dokumentasi->store('bonus/creative', 'public');
        }

        BonusCreative::create($data);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data Creative berhasil ditambahkan.');
    }

    public function update(): void
    {
        Gate::authorize('update-data');
        $item = BonusCreative::findOrFail($this->editId);

        $rules = $this->rules();
        $rules['dokumentasi'] = 'nullable|image|mimes:jpg,jpeg,png|max:10240';
        $this->validate($rules);

        $data = $this->buildData();

        if ($this->dokumentasi) {
            if ($item->dokumentasi) {
                Storage::disk('public')->delete($item->dokumentasi);
            }
            $data['dokumentasi'] = $this->dokumentasi->store('bonus/creative', 'public');
        }

        $item->update($data);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data Creative berhasil diperbarui.');
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
        $item = BonusCreative::findOrFail($this->deleteId);
        if ($item->dokumentasi) {
            Storage::disk('public')->delete($item->dokumentasi);
        }
        $item->delete();
        $this->dispatch('notify', type: 'success', message: 'Data Creative berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $items = BonusCreative::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nik', 'like', "%{$this->search}%")
                  ->orWhere('keterangan', 'like', "%{$this->search}%");
            });
        })
        ->when($this->bulan, function ($query) {
            $query->whereYear('tanggal', substr($this->bulan, 0, 4))
                  ->whereMonth('tanggal', substr($this->bulan, 5, 2));
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate(10);

        $employees = Employee::orderBy('nama')->get();

        return view('livewire.bonus-creative-table', compact('items', 'employees'));
    }

    private function buildData(): array
    {
        return [
            'tanggal' => $this->tanggal,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'keterangan' => $this->keterangan ?: null,
            'insentif' => str_replace(',', '.', $this->insentif),
            'pencapaian' => str_replace(',', '.', $this->pencapaian),
            'total' => str_replace(',', '.', $this->total),
        ];
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->nik = '';
        $this->nama = '';
        $this->keterangan = '';
        $this->dokumentasi = null;
        $this->dokumentasiPath = '';
        $this->insentif = '';
        $this->pencapaian = '';
        $this->total = '';
        $this->resetErrorBag();
    }
}
