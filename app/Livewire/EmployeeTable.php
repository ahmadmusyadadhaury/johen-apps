<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'nik';
    public string $sortDirection = 'asc';
    public string $filterDivision = '';
    public string $filterStatus = '';

    public function mount(): void
    {
        $this->filterDivision = request('division', '');
    }

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showPreview = false;
    public ?int $editId = null;
    public int $step = 1;

    public string $nik = '';
    public string $nama = '';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $jenis_kelamin = '';
    public string $alamat = '';
    public string $status = 'aktif';

    public string $position = '';
    public array $position_ids = [];
    public string $main_position_id = '';
    public string $division_id = '';
    public string $atasan = '';
    public string $atasan2 = '';
    public string $tanggal_masuk = '';
    public string $jenis_karyawan = '';
    public string $lokasi_kerja = '';
    public string $jenis_kerja = '';
    public string $jam_kerja = '';
    public string $jobdesk = '';
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public string $no_hp = '';
    public string $email = '';
    public string $no_kontak_darurat1 = '';
    public string $hubungan_darurat1 = '';
    public string $no_kontak_darurat2 = '';
    public string $hubungan_darurat2 = '';
    public string $no_bpjs = '';

    public string $tanggal_resign = '';
    public string $catatan = '';

    public function updatedJenisKerja($value): void
    {
        if ($value === 'Office') {
            $this->jam_kerja = 'Senin - Jumat 08.00-17.00, Sabtu 08.00-12.00';
        }
    }

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'max:30'],
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif,resign',
            'position' => 'nullable|string|max:255',
            'position_ids' => 'nullable|array',
            'position_ids.*' => 'exists:positions,id',
            'main_position_id' => 'nullable|string',
            'division_id' => 'nullable|exists:divisions,id',
            'atasan' => 'nullable|string|max:255',
            'atasan2' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'jenis_karyawan' => 'nullable|string|max:30',
            'lokasi_kerja' => 'nullable|in:Summarecon,Baleendah',
            'jenis_kerja' => 'nullable|in:Office,Operasional',
            'jam_kerja' => 'nullable|string|max:255',
            'jobdesk' => 'nullable|string',
            'no_hp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'no_kontak_darurat1' => 'nullable|string|max:30',
            'hubungan_darurat1' => 'nullable|string|max:50',
            'no_kontak_darurat2' => 'nullable|string|max:30',
            'hubungan_darurat2' => 'nullable|string|max:50',
            'no_bpjs' => 'nullable|string|max:30',
            'tanggal_resign' => 'nullable|date',
            'catatan' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'division_id.exists' => 'Divisi tidak ditemukan.',
        
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

    public function openCreateModal(): void
    {
        Gate::authorize('create-data');
        $this->resetForm();
        $this->step = 1;
        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('update-data');
        $emp = Employee::with('positions')->findOrFail($id);
        $this->editId = $emp->id;
        $this->nik = $emp->nik;
        $this->nama = $emp->nama;
        $this->tempat_lahir = $emp->tempat_lahir ?? '';
        $this->tanggal_lahir = $emp->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->jenis_kelamin = $emp->jenis_kelamin ?? '';
        $this->alamat = $emp->alamat ?? '';
        $this->status = $emp->status;
        $this->position = $emp->position ?? '';
        $this->position_ids = $emp->positions->pluck('id')->toArray();
        $mainPos = $emp->mainPosition();
        $this->main_position_id = (string) ($mainPos?->id ?? '');
        $this->division_id = (string) ($emp->division_id ?? '');
        $this->atasan = $emp->atasan ?? '';
        $this->atasan2 = $emp->atasan2 ?? '';
        $this->tanggal_masuk = $emp->tanggal_masuk?->format('Y-m-d') ?? '';
        $this->jenis_karyawan = $emp->jenis_karyawan ?? '';
        $this->lokasi_kerja = $emp->lokasi_kerja ?? '';
        $this->jenis_kerja = $emp->jenis_kerja ?? '';
        $this->jam_kerja = $emp->jam_kerja ?? '';
        $this->jobdesk = $emp->jobdesk ?? '';
        $this->no_hp = $emp->no_hp ?? '';
        $this->email = $emp->email ?? '';
        $this->no_kontak_darurat1 = $emp->no_kontak_darurat1 ?? '';
        $this->hubungan_darurat1 = $emp->hubungan_darurat1 ?? '';
        $this->no_kontak_darurat2 = $emp->no_kontak_darurat2 ?? '';
        $this->hubungan_darurat2 = $emp->hubungan_darurat2 ?? '';
        $this->no_bpjs = $emp->no_bpjs ?? '';
        $this->tanggal_resign = $emp->tanggal_resign?->format('Y-m-d') ?? '';
        $this->catatan = $emp->catatan ?? '';
        $this->step = 1;
        $this->showEditModal = true;
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showPreview = false;
        $this->editId = null;
        $this->step = 1;
        $this->resetErrorBag();
    }

    public function nextStep(): void
    {
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step--;
    }

    public function goToStep(int $step): void
    {
        $this->step = $step;
    }

    public function confirmPreview(): void
    {
        $this->validate($this->rules());
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showPreview = true;
    }

    public function backToForm(): void
    {
        $this->showPreview = false;
        if ($this->editId) {
            $this->showEditModal = true;
        } else {
            $this->showCreateModal = true;
        }
    }

    public function save(): void
    {
        Gate::authorize('create-data');
        $rules = $this->rules();
        $rules['nik'] = ['required', 'string', 'max:30', 'unique:employees,nik'];
        $this->validate($rules);

        $employee = Employee::create($this->buildData());

        if (!empty($this->position_ids)) {
            $syncData = [];
            foreach ($this->position_ids as $pid) {
                $syncData[$pid] = ['is_main' => $pid == (int) $this->main_position_id];
            }
            $employee->positions()->sync($syncData);
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Karyawan berhasil ditambahkan.');
    }

    public function update(): void
    {
        Gate::authorize('update-data');
        $emp = Employee::findOrFail($this->editId);

        $rules = $this->rules();
        $rules['nik'] = ['required', 'string', 'max:30', 'unique:employees,nik,' . $this->editId];
        $this->validate($rules);

        $emp->update($this->buildData());

        if (!empty($this->position_ids)) {
            $syncData = [];
            foreach ($this->position_ids as $pid) {
                $syncData[$pid] = ['is_main' => $pid == (int) $this->main_position_id];
            }
            $emp->positions()->sync($syncData);
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data karyawan berhasil diperbarui.');
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
        Employee::findOrFail($this->deleteId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Karyawan berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $employees = Employee::with('division')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nik', 'like', "%{$this->search}%")
                      ->orWhere('nama', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('no_hp', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterDivision, function ($query) {
                $query->where('division_id', $this->filterDivision);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->sortField === 'nik', function ($query) {
                $query->orderByRaw('CAST(nik AS UNSIGNED) ' . ($this->sortDirection === 'asc' ? 'asc' : 'desc'));
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(10);

        $divisions = Division::where('is_active', true)->orderBy('nama')->get();
        $allPositions = Position::where('is_active', true)->orderBy('nama')->get();
        return view('livewire.employee-table', [
            'employees' => $employees,
            'divisions' => $divisions,
            'allPositions' => $allPositions,
        ]);
    }

    private function buildData(): array
    {
        $positionNames = [];
        if (!empty($this->position_ids)) {
            $positionNames = Position::whereIn('id', $this->position_ids)->pluck('nama')->toArray();
        }
        $posStr = !empty($positionNames) ? implode(' & ', $positionNames) : ($this->position ?: null);

        return [
            'nik' => $this->nik,
            'nama' => $this->nama,
            'email' => $this->email ?: null,
            'no_hp' => $this->no_hp ?: null,
            'alamat' => $this->alamat ?: null,
            'tempat_lahir' => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'jenis_kelamin' => $this->jenis_kelamin ?: null,
            'division_id' => $this->division_id ?: null,
            'position' => $posStr,
            'atasan' => $this->atasan ?: null,
            'atasan2' => $this->atasan2 ?: null,
            'jenis_karyawan' => $this->jenis_karyawan ?: null,
            'lokasi_kerja' => $this->lokasi_kerja ?: null,
            'jenis_kerja' => $this->jenis_kerja ?: null,
            'jam_kerja' => $this->jam_kerja ?: null,
            'jobdesk' => $this->jobdesk ?: null,
            'no_kontak_darurat1' => $this->no_kontak_darurat1 ?: null,
            'hubungan_darurat1' => $this->hubungan_darurat1 ?: null,
            'no_kontak_darurat2' => $this->no_kontak_darurat2 ?: null,
            'hubungan_darurat2' => $this->hubungan_darurat2 ?: null,
            'no_bpjs' => $this->no_bpjs ?: null,
            'status' => $this->status,
            'tanggal_masuk' => $this->tanggal_masuk ?: null,
            'tanggal_resign' => $this->tanggal_resign ?: null,
            'catatan' => $this->catatan ?: null,
        ];
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nik = '';
        $this->nama = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = '';
        $this->jenis_kelamin = '';
        $this->alamat = '';
        $this->status = 'aktif';
        $this->position = '';
        $this->position_ids = [];
        $this->main_position_id = '';
        $this->division_id = '';
        $this->atasan = '';
        $this->atasan2 = '';
        $this->tanggal_masuk = '';
        $this->jenis_karyawan = '';
        $this->lokasi_kerja = '';
        $this->jenis_kerja = '';
        $this->jam_kerja = '';
        $this->jobdesk = '';
        $this->no_hp = '';
        $this->email = '';
        $this->no_kontak_darurat1 = '';
        $this->hubungan_darurat1 = '';
        $this->no_kontak_darurat2 = '';
        $this->hubungan_darurat2 = '';
        $this->no_bpjs = '';
        $this->tanggal_resign = '';
        $this->catatan = '';
        $this->step = 1;
        $this->resetErrorBag();
    }
}
