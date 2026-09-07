<?php

namespace App\Livewire;

use App\Models\ContractApproval;
use App\Models\EmployeeContract;
use App\Models\Position;
use App\Support\ContractEvaluationConfig;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class KontrakKerjaTable extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $evaluasiDetailModal = false;

    public ?int $selectedContractId = null;

    public array $selectedEvaluations = [];

    public ?array $evalContractInfo = null;

    public ?int $approveContractId = null;

    public bool $hasSubmittedEval = false;

    public bool $hasAnyEvaluation = false;

    public string $approveDecision = ContractApproval::DECISION_SETUJU;

    public string $approveCatatan = '';

    public ?array $penilaianInfo = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openPenilaian(int $contractId): void
    {
        if (! auth()->user()?->canViewEvaluationDetail()) {
            session()->flash('eval_error', 'Anda tidak berhak melihat hasil penilaian.');

            return;
        }

        $contract = EmployeeContract::with(['employee', 'evaluations.evaluator', 'approvals.approver'])->findOrFail($contractId);

        $this->penilaianInfo = [
            'nama' => $contract->employee->nama,
            'nik' => $contract->employee->nik,
            'posisi' => $contract->posisi ?: '-',
            'divisi' => $contract->employee->divisionNames() ?: '-',
            'mulai' => $contract->tanggal_mulai?->isoFormat('D MMM YYYY'),
            'berakhir' => $contract->tanggal_berakhir?->isoFormat('D MMM YYYY'),
        ];

        if (auth()->user()?->canApproveContractFor($contract)) {
            $this->hasSubmittedEval = $contract->evaluations()->whereNotNull('submitted_at')->exists();
            $this->hasAnyEvaluation = $contract->evaluations()->exists();
            $myApproval = $contract->approvals->firstWhere('approver_id', auth()->id());
            $this->approveContractId = $contract->id;
            $this->approveDecision = $myApproval?->decision ?? ContractApproval::DECISION_SETUJU;
            $this->approveCatatan = $myApproval?->catatan ?? '';
        } else {
            $this->hasSubmittedEval = false;
            $this->hasAnyEvaluation = false;
            $this->reset('approveContractId', 'approveDecision', 'approveCatatan');
        }

        $this->loadPenilaianEntries($contract);
        $this->selectedContractId = $contract->id;
        $this->evaluasiDetailModal = true;
        $this->evaluasiModal = false;
    }

    private function loadPenilaianEntries(EmployeeContract $contract): void
    {
        $indicators = ContractEvaluationConfig::indicators();

        $evaluations = $contract->evaluations->map(function ($e) use ($indicators, $contract) {
            $isNew = $e->isNewFormat();

            $catScores = [];
            $finalScore = null;
            if ($isNew) {
                foreach (ContractEvaluationConfig::categories() as $cat) {
                    $vals = collect($cat['indicators'])->map(fn ($ind) => $e->{$ind['field']});
                    $catScores[$cat['key']] = $vals->contains(fn ($v) => $v === null)
                        ? null
                        : round($vals->avg(), 2);
                }
                $filled = collect($catScores)->filter()->values();
                $finalScore = $filled->isNotEmpty() ? round($filled->avg(), 2) : null;
            }

            return [
                'id' => $e->id,
                'jenis' => 'evaluasi',
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
                'is_new_format' => $isNew,
                'catatan_kelebihan' => $e->catatan_kelebihan,
                'catatan_kekurangan' => $e->catatan_kekurangan,
                'rekomendasi_pengembangan' => $e->rekomendasi_pengembangan,
                'perpanjangan_bulan' => $e->perpanjangan_bulan,
                'perpanjangan_mulai' => $e->perpanjangan_mulai?->isoFormat('D MMM YYYY'),
                'perpanjangan_berakhir' => $e->perpanjangan_berakhir?->isoFormat('D MMM YYYY'),
                'submitted_at' => $e->submitted_at?->isoFormat('D MMM YYYY, HH:mm'),
                'cat_scores' => $catScores,
                'final_score' => $finalScore,
                'indicators' => collect($indicators)->map(fn ($ind) => [
                    'field' => $ind['field'],
                    'label' => $ind['label'],
                    'weight' => $ind['weight'],
                    'category_key' => $ind['category_key'],
                    'category_label' => $ind['category_label'],
                    'value' => $e->{$ind['field']},
                ])->values()->all(),
                'decision' => null,
                'evaluator' => $e->evaluator?->name ?? '-',
                'evaluator_role' => $e->evaluator?->getRoleDisplayName() ?? '',
                'created_at' => $e->created_at?->isoFormat('D MMM YYYY, HH:mm'),
                'can_edit' => auth()->id() === $e->evaluator_id,
            ];
        })->values()->all();

        $approvals = $contract->approvals->map(fn ($a) => [
            'id' => $a->id,
            'jenis' => 'approval',
            'contract_id' => $a->contract_id,
            'employee' => $contract->employee->nama,
            'nik' => $contract->employee->nik,
            'posisi' => $contract->posisi,
            'divisi' => $contract->employee->divisionNames() ?: '-',
            'mulai' => $contract->tanggal_mulai?->isoFormat('D MMM YYYY'),
            'berakhir' => $contract->tanggal_berakhir?->isoFormat('D MMM YYYY'),
            'kinerja' => null,
            'disiplin' => null,
            'kerjasama' => null,
            'kepatuhan' => null,
            'keterampilan' => null,
            'catatan' => $a->catatan,
            'rekomendasi' => null,
            'decision' => $a->decision,
            'evaluator' => $a->approver?->name ?? '-',
            'evaluator_role' => $a->approver?->getRoleDisplayName() ?? '',
            'created_at' => $a->created_at?->isoFormat('D MMM YYYY, HH:mm'),
            'can_edit' => auth()->id() === $a->approver_id,
        ])->values()->all();

        $this->selectedEvaluations = array_merge($evaluations, $approvals);
    }

    public function saveApproval(): void
    {
        $this->validate([
            'approveContractId' => 'required|integer|exists:employee_contracts,id',
            'approveDecision' => 'required|in:'.ContractApproval::DECISION_SETUJU.','.ContractApproval::DECISION_TIDAK,
            'approveCatatan' => 'nullable|string|max:2000',
        ]);

        $contract = EmployeeContract::find($this->approveContractId);

        abort_unless($contract && auth()->user()?->canApproveContractFor($contract), 403);

        ContractApproval::updateOrCreate(
            ['contract_id' => $this->approveContractId, 'approver_id' => auth()->id()],
            [
                'decision' => $this->approveDecision,
                'catatan' => $this->approveCatatan ?: null,
            ]
        );

        $extended = $this->extendContractIfFullyApproved($contract);

        session()->flash(
            'eval_success',
            $extended
                ? 'Keputusan approval berhasil disimpan. Kontrak otomatis diperpanjang & kontrak baru (addendum) telah dibuat.'
                : 'Keputusan approval kontrak berhasil disimpan.'
        );

        if ($contract = EmployeeContract::with(['employee', 'evaluations.evaluator', 'approvals.approver'])->find($this->approveContractId)) {
            $this->hasSubmittedEval = $contract->evaluations()->whereNotNull('submitted_at')->exists();
            $this->hasAnyEvaluation = $contract->evaluations()->exists();
            $this->loadPenilaianEntries($contract);
        }
    }

    private function extendContractIfFullyApproved(EmployeeContract $contract): bool
    {
        $contract = EmployeeContract::with(['approvals.approver', 'evaluations'])->find($contract->id);

        if (! $contract) {
            return false;
        }

        $managerApproval = $contract->approvals->first(fn ($a) => $a->approver?->isManager());
        $gmApproval = $contract->approvals->first(fn ($a) => $a->approver?->isGmCeo());

        $fullyApproved = $managerApproval?->decision === ContractApproval::DECISION_SETUJU
            && $gmApproval?->decision === ContractApproval::DECISION_SETUJU;

        if (! $fullyApproved) {
            return false;
        }

        // Hindari duplikat: jika addendum dari kontrak ini sudah pernah dibuat.
        if (EmployeeContract::where('extended_from_contract_id', $contract->id)->exists()) {
            return false;
        }

        // Rekomendasi perpanjangan dari koordinator (evaluasi non-super-admin).
        $recommendation = $contract->evaluations
            ->filter(fn ($e) => $e->rekomendasi === 'perpanjang'
                && $e->perpanjangan_mulai !== null
                && $e->perpanjangan_berakhir !== null)
            ->sortByDesc('submitted_at')
            ->first();

        if (! $recommendation) {
            return false;
        }

        DB::transaction(function () use ($contract, $recommendation) {
            $contract->update(['status' => 'selesai']);

            EmployeeContract::create([
                'employee_id' => $contract->employee_id,
                'jenis_kontrak' => $contract->jenis_kontrak,
                'posisi' => $contract->posisi,
                'atasan' => $contract->atasan,
                'tanggal_mulai' => $recommendation->perpanjangan_mulai,
                'tanggal_berakhir' => $recommendation->perpanjangan_berakhir,
                'status' => 'berlaku',
                'keterangan' => 'Addendum perpanjangan otomatis dari evaluasi kontrak.',
                'is_addendum' => true,
                'extended_from_contract_id' => $contract->id,
            ]);
        });

        return true;
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
            ->with(['employee.divisions', 'evaluations.evaluator', 'approvals.approver'])
            ->withCount(['evaluations', 'approvals'])
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
            $contract->already_evaluated = $user
                && $contract->evaluations->contains(fn ($e) => $e->evaluator_id === $user->id);
            $contract->can_approve = $user?->canApproveContractFor($contract) ?? false;
            $contract->already_approved = $user
                && $contract->approvals->contains(fn ($a) => $a->approver_id === $user->id);

            return $contract;
        });

        $canViewDetail = $user?->canViewEvaluationDetail() ?? false;

        return view('livewire.kontrak-kerja-table', compact(
            'contracts', 'totalAktif', 'akanBerakhir', 'urgent', 'totalSelesai', 'segeraHabis', 'canViewDetail'
        ));
    }

    private function withinEvaluationWindow(EmployeeContract $contract): bool
    {
        $sisaHari = now()->startOfDay()->diffInDays($contract->tanggal_berakhir->copy()->startOfDay(), false);

        return $sisaHari >= 0 && $sisaHari <= 14;
    }

    private function isScoped(): bool
    {
        $user = auth()->user();

        return $user && ($user->isAnyKoordinator() || $user->isManager());
    }

    private function getScopedTeamEmployeeIds(): array
    {
        $positionName = $this->getScopedPositionName();
        if (! $positionName) {
            return [];
        }

        $position = Position::where('nama', $positionName)->first();
        if (! $position) {
            return [];
        }

        $descendantIds = $this->getAllDescendantIds($position);
        $descendantIds[] = $position->id;

        if (empty($descendantIds)) {
            return [];
        }

        $ids = DB::table('employee_position')
            ->whereIn('position_id', $descendantIds)
            ->distinct()
            ->pluck('employee_id')
            ->all();

        // Jangan tampilkan kontrak/data milik user yang sedang login.
        $ownEmployeeId = auth()->user()?->employee_id;
        if ($ownEmployeeId !== null) {
            $ids = array_values(array_diff($ids, [$ownEmployeeId]));
        }

        return $ids;
    }

    private function getScopedPositionName(): ?string
    {
        $user = auth()->user();
        if (! $user) {
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
