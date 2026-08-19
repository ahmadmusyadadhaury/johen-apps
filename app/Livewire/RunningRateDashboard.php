<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\RunningRateDailySold;
use App\Models\RunningRateHostTarget;
use App\Models\RunningRatePeriod;
use App\Services\RunningRateService;
use App\Services\RunningRateStatus;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class RunningRateDashboard extends Component
{
    use WithPagination;

    public const DIVISIONS = [
        'Free Fire',
        'PUBG',
        'MLBB',
        'E-football',
        'Valorant',
        'Roblox',
        'Monkey PUBG',
        'FC Mobile',
    ];

    public string $divisi = '';
    public ?int $periodId = null;
    public string $hostFilter = '';
    public string $search = '';
    public string $tanggalFilter = '';
    public array $dailySold = [];

    public bool $showSoldModal = false;
    public ?int $editSoldId = null;
    public string $soldTanggal = '';
    public string $soldHost = '';
    public string $soldValue = '';

    public bool $showHistoryModal = false;

    public bool $showSetupModal = false;
    public string $setupNama = '';
    public string $setupMulai = '';
    public string $setupSelesai = '';
    public array $setupTargets = [];

    public bool $showDeleteConfirm = false;
    public ?int $deleteSoldId = null;

    public bool $showTargetModal = false;
    public ?int $editTargetHostId = null;
    public string $targetValue = '';

    public bool $showDeleteHostConfirm = false;
    public ?int $deleteHostId = null;

    private RunningRateService $service;

    public function boot(): void
    {
        $this->service = app(RunningRateService::class);
    }

    public function mount(): void
    {
        $user = auth()->user();
        $ownDivision = $user->getRoleDivisionName();

        $queryDivisi = request()->query('divisi', '');
        if (in_array($queryDivisi, self::DIVISIONS, true)) {
            $this->divisi = $queryDivisi;
        } else {
            $this->divisi = $ownDivision ?: 'Free Fire';
        }

        if (! in_array($this->divisi, self::DIVISIONS, true)) {
            $this->divisi = 'Free Fire';
        }

        $this->tanggalFilter = now()->toDateString();
        $this->resetPeriod();
        $this->loadDailySold();
    }

    public function resetPeriod(): void
    {
        $period = RunningRatePeriod::where('divisi', $this->divisi)
            ->orderByDesc('is_active')
            ->orderByDesc('tanggal_selesai')
            ->first();

        $this->periodId = $period?->id;
    }

    public function updatedTanggalFilter(): void
    {
        $this->resetPage();
        $this->loadDailySold();
    }

    public function updatedPeriodId(): void
    {
        $this->resetPage();
    }

    public function updatedHostFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    private function loadDailySold(): void
    {
        $period = $this->currentPeriod();
        if (! $period || $this->tanggalFilter === '') {
            $this->dailySold = [];

            return;
        }

        $existing = $period->dailySolds()
            ->whereDate('tanggal', $this->tanggalFilter)
            ->get()
            ->keyBy('host_id');

        $map = [];
        foreach ($period->targets as $target) {
            $row = $existing->get($target->host_id);
            $map[(string) $target->host_id] = $row ? (float) $row->sold : 0.0;
        }

        $this->dailySold = $map;
    }

    public function openSoldModal(): void
    {
        abort_unless($this->canManageSold(), 403);

        $period = $this->currentPeriod();
        if (! $period) {
            $this->dispatch('notify', type: 'warning', message: 'Buat periode Running Rate terlebih dahulu.');
            return;
        }

        $this->resetSoldForm();
        $this->soldTanggal = $this->tanggalFilter ?: now()->toDateString();
        $this->showSoldModal = true;
    }

    public function openEditSoldModal(int $id): void
    {
        abort_unless($this->canManageSold(), 403);

        $item = RunningRateDailySold::findOrFail($id);
        $this->editSoldId = $item->id;
        $this->soldTanggal = $item->tanggal->toDateString();
        $this->soldHost = (string) $item->host_id;
        $this->soldValue = (string) (float) $item->sold;
        $this->showSoldModal = true;
    }

    public function closeSoldModal(): void
    {
        $this->showSoldModal = false;
        $this->resetSoldForm();
        $this->resetValidation();
    }

    private function resetSoldForm(): void
    {
        $this->editSoldId = null;
        $this->soldTanggal = '';
        $this->soldHost = '';
        $this->soldValue = '';
        $this->resetErrorBag();
    }

    public function saveSold(): void
    {
        abort_unless($this->canManageSold(), 403);

        $period = $this->currentPeriod();
        if (! $period) {
            $this->addError('soldTanggal', 'Periode Running Rate belum tersedia.');
            return;
        }

        $this->validateSold($period);

        $hostId = (int) $this->soldHost;
        $tanggal = $this->soldTanggal;
        $sold = (float) str_replace(',', '.', $this->soldValue);

        if (! $period->targets()->where('host_id', $hostId)->exists()) {
            $this->addError('soldHost', 'Host tersebut belum memiliki target pada periode ini.');
            return;
        }

        $existing = RunningRateDailySold::where('period_id', $period->id)
            ->where('host_id', $hostId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if ($existing) {
            $existing->update([
                'sold' => $sold,
                'input_by' => auth()->id(),
            ]);
            $message = 'Sold ' . $existing->host?->nama . ' untuk tanggal tersebut sudah ada, data diperbarui.';
        } else {
            RunningRateDailySold::create([
                'period_id' => $period->id,
                'host_id' => $hostId,
                'tanggal' => $tanggal,
                'sold' => $sold,
                'input_by' => auth()->id(),
            ]);
            $message = 'Sold berhasil diinput.';
        }

        $this->loadDailySold();
        $this->closeSoldModal();
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function updateSold(): void
    {
        abort_unless($this->canManageSold(), 403);

        $period = $this->currentPeriod();
        if (! $period) {
            $this->addError('soldTanggal', 'Periode Running Rate belum tersedia.');
            return;
        }

        $this->validateSold($period);

        $item = RunningRateDailySold::findOrFail($this->editSoldId);
        $item->update([
            'tanggal' => $this->soldTanggal,
            'host_id' => (int) $this->soldHost,
            'sold' => (float) str_replace(',', '.', $this->soldValue),
            'input_by' => auth()->id(),
        ]);

        $this->loadDailySold();
        $this->closeSoldModal();
        $this->dispatch('notify', type: 'success', message: 'Sold berhasil dikoreksi.');
    }

    private function validateSold(RunningRatePeriod $period): void
    {
        $this->validate([
            'soldTanggal' => 'required|date|after_or_equal:' . $period->tanggal_mulai->toDateString() . '|before_or_equal:' . $period->tanggal_selesai->toDateString(),
            'soldHost' => 'required|integer',
            'soldValue' => 'required|numeric|min:0',
        ], [
            'soldTanggal.required' => 'Tanggal wajib diisi.',
            'soldTanggal.date' => 'Tanggal tidak valid.',
            'soldTanggal.after_or_equal' => 'Tanggal harus berada dalam periode Running Rate.',
            'soldTanggal.before_or_equal' => 'Tanggal harus berada dalam periode Running Rate.',
            'soldHost.required' => 'Host wajib dipilih.',
            'soldValue.required' => 'Sold wajib diisi.',
            'soldValue.numeric' => 'Sold harus berupa angka.',
            'soldValue.min' => 'Sold tidak boleh negatif.',
        ]);
    }

    public function confirmDeleteSold(int $id): void
    {
        abort_unless($this->canManageSold(), 403);

        $item = RunningRateDailySold::findOrFail($id);
        if ($item->period_id !== $this->currentPeriod()?->id) {
            return;
        }

        $this->deleteSoldId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDeleteSold(): void
    {
        abort_unless($this->canManageSold(), 403);

        if (! $this->deleteSoldId) {
            return;
        }

        RunningRateDailySold::whereKey($this->deleteSoldId)->delete();

        $this->showDeleteConfirm = false;
        $this->deleteSoldId = null;
        $this->loadDailySold();
        $this->dispatch('notify', type: 'success', message: 'Riwayat Sold dihapus.');
    }

    public function cancelDeleteSold(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteSoldId = null;
    }

    public function openEditTargetModal(int $hostId): void
    {
        abort_unless($this->canManageSold(), 403);

        $period = $this->currentPeriod();
        if (! $period) {
            return;
        }

        $this->editTargetHostId = $hostId;
        $this->targetValue = (string) (float) $period->targets()->where('host_id', $hostId)->value('target');
        $this->soldValue = (string) (float) $period->dailySolds()
            ->where('host_id', $hostId)
            ->whereDate('tanggal', $this->tanggalFilter ?: now()->toDateString())
            ->value('sold');
        $this->showTargetModal = true;
    }

    public function closeTargetModal(): void
    {
        $this->showTargetModal = false;
        $this->editTargetHostId = null;
        $this->targetValue = '';
        $this->soldValue = '';
        $this->resetValidation();
    }

    public function saveTarget(): void
    {
        abort_unless($this->canManageSold(), 403);

        $period = $this->currentPeriod();
        if (! $period || ! $this->editTargetHostId) {
            return;
        }

        $this->validate([
            'targetValue' => 'required|numeric|min:0',
            'soldValue' => 'required|numeric|min:0',
        ], [
            'targetValue.required' => 'Target wajib diisi.',
            'targetValue.numeric' => 'Target harus berupa angka.',
            'targetValue.min' => 'Target tidak boleh negatif.',
            'soldValue.required' => 'Sold wajib diisi.',
            'soldValue.numeric' => 'Sold harus berupa angka.',
            'soldValue.min' => 'Sold tidak boleh negatif.',
        ]);

        $hostId = (int) $this->editTargetHostId;

        RunningRateHostTarget::updateOrCreate(
            ['period_id' => $period->id, 'host_id' => $hostId],
            ['target' => (float) str_replace(',', '.', $this->targetValue)],
        );

        RunningRateDailySold::updateOrCreate(
            ['period_id' => $period->id, 'host_id' => $hostId, 'tanggal' => $this->tanggalFilter ?: now()->toDateString()],
            ['sold' => (float) str_replace(',', '.', $this->soldValue), 'input_by' => auth()->id()],
        );

        $this->loadDailySold();
        $this->closeTargetModal();
        $this->dispatch('notify', type: 'success', message: 'Target dan Sold host berhasil diperbarui.');
    }

    public function confirmDeleteHost(int $hostId): void
    {
        abort_unless($this->canManageSold(), 403);

        $this->deleteHostId = $hostId;
        $this->showDeleteHostConfirm = true;
    }

    public function cancelDeleteHost(): void
    {
        $this->showDeleteHostConfirm = false;
        $this->deleteHostId = null;
    }

    public function executeDeleteHost(): void
    {
        abort_unless($this->canManageSold(), 403);

        $period = $this->currentPeriod();
        if (! $period || ! $this->deleteHostId) {
            return;
        }

        $hostId = (int) $this->deleteHostId;

        RunningRateDailySold::where('period_id', $period->id)->where('host_id', $hostId)->delete();
        RunningRateHostTarget::where('period_id', $period->id)->where('host_id', $hostId)->delete();

        $this->showDeleteHostConfirm = false;
        $this->deleteHostId = null;
        $this->loadDailySold();
        $this->dispatch('notify', type: 'success', message: 'Data host pada periode ini dihapus.');
    }

    public function openHistoryModal(): void
    {
        $this->showHistoryModal = true;
        $this->resetPage();
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
    }

    public function openSetupModal(): void
    {
        abort_unless($this->canManageSold(), 403);

        $hosts = $this->divisionHosts();

        $this->setupNama = Carbon::now()->translatedFormat('F Y');
        $this->setupMulai = Carbon::now()->startOfMonth()->toDateString();
        $this->setupSelesai = Carbon::now()->endOfMonth()->toDateString();
        $this->setupTargets = $hosts->mapWithKeys(fn (Employee $host) => [(string) $host->id => '0'])->all();

        $this->showSetupModal = true;
    }

    public function closeSetupModal(): void
    {
        $this->showSetupModal = false;
        $this->setupNama = '';
        $this->setupMulai = '';
        $this->setupSelesai = '';
        $this->setupTargets = [];
        $this->resetValidation();
    }

    public function saveSetup(): void
    {
        abort_unless($this->canManageSold(), 403);

        $this->validate([
            'setupNama' => 'required|string|max:255',
            'setupMulai' => 'required|date',
            'setupSelesai' => 'required|date|after_or_equal:setupMulai',
            'setupTargets' => 'array|min:1',
            'setupTargets.*' => 'required|numeric|min:0',
        ], [
            'setupNama.required' => 'Nama periode wajib diisi.',
            'setupMulai.required' => 'Tanggal mulai wajib diisi.',
            'setupSelesai.required' => 'Tanggal selesai wajib diisi.',
            'setupSelesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'setupTargets.min' => 'Minimal satu host harus memiliki target.',
            'setupTargets.*.required' => 'Target host wajib diisi.',
            'setupTargets.*.numeric' => 'Target harus berupa angka.',
            'setupTargets.*.min' => 'Target tidak boleh negatif.',
        ]);

        $activePeriod = RunningRatePeriod::where('divisi', $this->divisi)->where('is_active', true)->first();
        if ($activePeriod) {
            $activePeriod->update(['is_active' => false]);
        }

        $period = RunningRatePeriod::create([
            'divisi' => $this->divisi,
            'nama' => $this->setupNama,
            'tanggal_mulai' => $this->setupMulai,
            'tanggal_selesai' => $this->setupSelesai,
            'is_active' => true,
        ]);

        foreach ($this->setupTargets as $hostId => $target) {
            if ((float) $target > 0) {
                $period->targets()->create([
                    'host_id' => (int) $hostId,
                    'target' => (float) str_replace(',', '.', $target),
                ]);
            }
        }

        $this->periodId = $period->id;
        $this->loadDailySold();
        $this->closeSetupModal();
        $this->dispatch('notify', type: 'success', message: 'Periode Running Rate berhasil dibuat.');
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($this->canView(), 403);

        $periods = RunningRatePeriod::where('divisi', $this->divisi)
            ->orderByDesc('is_active')
            ->orderByDesc('tanggal_selesai')
            ->get();

        $period = $this->currentPeriod();
        $hosts = $this->divisionHosts();
        $canManage = $this->canManageSold();

        $rows = collect();
        $summary = [
            'total_target' => 0,
            'total_sold' => 0,
            'achievement' => 0,
            'remaining' => 0,
            'remaining_working_days' => 0,
            'rr_daily' => 0,
            'rr_weekly' => 0,
        ];
        $chart = collect();
        $history = collect();

        if ($period) {
            $targetRows = $period->targets()->with('host')->get();

            $targetRows = $targetRows->filter(function ($target) {
                if ($this->hostFilter !== '') {
                    return (string) $target->host_id === $this->hostFilter;
                }

                return true;
            });

            if ($this->search !== '') {
                $targetRows = $targetRows->filter(fn ($target) => str_contains(strtolower($target->host?->nama ?? ''), strtolower($this->search)));
            }

            $asOf = $this->tanggalFilter !== '' ? Carbon::parse($this->tanggalFilter) : null;

            $rows = $targetRows->map(function ($target) use ($asOf) {
                $metrics = $this->service->hostMetrics($this->currentPeriod(), $target->host_id, $asOf);

                return [
                    ...$metrics,
                    'nama' => $target->host?->nama ?? '—',
                    'nik' => $target->host?->nik ?? '—',
                ];
            })->values();

            $summary = $this->service->summary(
                $period,
                $this->hostFilter !== '' ? [(int) $this->hostFilter] : null,
                $asOf,
            );

            $chart = $this->service->soldByDay(
                $period,
                $this->hostFilter !== '' ? [(int) $this->hostFilter] : null,
            );

            $history = $period->dailySolds()
                ->with(['host', 'inputBy'])
                ->when($this->hostFilter !== '', fn ($q) => $q->where('host_id', (int) $this->hostFilter))
                ->latest('tanggal')
                ->latest('id')
                ->paginate(10);
        }

        $recentActivities = $period
            ? $period->dailySolds()->with(['host', 'inputBy'])->latest()->limit(6)->get()
            : collect();

        $statusDisplay = [];
        foreach ($rows as $row) {
            $statusDisplay[$row['host_id']] = RunningRateStatus::display($row['status']);
        }

        $divisionHostOptions = $hosts->filter(function ($host) use ($period) {
            if (! $period) {
                return true;
            }

            return $period->targets()->where('host_id', $host->id)->exists();
        });

        $hostMap = $hosts->keyBy('id');

        return view('livewire.running-rate-dashboard', [
            'periods' => $periods,
            'period' => $period,
            'hosts' => $hosts,
            'divisionHostOptions' => $divisionHostOptions,
            'rows' => $rows,
            'summary' => $summary,
            'chart' => $chart,
            'history' => $history,
            'recentActivities' => $recentActivities,
            'dailySold' => $this->dailySold,
            'canManage' => $canManage,
            'statusDisplay' => $statusDisplay,
            'hostMap' => $hostMap,
            'display' => fn (string $status) => RunningRateStatus::display($status),
        ]);
    }

    private function currentPeriod(): ?RunningRatePeriod
    {
        if (! $this->periodId) {
            return null;
        }

        return RunningRatePeriod::where('id', $this->periodId)
            ->where('divisi', $this->divisi)
            ->first();
    }

    private function divisionHosts(): \Illuminate\Database\Eloquent\Collection
    {
        return RunningRateService::hostsForDivision($this->divisi);
    }

    private function canView(): bool
    {
        $user = auth()->user();

        if ($user->canViewAll()) {
            return true;
        }

        if ($user->isKoordinatorGame()) {
            return $user->getRoleDivisionName() === $this->divisi;
        }

        if ($user->isKoordinatorFcMobile() && $this->divisi === 'FC Mobile') {
            return true;
        }

        $staffMap = [
            'isStaffHostFf' => 'Free Fire',
            'isStaffHostPubg' => 'PUBG',
            'isStaffHostMlbb' => 'MLBB',
            'isStaffHostEfootball' => 'E-football',
            'isStaffHostValorant' => 'Valorant',
            'isStaffHostRoblox' => 'Roblox',
            'isStaffHostMonkeyPubg' => 'Monkey PUBG',
        ];

        foreach ($staffMap as $method => $division) {
            if ($user->$method() && $this->divisi === $division) {
                return true;
            }
        }

        return false;
    }

    private function canManageSold(): bool
    {
        $user = auth()->user();

        if ($user->isReadOnlyWorkspace()) {
            return false;
        }

        if ($user->isKoordinatorGame()) {
            return $user->getRoleDivisionName() === $this->divisi;
        }

        if ($user->isKoordinatorFcMobile() && $this->divisi === 'FC Mobile') {
            return true;
        }

        return false;
    }
}
