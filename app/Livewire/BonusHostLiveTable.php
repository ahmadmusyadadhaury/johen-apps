<?php

namespace App\Livewire;

use App\Models\AttendanceSession;
use App\Models\BonusHostLive;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BonusHostLiveTable extends Component
{
    use WithFileUploads, WithPagination;

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

    public string $jam_mulai = '';

    public string $jam_selesai = '';

    public string $nik = '';

    public string $nama = '';

    public string $jabatan = '';

    public string $divisi = '';

    public string $sesi = '';

    public string $ach_sold = '';

    public string $ach_view = '';

    public string $peak_view = '';

    public string $catatan = '';

    public $foto_statistik;

    public $foto_bukti_live;

    public string $fotoStatistikPath = '';

    public string $fotoBuktiLivePath = '';

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'nik' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'sesi' => 'required|in:Pagi,Siang,Malam,Subuh',
            'ach_sold' => 'required|numeric|min:1',
            'ach_view' => 'required|numeric|min:0',
            'peak_view' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'foto_statistik' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'foto_bukti_live' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
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
            'ach_view.required' => 'View wajib diisi.',
            'peak_view.required' => 'PEAK VIEW wajib diisi.',
            'foto_statistik.image' => 'Foto statistik harus berupa gambar.',
            'foto_statistik.mimes' => 'Foto statistik harus format JPG/PNG.',
            'foto_statistik.max' => 'Ukuran foto statistik maksimal 10MB.',
            'foto_bukti_live.image' => 'Foto bukti live harus berupa gambar.',
            'foto_bukti_live.mimes' => 'Foto bukti live harus format JPG/PNG.',
            'foto_bukti_live.max' => 'Ukuran foto bukti live maksimal 10MB.',
        ];
    }

    public function updatedNik(string $value): void
    {
        if (! $value) {
            return;
        }
        $employee = Employee::with('divisions')->where('nik', $value)->first();
        if ($employee) {
            $this->nama = $employee->nama;
            $this->jabatan = $employee->position;
            $this->divisi = $employee->divisions->first()?->nama ?? '';
        }
    }

    public function updatedSesi(string $value): void
    {
        $config = AttendanceSession::sessionConfigByNama($value);
        if ($config) {
            $this->jam_mulai = $config['mulai'];
            $this->jam_selesai = $config['selesai'];
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
        $item = BonusHostLive::findOrFail($id);
        $this->editId = $item->id;
        $this->tanggal = $item->tanggal->format('Y-m-d');
        $this->jam_mulai = $item->jam_mulai ? substr($item->jam_mulai, 0, 5) : '';
        $this->jam_selesai = $item->jam_selesai ? substr($item->jam_selesai, 0, 5) : '';
        $this->nik = $item->nik;
        $this->nama = $item->nama;
        $this->jabatan = $item->jabatan;
        $this->divisi = $item->divisi;
        $this->sesi = $item->sesi;
        $this->ach_sold = (string) $item->ach_sold;
        $this->ach_view = (string) $item->ach_view;
        $this->peak_view = (string) $item->peak_view;
        $this->catatan = $item->catatan ?? '';
        $this->fotoStatistikPath = $item->foto_statistik ?? '';
        $this->fotoBuktiLivePath = $item->foto_bukti_live ?? '';
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

        if ($this->foto_statistik) {
            $data['foto_statistik'] = $this->foto_statistik->store('bonus/host-live', 'public');
        }
        if ($this->foto_bukti_live) {
            $data['foto_bukti_live'] = $this->foto_bukti_live->store('bonus/host-live', 'public');
        }

        BonusHostLive::create($data);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data Host Live berhasil ditambahkan.');
    }

    public function update(): void
    {
        Gate::authorize('update-data');
        $item = BonusHostLive::findOrFail($this->editId);

        $rules = $this->rules();
        $rules['foto_statistik'] = 'nullable|image|mimes:jpg,jpeg,png|max:10240';
        $rules['foto_bukti_live'] = 'nullable|image|mimes:jpg,jpeg,png|max:10240';
        $this->validate($rules);

        $data = $this->buildData();

        if ($this->foto_statistik) {
            if ($item->foto_statistik) {
                Storage::disk('public')->delete($item->foto_statistik);
            }
            $data['foto_statistik'] = $this->foto_statistik->store('bonus/host-live', 'public');
        }
        if ($this->foto_bukti_live) {
            if ($item->foto_bukti_live) {
                Storage::disk('public')->delete($item->foto_bukti_live);
            }
            $data['foto_bukti_live'] = $this->foto_bukti_live->store('bonus/host-live', 'public');
        }

        $item->update($data);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data Host Live berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('delete-data');
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (! $this->deleteId) {
            return;
        }
        Gate::authorize('delete-data');
        $item = BonusHostLive::findOrFail($this->deleteId);
        if ($item->foto_statistik) {
            Storage::disk('public')->delete($item->foto_statistik);
        }
        if ($item->foto_bukti_live) {
            Storage::disk('public')->delete($item->foto_bukti_live);
        }
        $item->delete();
        $this->dispatch('notify', type: 'success', message: 'Data Host Live berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $items = BonusHostLive::when($this->search, function ($query) {
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
            ->where('position', 'like', 'Host%')
            ->orderBy('nama')
            ->get();

        $divisiList = ['Johen PUBG', 'Monkey PUBG', 'Mobile Legend', 'Free Fire', 'Roblox', 'e-Football', 'Valorant'];
        $divisions = Division::whereIn('nama', $divisiList)->orderBy('nama')->get();

        return view('livewire.bonus-host-live-table', compact('items', 'employees', 'divisions'));
    }

    private function buildData(): array
    {
        return [
            'tanggal' => $this->tanggal,
            'jam_mulai' => $this->jam_mulai ?: null,
            'jam_selesai' => $this->jam_selesai ?: null,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'jabatan' => $this->jabatan,
            'divisi' => $this->divisi,
            'sesi' => $this->sesi,
            'ach_sold' => str_replace(',', '.', $this->ach_sold),
            'ach_view' => str_replace(',', '.', $this->ach_view),
            'peak_view' => str_replace(',', '.', $this->peak_view),
            'catatan' => $this->catatan ?: null,
        ];
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->jam_mulai = '';
        $this->jam_selesai = '';
        $this->nik = '';
        $this->nama = '';
        $this->jabatan = '';
        $this->divisi = '';
        $this->sesi = '';
        $this->ach_sold = '';
        $this->ach_view = '';
        $this->peak_view = '';
        $this->catatan = '';
        $this->foto_statistik = null;
        $this->foto_bukti_live = null;
        $this->fotoStatistikPath = '';
        $this->fotoBuktiLivePath = '';
        $this->resetErrorBag();
    }
}
