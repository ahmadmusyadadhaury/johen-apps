<?php

namespace App\Livewire;

use App\Models\BonusPubg;
use App\Models\Employee;
use App\Models\Position;
use Livewire\Component;
use Livewire\WithPagination;

class ManagerDailyTrackingGame extends Component
{
    use WithPagination;

    public string $divisi = '';
    public string $search = '';
    public string $tanggal = '';
    public string $nama = '';
    public bool $showSuccess = false;

    protected $updatesQueryString = ['search'];

    public function mount(string $divisi = ''): void
    {
        $this->divisi = $divisi ?: (string) request()->query('divisi', '');
        $this->tanggal = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTanggal(): void
    {
        $this->resetPage();
    }

    public function updatedTanggal(): void
    {
        if ($this->tanggal === '') {
            $this->tanggal = now()->toDateString();
        }
    }

    public function updatingNama(): void
    {
        $this->resetPage();
    }

    private function getSubordinateIds(Employee $employee): array
    {
        $position = $employee->mainPosition();
        if (!$position) return [];

        $descendantIds = $this->getDescendantIds($position->id);
        $descendantIds = array_diff($descendantIds, [$position->id]);

        if (empty($descendantIds)) return [];

        return Employee::whereIn('id', function ($q) use ($descendantIds) {
            $q->select('employee_id')
              ->from('employee_position')
              ->whereIn('position_id', $descendantIds);
        })->pluck('id')->toArray();
    }

    private function getDescendantIds(int $positionId): array
    {
        $ids = [$positionId];
        $children = Position::where('parent_id', $positionId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getDescendantIds($childId));
        }
        return $ids;
    }

    private function getEfootballEmployeeIds(): array
    {
        $efootball = Position::where('nama', 'Koordinator E-football')->first();
        if (!$efootball) return [];

        $positionIds = $this->getDescendantIds($efootball->id);

        return Employee::whereHas('positions', function ($q) use ($positionIds) {
            $q->whereIn('position_id', $positionIds);
        })->pluck('id')->toArray();
    }

    private function isHeadOfStore2(Employee $employee): bool
    {
        $position = $employee->mainPosition();
        if (!$position) return false;

        return str_contains(strtolower($position->nama), 'head of store 2');
    }

    public function saveFeedback($id, $feedback): void
    {
        $user = auth()->user();
        if (!$user || !$user->isManager()) return;

        BonusPubg::where('id', $id)->update(['feedback_atasan' => $feedback]);
        $this->dispatch('daily-tracking-updated');
        $this->showSuccess = true;
    }

    public function render()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $empty = [
            'items' => collect(),
            'groupedItems' => collect(),
            'totalSold' => 0,
            'totalView' => 0,
            'totalPeak' => 0,
            'totalDurasi' => 0,
            'tanggal' => $this->tanggal,
            'namaOptions' => collect(),
            'divisi' => $this->divisi,
        ];

        if (!$employee || !$user->isManager() || $this->divisi === '') {
            return view('livewire.manager-daily-tracking-game', $empty);
        }

        $subordinateIds = $this->getSubordinateIds($employee);
        if (empty($subordinateIds)) {
            return view('livewire.manager-daily-tracking-game', $empty);
        }

        if ($this->isHeadOfStore2($employee)) {
            $efootballIds = $this->getEfootballEmployeeIds();
            if (!empty($efootballIds)) {
                $subordinateIds = array_diff($subordinateIds, $efootballIds);
                if (empty($subordinateIds)) {
                    return view('livewire.manager-daily-tracking-game', $empty);
                }
            }
        }

        $query = BonusPubg::whereIn('bonus_pubgs.employee_id', $subordinateIds)
            ->where('bonus_pubgs.status', 'disetujui')
            ->where('bonus_pubgs.divisi', $this->divisi)
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('bonus_pubgs.nama', 'like', "%{$this->search}%")
                      ->orWhere('bonus_pubgs.nik', 'like', "%{$this->search}%");
                });
            })
            ->when($this->tanggal, function ($q) {
                $q->whereDate('bonus_pubgs.tanggal', $this->tanggal);
            })
            ->when($this->nama, function ($q) {
                $q->where('bonus_pubgs.nama', $this->nama);
            })
            ->with('employee.divisions', 'employee.users');

        $orderRaw = "CASE
            WHEN users.role IN ('staff_host_pubg', 'koordinator_pubg') THEN 1
            WHEN users.role IN ('staff_host_ff', 'koordinator_ff') THEN 2
            WHEN users.role IN ('staff_host_mlbb', 'koordinator_mlbb') THEN 3
            WHEN users.role IN ('staff_host_efootball', 'koordinator_efootball') THEN 4
            WHEN users.role IN ('staff_host_valorant', 'koordinator_valorant') THEN 5
            WHEN users.role IN ('staff_host_roblox', 'koordinator_roblox') THEN 6
            WHEN users.role IN ('staff_host_monkey_pubg', 'koordinator_monkey_pubg') THEN 7
            WHEN users.role IN ('staff_admin', 'koordinator_admin') THEN 8
            ELSE 8
        END";

        $items = (clone $query)
            ->join('employees', 'bonus_pubgs.employee_id', '=', 'employees.id')
            ->join('users', 'employees.id', '=', 'users.employee_id')
            ->select('bonus_pubgs.*')
            ->orderByRaw($orderRaw)
            ->latest('bonus_pubgs.tanggal')
            ->paginate(20);

        $groupedItems = $items->getCollection()->groupBy(function ($item) {
            return $item->tanggal->format('Y-m-d');
        });

        $namaOptions = BonusPubg::whereIn('bonus_pubgs.employee_id', $subordinateIds)
            ->where('bonus_pubgs.status', 'disetujui')
            ->where('bonus_pubgs.divisi', $this->divisi)
            ->when($this->tanggal, function ($q) {
                $q->whereDate('bonus_pubgs.tanggal', $this->tanggal);
            })
            ->distinct()
            ->pluck('bonus_pubgs.nama')
            ->sort()
            ->values();

        $allItems = (clone $query)->get();
        $totalSold = $allItems->sum('ach_sold');
        $totalView = $allItems->sum('ach_view');
        $totalPeak = $allItems->sum('peak_view');
        $totalDurasi = $allItems->sum('durasi');

        return view('livewire.manager-daily-tracking-game', [
            'items' => $items,
            'groupedItems' => $groupedItems,
            'totalSold' => $totalSold,
            'totalView' => $totalView,
            'totalPeak' => $totalPeak,
            'totalDurasi' => $totalDurasi,
            'tanggal' => $this->tanggal,
            'namaOptions' => $namaOptions,
            'divisi' => $this->divisi,
        ]);
    }
}