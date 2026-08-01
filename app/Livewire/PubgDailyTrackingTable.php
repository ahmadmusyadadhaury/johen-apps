<?php

namespace App\Livewire;

use App\Models\BonusPubg;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PubgDailyTrackingTable extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $bulan = '';
    public string $divisi = '';

    private const DIVISI_PARAM_MAP = [
        'fc_mobile' => 'FC Mobile',
        'mlbb' => 'MLBB',
        'pubg' => 'PUBG',
        'ff' => 'Free Fire',
        'efootball' => 'E-football',
        'valorant' => 'Valorant',
        'roblox' => 'Roblox',
        'monkey_pubg' => 'Monkey PUBG',
    ];

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editId = null;
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public string $tanggal = '';
    public string $nik = '';
    public string $nama = '';
    public string $sesi = '';
    public string $ach_sold = '';
    public string $ach_view = '';
    public string $peak_view = '';
    public string $durasi = '';
    public string $insentif = '';
    public string $catatan = '';
    public $foto_bukti_stats;
    public $foto_bukti_live;
    public string $fotoBuktiStatsPath = '';
    public string $fotoBuktiLivePath = '';

    protected $updatesQueryString = ['search'];

    public function mount(): void
    {
        $this->divisi = request()->query('divisi', '');
    }

    protected function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'nik' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'sesi' => 'required|string|in:Pagi,Siang,Malam,Subuh',
            'ach_sold' => 'required|numeric|min:0',
            'ach_view' => 'required|numeric|min:0',
            'peak_view' => 'required|numeric|min:0',
            'durasi' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'foto_bukti_stats' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'foto_bukti_live' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ];
    }

    protected function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'sesi.required' => 'Sesi wajib diisi.',
            'ach_sold.required' => 'Sold wajib diisi.',
            'ach_view.required' => 'View wajib diisi.',
            'peak_view.required' => 'Peak View wajib diisi.',
            'durasi.required' => 'Durasi wajib diisi.',
            'durasi.numeric' => 'Durasi harus berupa angka.',
            'durasi.min' => 'Durasi minimal 0.',
            'foto_bukti_stats.image' => 'Bukti Stats harus berupa gambar.',
            'foto_bukti_stats.mimes' => 'Bukti Stats harus format JPG/PNG.',
            'foto_bukti_stats.max' => 'Ukuran Bukti Stats maksimal 10MB.',
            'foto_bukti_live.image' => 'Bukti Live harus berupa gambar.',
            'foto_bukti_live.mimes' => 'Bukti Live harus format JPG/PNG.',
            'foto_bukti_live.max' => 'Ukuran Bukti Live maksimal 10MB.',
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

    public function updatedAchSold(string $value): void
    {
        $value = str_replace(',', '.', $value);
        if (is_numeric($value) && $value > 0) {
            $this->insentif = (string) ($value * 100000);
        } else {
            $this->insentif = '0';
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
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        $item = BonusPubg::findOrFail($id);
        if (!$this->canModify($item)) return;
        $this->editId = $item->id;
        $this->tanggal = $item->tanggal->format('Y-m-d');
        $this->nik = $item->nik;
        $this->nama = $item->nama;
        $this->sesi = $item->sesi ?? '';
        $this->ach_sold = (string) $item->ach_sold;
        $this->ach_view = (string) $item->ach_view;
        $this->peak_view = (string) $item->peak_view;
        $this->durasi = (string) (int) $item->durasi;
        $this->insentif = (string) ($item->ach_sold * 100000);
        $this->catatan = $item->catatan ?? '';
        $this->fotoBuktiStatsPath = $item->foto_bukti_stats ?? '';
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
        $this->validate();

        $employee = Employee::where('nik', $this->nik)->first();
        if (!$employee) {
            $this->addError('nik', 'Karyawan dengan NIK tersebut tidak ditemukan.');
            return;
        }

        $sold = str_replace(',', '.', $this->ach_sold);

        $user = auth()->user();
        if ($this->isDivisiKoordinator() && !$user->isKoordinatorGame()) return;

        $status = $user->isKoordinatorGame() ? 'disetujui' : 'pending';

        $data = [
            'employee_id' => $employee->id,
            'tanggal' => $this->tanggal,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'sesi' => $this->sesi,
            'divisi' => $this->getDivisiName(),
            'ach_sold' => $sold,
            'ach_view' => str_replace(',', '.', $this->ach_view),
            'peak_view' => str_replace(',', '.', $this->peak_view),
            'durasi' => str_replace(',', '.', $this->durasi),
            'insentif' => $sold * 100000,
            'catatan' => $this->catatan ?: null,
            'status' => $status,
        ];

        if ($this->foto_bukti_stats) {
            $data['foto_bukti_stats'] = $this->foto_bukti_stats->store('daily-tracking', 'public');
        }
        if ($this->foto_bukti_live) {
            $data['foto_bukti_live'] = $this->foto_bukti_live->store('daily-tracking', 'public');
        }

        BonusPubg::create($data);

        $this->closeModal();
        $this->dispatch('daily-tracking-updated');
        $message = $status === 'pending'
            ? 'Data berhasil ditambahkan. Menunggu persetujuan koordinator.'
            : 'Data berhasil ditambahkan.';
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function update(): void
    {
        $this->validate();
        $sold = str_replace(',', '.', $this->ach_sold);
        $item = BonusPubg::findOrFail($this->editId);
        if (!$this->canModify($item)) return;

        $data = [
            'tanggal' => $this->tanggal,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'sesi' => $this->sesi,
            'ach_sold' => $sold,
            'ach_view' => str_replace(',', '.', $this->ach_view),
            'peak_view' => str_replace(',', '.', $this->peak_view),
            'durasi' => str_replace(',', '.', $this->durasi),
            'insentif' => $sold * 100000,
            'catatan' => $this->catatan ?: null,
        ];

        if ($this->foto_bukti_stats) {
            if ($item->foto_bukti_stats) {
                Storage::disk('public')->delete($item->foto_bukti_stats);
            }
            $data['foto_bukti_stats'] = $this->foto_bukti_stats->store('daily-tracking', 'public');
        }
        if ($this->foto_bukti_live) {
            if ($item->foto_bukti_live) {
                Storage::disk('public')->delete($item->foto_bukti_live);
            }
            $data['foto_bukti_live'] = $this->foto_bukti_live->store('daily-tracking', 'public');
        }

        $item->update($data);

        $this->closeModal();
        $this->dispatch('daily-tracking-updated');
        $this->dispatch('notify', type: 'success', message: 'Data berhasil diperbarui.');
    }

    private function canModify(BonusPubg $item): bool
    {
        $user = auth()->user();
        if ($this->isKoordinatorView()) return true;
        if ($item->status !== 'pending') return false;
        $employee = $user->employee;
        return $employee && $item->employee_id === $employee->id;
    }

    public function confirmDelete(int $id): void
    {
        $item = BonusPubg::findOrFail($id);
        if (!$this->canModify($item)) return;
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (!$this->deleteId) return;
        $item = BonusPubg::findOrFail($this->deleteId);
        if (!$this->canModify($item)) return;
        if ($item->foto_bukti_stats) {
            Storage::disk('public')->delete($item->foto_bukti_stats);
        }
        if ($item->foto_bukti_live) {
            Storage::disk('public')->delete($item->foto_bukti_live);
        }
        $item->delete();
        $this->dispatch('daily-tracking-updated');
        $this->dispatch('notify', type: 'success', message: 'Data berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function setujui(int $id): void
    {
        $user = auth()->user();
        if (!$this->isKoordinatorView()) return;

        $item = BonusPubg::findOrFail($id);
        if (!in_array($item->employee_id, $this->getViewEmployeeIds())) return;

        $item->update([
            'status' => 'disetujui',
            'approved_by' => $user->employee->id,
        ]);
        $this->dispatch('daily-tracking-updated');
        $this->dispatch('notify', type: 'success', message: 'Data berhasil disetujui.');
    }

    public function tolak(int $id): void
    {
        $user = auth()->user();
        if (!$this->isKoordinatorView()) return;

        $item = BonusPubg::findOrFail($id);
        if (!in_array($item->employee_id, $this->getViewEmployeeIds())) return;

        $item->update(['status' => 'ditolak']);
        $this->dispatch('daily-tracking-updated');
        $this->dispatch('notify', type: 'success', message: 'Data ditolak.');
    }

    public function getDivisiName(): string
    {
        if ($this->divisi !== '') {
            if (isset(self::DIVISI_PARAM_MAP[$this->divisi])) {
                return self::DIVISI_PARAM_MAP[$this->divisi];
            }
            if (in_array($this->divisi, self::DIVISI_PARAM_MAP, true)) {
                return $this->divisi;
            }
        }

        $user = auth()->user();

        return match (true) {
            $user->isKoordinatorMlbb(), $user->isStaffHostMlbb() => 'MLBB',
            $user->isKoordinatorEfootball(), $user->isStaffHostEfootball() => 'E-football',
            $user->isKoordinatorValorant(), $user->isStaffHostValorant() => 'Valorant',
            $user->isKoordinatorRoblox(), $user->isStaffHostRoblox() => 'Roblox',
             $user->isKoordinatorMonkeyPubg(), $user->isStaffHostMonkeyPubg() => 'Monkey PUBG',
            $user->isKoordinatorFf(), $user->isStaffHostFf() => 'Free Fire',
            $user->isKoordinatorPubg(), $user->isStaffHostPubg() => 'PUBG',
            default => 'PUBG',
        };
    }

    private function getDivisionRootPosition(): ?Position
    {
        $map = [
            'FC Mobile' => 'Koordinator FC Mobile',
            'MLBB' => 'Koordinator MLBB',
            'PUBG' => 'Koordinator Johen PUBG',
            'Free Fire' => 'Koordinator Free Fire',
            'E-football' => 'Koordinator E-football',
            'Valorant' => 'Koordinator Valorant',
            'Roblox' => 'Koordinator Roblox',
            'Monkey PUBG' => 'Koordinator Monkey PUBG',
        ];

        $divisi = $this->getDivisiName();
        if (!isset($map[$divisi])) return null;

        return Position::where('nama', $map[$divisi])->first();
    }

    private function getDivisionEmployeeIds(Position $root): array
    {
        $positionIds = $this->getAllDescendantIds($root);
        $positionIds[] = $root->id;

        return Employee::whereHas('positions', function ($q) use ($positionIds) {
            $q->whereIn('position_id', $positionIds);
        })->pluck('id')->toArray();
    }

    public function isDivisiKoordinator(): bool
    {
        $root = $this->getDivisionRootPosition();
        if (!$root) return false;

        $employee = auth()->user()->employee;
        if (!$employee) return false;

        return $employee->positions()->where('position_id', $root->id)->exists();
    }

    private function isKoordinatorView(): bool
    {
        return auth()->user()->isKoordinatorGame() || $this->isDivisiKoordinator();
    }

    private function getViewEmployeeIds(): array
    {
        $employee = auth()->user()->employee;
        if (!$employee) return [];

        if ($this->isDivisiKoordinator()) {
            return $this->getDivisionEmployeeIds($this->getDivisionRootPosition());
        }

        $user = auth()->user();
        if ($user->isKoordinatorGame()) {
            $ids = [$employee->id];
            $subordinateIds = $this->getSubordinateEmployeeIds();
            return array_values(array_unique(array_merge($ids, $subordinateIds)));
        }

        return [$employee->id];
    }

    public function render()
    {
        $user = auth()->user();
        $userEmployee = $user->employee;

        $divisi = $this->getDivisiName();

        $query = BonusPubg::query();

        $viewEmployeeIds = $this->getViewEmployeeIds();
        $query->whereIn('bonus_pubgs.employee_id', $viewEmployeeIds);

        $query->where('bonus_pubgs.divisi', $divisi);

        $items = $query->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nik', 'like', "%{$this->search}%");
            });
        })
        ->when($this->bulan, function ($query) {
            $query->whereYear('tanggal', substr($this->bulan, 0, 4))
                  ->whereMonth('tanggal', substr($this->bulan, 5, 2));
        })
        ->latest('tanggal')
        ->paginate(10);

        $groupedItems = $items->getCollection()->groupBy(function ($item) {
            return $item->tanggal->format('Y-m-d');
        });

        $divisionRoot = $this->divisi !== '' ? $this->getDivisionRootPosition() : null;

        $employees = Employee::when($divisionRoot, function ($q) use ($divisionRoot) {
            $q->whereIn('id', $this->getDivisionEmployeeIds($divisionRoot));
        })->when(!$divisionRoot && $user->isKoordinatorGame(), function ($q) use ($userEmployee) {
            $subordinateIds = $this->getSubordinateEmployeeIds();
            $ids = $userEmployee ? [$userEmployee->id] : [];
            if (!empty($subordinateIds)) {
                $ids = array_merge($ids, $subordinateIds);
            }
            $q->whereIn('id', $ids);
        })->when(!$divisionRoot && !$user->isKoordinatorGame() && !$user->isManager() && $userEmployee, function ($q) use ($user) {
            $roleMap = [
                'isStaffHostPubg' => User::ROLE_STAFF_HOST_PUBG,
                'isStaffHostFf' => User::ROLE_STAFF_HOST_FF,
                'isStaffHostMlbb' => User::ROLE_STAFF_HOST_MLBB,
                'isStaffHostEfootball' => User::ROLE_STAFF_HOST_EFOOTBALL,
                'isStaffHostValorant' => User::ROLE_STAFF_HOST_VALORANT,
                'isStaffHostRoblox' => User::ROLE_STAFF_HOST_ROBLOX,
                'isStaffHostMonkeyPubg' => User::ROLE_STAFF_HOST_MONKEY_PUBG,
            ];
            foreach ($roleMap as $method => $role) {
                if ($user->$method()) {
                    $q->whereHas('users', fn ($uq) => $uq->where('role', $role));
                    break;
                }
            }
        })->orderBy('nama')->get();

        $totalSold = 0;
        $totalView = 0;
        $totalPeak = 0;
        $totalDurasi = 0;
        $soldBreakdown = collect();
        $viewBreakdown = collect();
        $peakBreakdown = collect();
        $durasiBreakdown = collect();
        if (($user->isStaffHostPubg() || $user->isStaffHostFf() || $user->isStaffHostMlbb() || $user->isStaffHostEfootball() || $user->isStaffHostValorant() || $user->isStaffHostRoblox() || $user->isStaffHostMonkeyPubg() || $user->isKoordinatorGame()) && $userEmployee) {
            $statsQuery = BonusPubg::query();
            $statsQuery->whereIn('employee_id', $this->getViewEmployeeIds());
            $statsQuery->where('divisi', $divisi);

            $totalSold = (clone $statsQuery)->sum('ach_sold');
            $totalView = (clone $statsQuery)->sum('ach_view');
            $totalPeak = (clone $statsQuery)->sum('peak_view');
            $totalDurasi = (clone $statsQuery)->sum('durasi');

            $soldBreakdown = (clone $statsQuery)
                ->selectRaw('nama, SUM(ach_sold) as total')
                ->groupBy('nama')->orderByDesc('total')->get();
            $viewBreakdown = (clone $statsQuery)
                ->selectRaw('nama, SUM(ach_view) as total')
                ->groupBy('nama')->orderByDesc('total')->get();
            $peakBreakdown = (clone $statsQuery)
                ->selectRaw('nama, SUM(peak_view) as total')
                ->groupBy('nama')->orderByDesc('total')->get();
            $durasiBreakdown = (clone $statsQuery)
                ->selectRaw('nama, SUM(durasi) as total')
                ->groupBy('nama')->orderByDesc('total')->get();
        }

        return view('livewire.pubg-daily-tracking-table', compact('items', 'groupedItems', 'employees', 'totalSold', 'totalView', 'totalPeak', 'totalDurasi', 'soldBreakdown', 'viewBreakdown', 'peakBreakdown', 'durasiBreakdown', 'divisi'));
    }

    private function getSubordinateEmployeeIds(): array
    {
        $employee = auth()->user()->employee;
        if (!$employee) return [];

        $ids = [];

        $mainPosition = $employee->mainPosition();
        if ($mainPosition) {
            $descendantIds = $this->getAllDescendantIds($mainPosition);
            if (!empty($descendantIds)) {
                $ids = Employee::whereHas('positions', function ($q) use ($descendantIds) {
                    $q->whereIn('position_id', $descendantIds)
                      ->where('is_main', true);
                })->pluck('id')->toArray();
            }
        }

        $user = auth()->user();
        $roleMap = [
            'isKoordinatorFf' => User::ROLE_STAFF_HOST_FF,
            'isKoordinatorPubg' => User::ROLE_STAFF_HOST_PUBG,
            'isKoordinatorMlbb' => User::ROLE_STAFF_HOST_MLBB,
            'isKoordinatorEfootball' => User::ROLE_STAFF_HOST_EFOOTBALL,
            'isKoordinatorValorant' => User::ROLE_STAFF_HOST_VALORANT,
            'isKoordinatorRoblox' => User::ROLE_STAFF_HOST_ROBLOX,
            'isKoordinatorMonkeyPubg' => User::ROLE_STAFF_HOST_MONKEY_PUBG,
        ];

        foreach ($roleMap as $method => $staffRole) {
            if ($user->$method()) {
                $roleIds = Employee::whereHas('users', function ($q) use ($staffRole) {
                    $q->where('role', $staffRole);
                })->pluck('id')->toArray();
                $ids = array_merge($ids, $roleIds);
                break;
            }
        }

        return array_values(array_unique($ids));
    }

    private function getAllDescendantIds(Position $position): array
    {
        $ids = [];
        $children = Position::where('parent_id', $position->id)->get();
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllDescendantIds($child));
        }
        return $ids;
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->nik = '';
        $this->nama = '';
        $this->sesi = '';
        $this->ach_sold = '';
        $this->ach_view = '';
        $this->peak_view = '';
        $this->durasi = '';
        $this->insentif = '';
        $this->catatan = '';
        $this->foto_bukti_stats = null;
        $this->foto_bukti_live = null;
        $this->fotoBuktiStatsPath = '';
        $this->fotoBuktiLivePath = '';
        $this->resetErrorBag();
    }
}
