<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Region;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
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
        $this->provinceList = Region::provinces()->pluck('name', 'id')->toArray();
    }

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showPreview = false;

    public ?int $editId = null;

    public int $step = 1;

    public string $nik = '';

    public string $nik_ktp = '';

    public string $nama = '';

    public string $tempat_lahir = '';

    public string $tanggal_lahir = '';

    public string $jenis_kelamin = '';

    public string $ukuran_baju = '';

    public string $agama = '';

    public string $pendidikan_terakhir = '';

    public string $informasi_lowongan = '';

    public string $alamat = '';

    public string $provinsi = '';

    public string $kota = '';

    public string $kecamatan = '';

    public string $kelurahan = '';

    public string $kode_pos = '';

    public array $provinceList = [];

    public array $cityList = [];

    public array $districtList = [];

    public array $villageList = [];

    public string $status = 'aktif';

    public string $status_pernikahan = '';

    public string $position = '';

    public array $position_ids = [];

    public string $main_position_id = '';

    public array $division_ids = [];

    public string $atasan = '';

    public string $atasan2 = '';

    public string $tanggal_masuk = '';

    public string $jenis_karyawan = '';

    public string $lokasi_kerja = '';

    public string $jenis_kerja = '';

    public string $jam_kerja = '';

    public string $jam_masuk = '';

    public string $jam_kerja_effective = '';

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

    public string $status_bpjs = '';

    public string $tanggal_resign = '';

    public string $catatan = '';

    public function updatedJamKerja($value): void
    {
        // Pilih shift otomatis mengisi jam masuk sebagai acuan telat
        // (status terlambat/tepat waktu memakai shift + toleransi 5 menit).
        if ($value && isset(Employee::SHIFT_OPTIONS[$value])) {
            $this->jam_masuk = Employee::SHIFT_OPTIONS[$value];
        }
    }

    public function updatedPositionIds($value): void
    {
        $this->syncJobdeskFromPositions();
    }

    public function updatedMainPositionId($value): void
    {
        $this->syncJobdeskFromPositions();
    }

    private function syncJobdeskFromPositions(): void
    {
        if (empty($this->position_ids)) {
            $this->jobdesk = '';

            return;
        }

        $positions = Position::whereIn('id', $this->position_ids)->get()
            ->sortBy('nama')
            ->sortBy(fn ($pos) => $pos->id == (int) $this->main_position_id ? 0 : 1);

        $blocks = $positions->map(function ($pos) {
            $judul = strtoupper($pos->nama);
            $deskripsi = trim(strip_tags($pos->deskripsi ?: ''));

            return $judul.PHP_EOL.($deskripsi === '' ? '-' : $deskripsi);
        });

        $this->jobdesk = $blocks->implode(PHP_EOL.PHP_EOL);
    }

    public function updatedProvinsi($value): void
    {
        $this->kota = '';
        $this->kecamatan = '';
        $this->kelurahan = '';
        $this->cityList = $value
            ? Region::where('type', Region::TYPE_KABUPATEN)->where('parent_id', $value)->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
        $this->districtList = [];
        $this->villageList = [];
    }

    public function updatedKota($value): void
    {
        $this->kecamatan = '';
        $this->kelurahan = '';
        $this->districtList = $value
            ? Region::where('type', Region::TYPE_KECAMATAN)->where('parent_id', $value)->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
        $this->villageList = [];
    }

    public function updatedKecamatan($value): void
    {
        $this->kelurahan = '';
        $this->villageList = $value
            ? Region::where('type', Region::TYPE_KELURAHAN)->where('parent_id', $value)->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
    }

    private function loadRegionLists(): void
    {
        $this->provinceList = Region::provinces()->pluck('name', 'id')->toArray();
        $this->cityList = $this->provinsi
            ? Region::where('type', Region::TYPE_KABUPATEN)->where('parent_id', $this->provinsi)->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
        $this->districtList = $this->kota
            ? Region::where('type', Region::TYPE_KECAMATAN)->where('parent_id', $this->kota)->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
        $this->villageList = $this->kecamatan
            ? Region::where('type', Region::TYPE_KELURAHAN)->where('parent_id', $this->kecamatan)->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
    }

    private function provinceIdForName(?string $name): string
    {
        if (! $name) {
            return '';
        }
        $row = Region::where('type', Region::TYPE_PROVINSI)->where('name', $name)->first();

        return (string) ($row->id ?? '');
    }

    private function regionIdForParentId(string $parentId, string $type, ?string $name): string
    {
        if (! $parentId || ! $name) {
            return '';
        }
        $row = Region::where('type', $type)->where('parent_id', $parentId)->where('name', $name)->first();

        return (string) ($row->id ?? '');
    }

    protected $updatesQueryString = ['search'];

    protected function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'max:30'],
            'nik_ktp' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/'],
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'ukuran_baju' => 'required|in:S,M,L,XL,XXL',
            'agama' => 'required|string|max:50',
            'pendidikan_terakhir' => 'required|string|max:100',
            'informasi_lowongan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'provinsi' => 'required|string|max:150',
            'kota' => 'required|string|max:150',
            'kecamatan' => 'required|string|max:150',
            'kelurahan' => 'required|string|max:150',
            'kode_pos' => 'required|string|max:10',
            'status' => 'required|in:aktif,nonaktif,resign',
            'status_pernikahan' => 'required|in:sudah menikah,belum menikah',
            'position' => 'nullable|string|max:255',
            'position_ids' => 'required|array|min:1',
            'position_ids.*' => 'exists:positions,id',
            'main_position_id' => 'required|string',
            'division_ids' => 'required|array|min:1',
            'division_ids.*' => 'exists:divisions,id',
            'atasan' => ['required', 'string', Rule::in(Employee::ATASAN_OPTIONS)],
            'atasan2' => ['nullable', 'string', Rule::in(Employee::ATASAN_OPTIONS)],
            'tanggal_masuk' => 'required|date',
            'jenis_karyawan' => 'required|string|max:30',
            'lokasi_kerja' => 'required|in:Summarecon,Baleendah',
            'jenis_kerja' => 'required|in:Office,Operasional',
            'jam_kerja' => 'required|string|max:255',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_kerja_effective' => 'nullable|date|after_or_equal:2000-01-01',
            'jobdesk' => 'required|string',
            'no_hp' => 'required|string|max:30',
            'email' => 'required|email|max:255',
            'no_kontak_darurat1' => 'required|string|max:30',
            'hubungan_darurat1' => 'required|string|max:50',
            'no_kontak_darurat2' => 'required|string|max:30',
            'hubungan_darurat2' => 'required|string|max:50',
            'no_bpjs' => 'required|string|max:30',
            'status_bpjs' => 'required|in:aktif,tidak aktif',
            'tanggal_resign' => 'nullable|date',
            'catatan' => 'nullable|string',
        ];
    }

    protected function stepRules(int $step): array
    {
        $all = $this->rules();

        $fields = match ($step) {
            1 => ['nik_ktp', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'status', 'status_pernikahan', 'ukuran_baju', 'agama', 'pendidikan_terakhir', 'provinsi', 'kota', 'kecamatan', 'kelurahan', 'kode_pos', 'alamat'],
            2 => ['nik', 'position_ids', 'main_position_id', 'division_ids', 'atasan', 'atasan2', 'tanggal_masuk', 'jenis_karyawan', 'lokasi_kerja', 'jenis_kerja', 'jam_kerja', 'jobdesk'],
            3 => ['no_hp', 'email', 'informasi_lowongan', 'no_kontak_darurat1', 'hubungan_darurat1', 'no_kontak_darurat2', 'hubungan_darurat2', 'no_bpjs', 'status_bpjs'],
            default => array_keys($all),
        };

        return array_intersect_key($all, array_flip($fields));
    }

    protected function messages(): array
    {
        return [
            'nik.required' => 'NIP wajib diisi.',
            'nik.unique' => 'NIP sudah terdaftar.',
            'nik_ktp.required' => 'NIK KTP wajib diisi.',
            'nik_ktp.regex' => 'NIK KTP hanya boleh berisi angka.',
            'nama.required' => 'Nama wajib diisi.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Tanggal lahir tidak valid.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'ukuran_baju.required' => 'Ukuran baju wajib dipilih.',
            'agama.required' => 'Agama wajib dipilih.',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib dipilih.',
            'informasi_lowongan.required' => 'Informasi lowongan wajib dipilih.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'provinsi.required' => 'Provinsi wajib dipilih.',
            'kota.required' => 'Kota/Kabupaten wajib dipilih.',
            'kecamatan.required' => 'Kecamatan wajib dipilih.',
            'kelurahan.required' => 'Kelurahan/Desa wajib dipilih.',
            'kode_pos.required' => 'Kode pos wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'status_pernikahan.required' => 'Status pernikahan wajib dipilih.',
            'status_pernikahan.in' => 'Status pernikahan tidak valid.',
            'position_ids.required' => 'Minimal satu jabatan wajib dipilih.',
            'position_ids.min' => 'Minimal satu jabatan wajib dipilih.',
            'main_position_id.required' => 'Jabatan utama wajib ditentukan.',
            'division_ids.required' => 'Minimal satu divisi wajib dipilih.',
            'division_ids.min' => 'Minimal satu divisi wajib dipilih.',
            'division_ids.*.exists' => 'Divisi tidak ditemukan.',
            'atasan.required' => 'Atasan 1 wajib diisi.',
            'atasan.in' => 'Atasan 1 tidak valid.',
            'atasan2.in' => 'Atasan 2 tidak valid.',
            'tanggal_masuk.required' => 'Tanggal bergabung wajib diisi.',
            'tanggal_masuk.date' => 'Tanggal bergabung tidak valid.',
            'jenis_karyawan.required' => 'Jenis karyawan wajib dipilih.',
            'lokasi_kerja.required' => 'Lokasi kerja wajib dipilih.',
            'jenis_kerja.required' => 'Jenis kerja wajib dipilih.',
            'jam_kerja.required' => 'Jam kerja wajib dipilih.',
            'jobdesk.required' => 'Jobdesk wajib diisi.',
            'no_hp.required' => 'No. telepon wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'no_kontak_darurat1.required' => 'No. kontak darurat 1 wajib diisi.',
            'hubungan_darurat1.required' => 'Hubungan kontak darurat 1 wajib dipilih.',
            'no_kontak_darurat2.required' => 'No. kontak darurat 2 wajib diisi.',
            'hubungan_darurat2.required' => 'Hubungan kontak darurat 2 wajib dipilih.',
            'no_bpjs.required' => 'No. BPJS wajib diisi.',
            'status_bpjs.required' => 'Status BPJS wajib dipilih.',
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
        $this->authorizeWrite('create-data');
        $this->resetForm();
        $this->resetValidation();
        $this->step = 1;
        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->authorizeWrite('update-data');
        $emp = Employee::with('positions', 'divisions')->findOrFail($id);
        $this->editId = $emp->id;
        $this->nik = $emp->nik;
        $this->nik_ktp = $emp->nik_ktp ?? '';
        $this->nama = $emp->nama;
        $this->tempat_lahir = $emp->tempat_lahir ?? '';
        $this->tanggal_lahir = $emp->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->jenis_kelamin = $emp->jenis_kelamin ?? '';
        $this->ukuran_baju = $emp->ukuran_baju ?? '';
        $this->agama = $emp->agama ?? '';
        $this->pendidikan_terakhir = $emp->pendidikan_terakhir ?? '';
        $this->informasi_lowongan = $emp->informasi_lowongan ?? '';
        $this->alamat = $emp->alamat ?? '';
        $this->provinsi = $this->provinceIdForName($emp->provinsi ?? '');
        $this->kota = $this->regionIdForParentId($this->provinsi, Region::TYPE_KABUPATEN, $emp->kota ?? '');
        $this->kecamatan = $this->regionIdForParentId($this->kota, Region::TYPE_KECAMATAN, $emp->kecamatan ?? '');
        $this->kelurahan = $this->regionIdForParentId($this->kecamatan, Region::TYPE_KELURAHAN, $emp->kelurahan ?? '');
        $this->kode_pos = $emp->kode_pos ?? '';
        $this->loadRegionLists();
        $this->status = $emp->status;
        $this->status_pernikahan = $emp->status_pernikahan ?? '';
        $this->position = $emp->position ?? '';
        $this->position_ids = $emp->positions->pluck('id')->toArray();
        $mainPos = $emp->mainPosition();
        $this->main_position_id = (string) ($mainPos?->id ?? '');
        $this->division_ids = $emp->divisions->pluck('id')->toArray();
        $this->atasan = $emp->atasan ?? '';
        $this->atasan2 = $emp->atasan2 ?? '';
        $this->tanggal_masuk = $emp->tanggal_masuk?->format('Y-m-d') ?? '';
        $this->jenis_karyawan = $emp->jenis_karyawan ?? '';
        $this->lokasi_kerja = $emp->lokasi_kerja ?? '';
        $this->jenis_kerja = $emp->jenis_kerja ?? '';
        $this->jam_kerja = $emp->jam_kerja ?? '';
        $this->jam_masuk = $emp->jam_masuk ? substr($emp->jam_masuk, 0, 5) : '';
        $this->jam_kerja_effective = now()->toDateString();
        $this->jobdesk = $emp->jobdesk ?? '';
        if (! empty($this->position_ids)) {
            $this->syncJobdeskFromPositions();
        }
        $this->no_hp = $emp->no_hp ?? '';
        $this->email = $emp->email ?? '';
        $this->no_kontak_darurat1 = $emp->no_kontak_darurat1 ?? '';
        $this->hubungan_darurat1 = $emp->hubungan_darurat1 ?? '';
        $this->no_kontak_darurat2 = $emp->no_kontak_darurat2 ?? '';
        $this->hubungan_darurat2 = $emp->hubungan_darurat2 ?? '';
        $this->no_bpjs = $emp->no_bpjs ?? '';
        $this->status_bpjs = $emp->status_bpjs ?? '';
        $this->tanggal_resign = $emp->tanggal_resign?->format('Y-m-d') ?? '';
        $this->catatan = $emp->catatan ?? '';
        $this->step = 1;
        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showPreview = false;
        $this->editId = null;
        $this->step = 1;
        $this->resetValidation();
    }

    public function nextStep(): void
    {
        $this->validate($this->stepRules($this->step));
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
        $this->authorizeWrite('create-data');
        $rules = $this->rules();
        $rules['nik'] = ['required', 'string', 'max:30', 'unique:employees,nik'];
        $this->validate($rules);

        $employee = Employee::create($this->buildData());

        if ($employee->jam_kerja || $employee->jam_masuk) {
            $employee->setJamKerja(
                $employee->jam_kerja,
                $employee->jam_masuk,
                $employee->tanggal_masuk?->toDateString() ?? now()->toDateString()
            );
        }

        if (! empty($this->position_ids)) {
            $syncData = [];
            foreach ($this->position_ids as $pid) {
                $syncData[$pid] = ['is_main' => $pid == (int) $this->main_position_id];
            }
            $employee->positions()->sync($syncData);
        }

        $employee->divisions()->sync($this->division_ids);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Karyawan berhasil ditambahkan.');
    }

    public function update(): void
    {
        $this->authorizeWrite('update-data');
        $emp = Employee::findOrFail($this->editId);

        $rules = $this->rules();
        $rules['nik'] = ['required', 'string', 'max:30', 'unique:employees,nik,'.$this->editId];
        $this->validate($rules);

        $oldJamKerja = $emp->jam_kerja;
        $oldJamMasuk = $emp->jam_masuk;

        $emp->update($this->buildData());

        $newJamKerja = $this->jam_kerja ?: null;
        $newJamMasuk = $this->jam_masuk ? $this->jam_masuk.':00' : null;

        if ($newJamKerja !== $oldJamKerja || $newJamMasuk !== $oldJamMasuk) {
            $emp->setJamKerja($newJamKerja, $newJamMasuk, $this->jam_kerja_effective ?: now()->toDateString());
        }

        if (! empty($this->position_ids)) {
            $syncData = [];
            foreach ($this->position_ids as $pid) {
                $syncData[$pid] = ['is_main' => $pid == (int) $this->main_position_id];
            }
            $emp->positions()->sync($syncData);
        }

        $emp->divisions()->sync($this->division_ids);

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Data karyawan berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorizeWrite('delete-data');
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (! $this->deleteId) {
            return;
        }
        $this->authorizeWrite('delete-data');
        Employee::findOrFail($this->deleteId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Karyawan berhasil dihapus.');
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

    public function render()
    {
        // listSelect: tanpa kolom foto (base64 besar) agar memori aman.
        $employees = Employee::with('divisions')->listSelect()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nik', 'like', "%{$this->search}%")
                        ->orWhere('nama', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('no_hp', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterDivision, function ($query) {
                $query->whereHas('divisions', fn ($q) => $q->where('divisions.id', $this->filterDivision));
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->sortField === 'nik', function ($query) {
                $query->orderByRaw('CAST(nik AS UNSIGNED) '.($this->sortDirection === 'asc' ? 'asc' : 'desc'));
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
        if (! empty($this->position_ids)) {
            $positionNames = Position::whereIn('id', $this->position_ids)->pluck('nama')->toArray();
        }
        $posStr = ! empty($positionNames) ? implode(' & ', $positionNames) : ($this->position ?: null);

        return [
            'nik' => $this->nik,
            'nik_ktp' => $this->nik_ktp ?: null,
            'nama' => $this->nama,
            'email' => $this->email ?: null,
            'no_hp' => $this->no_hp ?: null,
            'alamat' => $this->alamat ?: null,
            'provinsi' => $this->provinsi ? ($this->provinceList[$this->provinsi] ?? null) : null,
            'kota' => $this->kota ? ($this->cityList[$this->kota] ?? null) : null,
            'kecamatan' => $this->kecamatan ? ($this->districtList[$this->kecamatan] ?? null) : null,
            'kelurahan' => $this->kelurahan ? ($this->villageList[$this->kelurahan] ?? null) : null,
            'kode_pos' => $this->kode_pos ?: null,
            'tempat_lahir' => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'jenis_kelamin' => $this->jenis_kelamin ?: null,
            'ukuran_baju' => $this->ukuran_baju ?: null,
            'agama' => $this->agama ?: null,
            'pendidikan_terakhir' => $this->pendidikan_terakhir ?: null,
            'informasi_lowongan' => $this->informasi_lowongan ?: null,
            'position' => $posStr,
            'atasan' => $this->atasan ?: null,
            'atasan2' => $this->atasan2 ?: null,
            'jenis_karyawan' => $this->jenis_karyawan ?: null,
            'lokasi_kerja' => $this->lokasi_kerja ?: null,
            'jenis_kerja' => $this->jenis_kerja ?: null,
            'jam_kerja' => $this->jam_kerja ?: null,
            'jam_masuk' => $this->jam_masuk ? $this->jam_masuk.':00' : null,
            'jobdesk' => $this->jobdesk ?: null,
            'no_kontak_darurat1' => $this->no_kontak_darurat1 ?: null,
            'hubungan_darurat1' => $this->hubungan_darurat1 ?: null,
            'no_kontak_darurat2' => $this->no_kontak_darurat2 ?: null,
            'hubungan_darurat2' => $this->hubungan_darurat2 ?: null,
            'no_bpjs' => $this->no_bpjs ?: null,
            'status_bpjs' => $this->status_bpjs ?: null,
            'status' => $this->status,
            'status_pernikahan' => $this->status_pernikahan ?: null,
            'tanggal_masuk' => $this->tanggal_masuk ?: null,
            'tanggal_resign' => $this->tanggal_resign ?: null,
            'catatan' => $this->catatan ?: null,
        ];
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->nik = '';
        $this->nik_ktp = '';
        $this->nama = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = '';
        $this->jenis_kelamin = '';
        $this->ukuran_baju = '';
        $this->agama = '';
        $this->pendidikan_terakhir = '';
        $this->informasi_lowongan = '';
        $this->alamat = '';
        $this->provinsi = '';
        $this->kota = '';
        $this->kecamatan = '';
        $this->kelurahan = '';
        $this->kode_pos = '';
        $this->cityList = [];
        $this->districtList = [];
        $this->villageList = [];
        $this->status = 'aktif';
        $this->status_pernikahan = '';
        $this->position = '';
        $this->position_ids = [];
        $this->main_position_id = '';
        $this->division_ids = [];
        $this->atasan = '';
        $this->atasan2 = '';
        $this->tanggal_masuk = '';
        $this->jenis_karyawan = '';
        $this->lokasi_kerja = '';
        $this->jenis_kerja = '';
        $this->jam_kerja = '';
        $this->jam_masuk = '';
        $this->jam_kerja_effective = '';
        $this->jobdesk = '';
        $this->no_hp = '';
        $this->email = '';
        $this->no_kontak_darurat1 = '';
        $this->hubungan_darurat1 = '';
        $this->no_kontak_darurat2 = '';
        $this->hubungan_darurat2 = '';
        $this->no_bpjs = '';
        $this->status_bpjs = '';
        $this->tanggal_resign = '';
        $this->catatan = '';
        $this->step = 1;
        $this->resetErrorBag();
    }
}
