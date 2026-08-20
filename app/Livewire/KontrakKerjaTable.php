<?php

namespace App\Livewire;

use App\Models\ContractEvaluation;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\Position;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class KontrakKerjaTable extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $evaluasiModal = false;

    public bool $evaluasiDetailModal = false;

    public ?int $evalContractId = null;

    public ?int $evalKinerja = null;

    public ?int $evalDisiplin = null;

    public ?int $evalKerjasama = null;

    public ?int $evalKepatuhan = null;

    public ?int $evalKeterampilan = null;

    public string $evalCatatan = '';

    public string $evalRekomendasi = '';

    public ?int $selectedContractId = null;

    public array $selectedEvaluations = [];

    public ?array $evalContractInfo = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openEvaluasi(int $contractId): void
    {
        $contract = EmployeeContract::with('employee')->findOrFail($contractId);

        if (!auth()->user()?->canEvaluateContractFor($contract)) {
            session()->flash('eval_error', 'Anda tidak berhak mengevaluasi kontrak ini.');

            return;
        }

        $myEval = $contract->evaluations()->where('evaluator_id', auth()->id())->first();

        $this->evalContractId = $contract->id;
        $this->evalContractInfo = [
            'nama' => $contract->employee->nama,
            'nik' => $contract->employee->nik,
            'posisi' => $contract->posisi ?: '-',
            'divisi' => $contract->employee->divisionNames() ?: '-',
            'mulai' => $contract->tanggal_mulai?->isoFormat('D MMM YYYY'),
            'berakhir' => $contract->tanggal_berakhir?->isoFormat('D MMM YYYY'),
        ];
        $this->evalKinerja = $myEval?->kinerja;
        $this->evalDisiplin = $myEval?->disiplin;
        $this->evalKerjasama = $myEval?->kerjasama;
        $this->evalKepatuhan = $myEval?->kepatuhan;
        $this->evalKeterampilan = $myEval?->keterampilan;
        $this->evalCatatan = $myEval?->catatan ?? '';
        $this->evalRekomendasi = $myEval?->rekomendasi ?? '';
        $this->evaluasiModal = true;
        $this->evaluasiDetailModal = false;
    }

    public function openDetail(int $contractId): void
    {
        if (!auth()->user()?->canViewEvaluationDetail()) {
            session()->flash('eval_error', 'Anda tidak berhak melihat hasil evaluasi.');

            return;
        }

        $contract = EmployeeContract::with(['employee', 'evaluations.evaluator'])->findOrFail($contractId);

        $evaluations = $contract->evaluations->map(fn ($e) => [
            'id' => $e->id,
            'contract_id' => $e->contract_id,
            'employee' => $contract->employee->nama,
            'nik' => $contract->employee->nik,
            'posisi' => $contract->posisi,
            'divisi' => $contract->employee->divisionNames() ?: '-',
            'mulai' => $contract->tanggal_mulai?->isoFormat('D MMM YYYY'),
            'berakhir' => $contract->tanggal_berakhir?->isoFormat('D MMM YYYY'),
            'kinerja' => $e->kinerja,
            'disiplin' => $e->disiplin,
            'kerjasama' => $e->kerjasama,
            'kepatuhan' => $e->kepatuhan,
            'keterampilan' => $e->keterampilan,
            'catatan' => $e->catatan,
            'rekomendasi' => $e->rekomendasi,
            'evaluator' => $e->evaluator?->name ?? '-',
            'evaluator_role' => $e->evaluator?->getRoleDisplayName() ?? '',
            'created_at' => $e->created_at?->isoFormat('D MMM YYYY, HH:mm'),
            'can_edit' => auth()->id() === $e->evaluator_id,
        ])->values()->all();

        if (empty($evaluations)) {
            $this->openEvaluasi($contractId);

            return;
        }

        $this->selectedContractId = $contract->id;
        $this->selectedEvaluations = $evaluations;
        $this->evaluasiDetailModal = true;
        $this->evaluasiModal = false;
    }

    public function saveEvaluasi(): void
    {
        $this->validate([
            'evalContractId' => 'required|integer|exists:employee_contracts,id',
            'evalKinerja' => 'required|integer|min:1|max:5',
            'evalDisiplin' => 'required|integer|min:1|max:5',
            'evalKerjasama' => 'required|integer|min:1|max:5',
            'evalKepatuhan' => 'required|integer|min:1|max:5',
            'evalKeterampilan' => 'required|integer|min:1|max:5',
            'evalCatatan' => 'nullable|string|max:2000',
            'evalRekomendasi' => 'required|in:perpanjang,tidak_perpanjang,pertimbangkan',
        ]);

        $contract = EmployeeContract::find($this->evalContractId);

        abort_unless($contract && auth()->user()?->canEvaluateContractFor($contract), 403);

        ContractEvaluation::updateOrCreate(
            ['contract_id' => $this->evalContractId, 'evaluator_id' => auth()->id()],
            [
                'kinerja' => $this->evalKinerja,
                'disiplin' => $this->evalDisiplin,
                'kerjasama' => $this->evalKerjasama,
                'kepatuhan' => $this->evalKepatuhan,
                'keterampilan' => $this->evalKeterampilan,
                'catatan' => $this->evalCatatan ?: null,
                'rekomendasi' => $this->evalRekomendasi,
            ]
        );

        session()->flash('eval_success', 'Evaluasi kontrak berhasil disimpan.');

        $this->reset(['evaluasiModal', 'evalContractId', 'evalKinerja', 'evalDisiplin', 'evalKerjasama', 'evalKepatuhan', 'evalKeterampilan', 'evalCatatan', 'evalRekomendasi', 'evalContractInfo']);
    }

    public function render()
    {
        $user = auth()->user();
        $scoped = $this->isScoped();
        $teamEmployeeIds = $scoped ? $this->getScopedTeamEmployeeIds() : null;

        $base = EmployeeContract::query()
            ->when($scoped, fn ($q) => $q->whereIn('employee_id', $teamEmployeeIds));

        $totalAktif = (clone $base)->where('status', 'berlaku')
            ->whereDate('tanggal_berakhir', '>=', now())->count();
        $akanBerakhir = (clone $base)->where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(14))
            ->where('tanggal_berakhir', '>=', now())
            ->count();
        $urgent = (clone $base)->where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(3))
            ->where('tanggal_berakhir', '>=', now())
            ->count();
        $totalSelesai = (clone $base)->where('status', 'selesai')->count();

        $segeraHabis = (clone $base)->with('employee.divisions')
            ->where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(14))
            ->where('tanggal_berakhir', '>=', now())
            ->get();

        $contracts = (clone $base)
            ->with(['employee.divisions', 'evaluations.evaluator'])
            ->withCount('evaluations')
            ->where('status', 'berlaku')
            ->whereDate('tanggal_berakhir', '>=', now())
            ->when($this->search, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('nama', 'like', "%{$this->search}%")
                      ->orWhere('nik', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('tanggal_berakhir', 'asc')
            ->paginate(10);

        $contracts->getCollection()->transform(function (EmployeeContract $contract) use ($user) {
            $contract->can_evaluate = $user?->canEvaluateContractFor($contract) ?? false;

            return $contract;
        });

        $canViewDetail = $user?->canViewEvaluationDetail() ?? false;

        return view('livewire.kontrak-kerja-table', compact(
            'contracts', 'totalAktif', 'akanBerakhir', 'urgent', 'totalSelesai', 'segeraHabis', 'canViewDetail'
        ));
    }

    private function isScoped(): bool
    {
        $user = auth()->user();

        return $user && ($user->isAnyKoordinator() || $user->isManager());
    }

    private function getScopedTeamEmployeeIds(): array
    {
        $positionName = $this->getScopedPositionName();
        if (!$positionName) {
            return [];
        }

        $position = Position::where('nama', $positionName)->first();
        if (!$position) {
            return [];
        }

        $descendantIds = $this->getAllDescendantIds($position);
        if (empty($descendantIds)) {
            return [];
        }

        return DB::table('employee_position')
            ->whereIn('position_id', $descendantIds)
            ->distinct()
            ->pluck('employee_id')
            ->all();
    }

    private function getScopedPositionName(): ?string
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        if ($user->isManager()) {
            return $user->employee?->mainPosition()?->nama;
        }

        $mapped = match (true) {
            $user->isKoordinatorPubg() => 'Koordinator Johen PUBG',
            $user->isKoordinatorFf() => 'Koordinator Free Fire',
            $user->isKoordinatorMlbb() => 'Koordinator MLBB',
            $user->isKoordinatorEfootball() => 'Koordinator E-football',
            $user->isKoordinatorValorant() => 'Koordinator Valorant',
            $user->isKoordinatorRoblox() => 'Koordinator Roblox',
            $user->isKoordinatorMonkeyPubg() => 'Koordinator Monkey PUBG',
            $user->isKoordinatorIt() => 'Koordinator IT',
            $user->isKoordinatorCreative() => 'Koordinator Creative',
            $user->isKoordinatorAdmin() => 'Koordinator Admin',
            $user->isKoordinatorStock() => 'Koordinator Stock',
            $user->isKoordinatorFcMobile() => 'Koordinator FC Mobile',
            default => null,
        };

        if ($mapped) {
            return $mapped;
        }

        if ($user->isKoordinator()) {
            $position = $user->employee?->mainPosition()?->nama;
            if ($position && str_starts_with(strtolower($position), 'koordinator')) {
                return $position;
            }
        }

        return null;
    }

    private function getAllDescendantIds(Position $position): array
    {
        $ids = [];
        foreach ($position->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllDescendantIds($child));
        }

        return $ids;
    }
}