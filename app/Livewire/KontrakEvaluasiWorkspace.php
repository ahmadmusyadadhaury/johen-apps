<?php

namespace App\Livewire;

use App\Models\ContractEvaluation;
use App\Models\EmployeeContract;
use App\Support\ContractEvaluationConfig;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class KontrakEvaluasiWorkspace extends Component
{
    public EmployeeContract $contract;

    public bool $isEdit = false;

    public bool $isSubmitted = false;

    public string $saveState = 'idle';

    public ?string $savedAt = null;

    public bool $showSubmitDialog = false;


    // Indikator (skala 0-4)
    public ?int $i_kehadiran = null;

    public ?int $i_ketepatan_waktu = null;

    public ?int $i_kepatuhan_peraturan = null;

    public ?int $i_tanggung_jawab = null;

    public ?int $i_kualitas_kerja = null;

    public ?int $i_produktivitas = null;

    public ?int $i_penyelesaian_tugas = null;

    public ?int $i_komunikasi = null;

    public ?int $i_kerja_sama_tim = null;

    public ?int $i_inisiatif = null;

    public ?int $i_pencapaian_target = null;

    public ?int $i_penghargaan_sanksi = null;

    // Catatan
    public string $catatanKelebihan = '';

    public string $catatanKekurangan = '';

    public string $catatanTambahan = '';

    // Rekomendasi pengembangan
    public array $devTags = [];

    public string $devOther = '';

    // Perpanjangan
    public ?int $perpanjanganBulan = null;

    public ?string $perpanjanganMulai = null;

    public ?string $perpanjanganBerakhir = null;

    // Evaluasi rekan (evaluator lain) untuk tampilan read-only + nilai gabungan
    public ?ContractEvaluation $peerEval = null;

    #[Computed]
    public function editableSections(): array
    {
        if (auth()->user()?->isSuperAdmin()) {
            return ['disiplin'];
        }

        return ['kinerja', 'sikap', 'hasil'];
    }

    public function canEditCategory(string $categoryKey): bool
    {
        return in_array($categoryKey, $this->editableSections);
    }

    public function canEditQualitative(): bool
    {
        return !auth()->user()?->isSuperAdmin();
    }

    public function mount(EmployeeContract $contract): void
    {
        if (!auth()->user()?->canEvaluateContractFor($contract)) {
            abort(403);
        }

        $myEval = $contract->evaluations()->where('evaluator_id', auth()->id())->first();

        if (!$myEval && !($this->withinWindow($contract))) {
            session()->flash('eval_error', 'Evaluasi hanya dapat diisi saat kontrak tersisa 14 hari atau kurang.');
            $this->redirectRoute('hris.kontrak-kerja', absolute: false);

            return;
        }

        $this->contract = $contract->load(['employee.divisions', 'evaluations.evaluator', 'approvals.approver']);

        $this->peerEval = $contract->evaluations
            ->load('evaluator')
            ->where('evaluator_id', '!=', auth()->id())
            ->first();

        if ($myEval) {
            $this->isEdit = true;
            $this->isSubmitted = $myEval->isSubmitted();
            $this->fillFromEvaluation($myEval);
        }
    }

    private function withinWindow(EmployeeContract $c): bool
    {
        $sisaHari = now()->startOfDay()->diffInDays($c->tanggal_berakhir->copy()->startOfDay(), false);

        return $sisaHari >= 0 && $sisaHari <= 14;
    }

    private function fillFromEvaluation(ContractEvaluation $e): void
    {
        foreach (array_keys(ContractEvaluationConfig::indicators()) as $field) {
            $this->{$field} = $e->{$field};
        }
        $this->catatanKelebihan = $e->catatan_kelebihan ?? '';
        $this->catatanKekurangan = $e->catatan_kekurangan ?? '';
        $this->catatanTambahan = $e->catatan ?? '';
        $this->devTags = $e->rekomendasi_pengembangan ?? [];
        $tags = collect(ContractEvaluationConfig::devTags());
        $this->devOther = collect($this->devTags)->reject(fn ($t) => $tags->contains($t))->implode(', ');
        $this->devTags = collect($this->devTags)->filter(fn ($t) => $tags->contains($t))->values()->all();
        $this->perpanjanganBulan = $e->perpanjangan_bulan;
        $this->perpanjanganMulai = $e->perpanjangan_mulai?->toDateString();
        $this->perpanjanganBerakhir = $e->perpanjangan_berakhir?->toDateString();
    }

    public function updated(string $property): void
    {
        $formFields = array_merge(
            array_keys(ContractEvaluationConfig::indicators()),
            ['catatanKelebihan', 'catatanKekurangan', 'catatanTambahan', 'devTags', 'devOther', 'perpanjanganBulan', 'perpanjanganMulai', 'perpanjanganBerakhir']
        );

        if (!in_array($property, $formFields)) {
            return;
        }

        $this->saveState = 'saving';

        try {
            $this->persistDraft();
            $this->saveState = 'saved';
            $this->savedAt = now()->format('H:i');
        } catch (\Throwable) {
            $this->saveState = 'error';
        }
    }

    private function evaluationAttributes(): array
    {
        $attrs = [];
        foreach (array_keys(ContractEvaluationConfig::indicators()) as $field) {
            $attrs[$field] = $this->{$field};
        }

        $tags = $this->devTags;
        if (trim($this->devOther) !== '') {
            foreach (preg_split('/,\s*/', trim($this->devOther)) as $other) {
                if ($other !== '' && !in_array($other, $tags)) {
                    $tags[] = $other;
                }
            }
        }
        $attrs['rekomendasi_pengembangan'] = $tags ?: null;
        $attrs['catatan_kelebihan'] = $this->catatanKelebihan ?: null;
        $attrs['catatan_kekurangan'] = $this->catatanKekurangan ?: null;
        $attrs['catatan'] = $this->catatanTambahan ?: null;
        $attrs['perpanjangan_bulan'] = $this->perpanjanganBulan;
        $attrs['perpanjangan_mulai'] = $this->perpanjanganMulai;
        $attrs['perpanjangan_berakhir'] = $this->perpanjanganBerakhir;

        return $attrs;
    }

    private function persistDraft(): void
    {
        ContractEvaluation::updateOrCreate(
            ['contract_id' => $this->contract->id, 'evaluator_id' => auth()->id()],
            $this->evaluationAttributes()
        );
    }

    public function saveDraft(): void
    {
        try {
            $this->persistDraft();
            $this->saveState = 'saved';
            $this->savedAt = now()->format('H:i');
            $this->dispatch('notify', type: 'success', message: 'Draft evaluasi berhasil disimpan.');
        } catch (\Throwable) {
            $this->saveState = 'error';
        }
    }

    public function toggleDevTag(string $tag): void
    {
        if (in_array($tag, $this->devTags)) {
            $this->devTags = array_values(array_diff($this->devTags, [$tag]));

            if ($tag === 'Lainnya') {
                $this->devOther = '';
            }
        } else {
            if ($tag === 'Tidak ada rekomendasi') {
                $this->devTags = [];
            }

            $this->devTags[] = $tag;
            $this->updated('devTags');
        }
    }

    public function openSubmitDialog(): void
    {
        if (count($this->missingIndicators()) === 0) {
            $this->dispatch('eval-open-submit');
        } else {
            $this->addError('summary', 'Masih ada penilaian yang belum diisi. Lengkapi terlebih dahulu.');
        }
    }


    #[Computed]
    public function missingIndicators(): array
    {
        $missing = [];

        foreach (ContractEvaluationConfig::indicators() as $indicator) {
            if (!$this->canEditCategory($indicator['category_key'])) {
                continue;
            }

            if ($this->{$indicator['field']} === null) {
                $missing[] = $indicator;
            }
        }

        return $missing;
    }

    #[Computed]
    public function approvalSteps(): array
    {
        $approvals = $this->contract->approvals;
        $managerApproval = $approvals->first(fn ($a) => $a->approver?->isManager());
        $gmApproval = $approvals->first(fn ($a) => $a->approver?->isGmCeo());

        return [
            [
                'label' => 'Evaluasi (Koordinator & HR)',
                'name' => auth()->user()?->name,
                'desc' => $this->isSubmitted ? 'Evaluasi diselesaikan' : 'Menunggu submit evaluasi',
                'state' => $this->isSubmitted ? 'done' : 'current',
            ],
            [
                'label' => 'Head of Store Manager',
                'name' => $managerApproval?->approver?->name,
                'desc' => !$this->isSubmitted ? 'Menunggu evaluasi' : ($managerApproval?->decision === 'disetujui' ? 'Disetujui' : ($managerApproval ? 'Tidak disetujui' : 'Menunggu persetujuan')),
                'state' => $managerApproval?->decision === 'disetujui' ? 'done' : ($managerApproval?->decision === 'tidak_disetujui' ? 'rejected' : ($this->isSubmitted ? 'current' : 'pending')),
            ],
            [
                'label' => 'General Manager / CEO',
                'name' => $gmApproval?->approver?->name,
                'desc' => (!$this->isSubmitted || !$managerApproval) ? 'Menunggu' : ($gmApproval?->decision === 'disetujui' ? 'Disetujui' : ($gmApproval ? 'Tidak disetujui' : 'Menunggu persetujuan')),
                'state' => $gmApproval?->decision === 'disetujui' ? 'done' : ($gmApproval?->decision === 'tidak_disetujui' ? 'rejected' : 'pending'),
            ],
        ];
    }

    public function submit(): void
    {
        abort_unless(auth()->user()?->canEvaluateContractFor($this->contract), 403);

        $rules = [];
        foreach (array_keys(ContractEvaluationConfig::indicators()) as $field) {
            if (!$this->canEditCategory(ContractEvaluationConfig::indicators()[$field]['category_key'])) {
                continue;
            }

            $rules[$field] = ['required', 'integer', 'between:0,4'];
        }
        $rules += [
            'catatanKelebihan' => ['nullable', 'string', 'max:2000'],
            'catatanKekurangan' => ['nullable', 'string', 'max:2000'],
            'catatanTambahan' => ['nullable', 'string', 'max:2000'],
            'devTags' => ['array'],
            'devOther' => ['nullable', 'string', 'max:500'],
            'perpanjanganBulan' => ['nullable', 'integer', 'between:1,36'],
            'perpanjanganMulai' => ['nullable', 'date'],
            'perpanjanganBerakhir' => ['nullable', 'date', 'after:perpanjanganMulai'],
        ];

        $this->validate($rules);

        $lulus = $this->finalScore >= ContractEvaluationConfig::PASSING_THRESHOLD;

        $isSuperAdmin = auth()->user()?->isSuperAdmin() ?? false;

        if (!$isSuperAdmin && $lulus && ($this->perpanjanganBulan === null || !$this->perpanjanganMulai || !$this->perpanjanganBerakhir)) {
            $this->addError('perpanjangan', 'Durasi & tanggal perpanjangan wajib diisi untuk rekomendasi perpanjang.');
            $this->showSubmitDialog = false;

            return;
        }

        $attrs = $this->evaluationAttributes();
        if (!$isSuperAdmin) {
            $attrs['rekomendasi'] = $lulus ? 'perpanjang' : 'tidak_perpanjang';
        }
        $attrs['submitted_at'] = now();

        DB::transaction(function () use ($attrs) {
            ContractEvaluation::updateOrCreate(
                ['contract_id' => $this->contract->id, 'evaluator_id' => auth()->id()],
                $attrs
            );
        });

        $this->isSubmitted = true;
        $this->showSubmitDialog = false;

        session()->flash('eval_success', 'Evaluasi kontrak berhasil dikirim & masuk tahap approval.');
        $this->redirectRoute('hris.kontrak-kerja', absolute: false);
    }

    #[Computed]
    public function indicatorValues(): array
    {
        $values = [];
        foreach (array_keys(ContractEvaluationConfig::indicators()) as $field) {
            $values[$field] = $this->effectiveValue($field);
        }

        return $values;
    }

    public function effectiveValue(string $field): ?int
    {
        $indicator = ContractEvaluationConfig::indicators()[$field] ?? null;

        if (!$indicator) {
            return null;
        }

        $mine = $this->{$field};

        // Jika field termasuk scope yang boleh diedit user saat ini, pakai nilai user.
        if ($this->canEditCategory($indicator['category_key'])) {
            return $mine;
        }

        // Field di luar scope: tampilkan nilai rekan (read-only) bila ada.
        if ($this->peerEval && $this->peerEval->{$field} !== null) {
            return $this->peerEval->{$field};
        }

        return $mine;
    }

    #[Computed]
    public function filledCount(): int
    {
        return collect($this->indicatorValues)->filter(fn ($v) => $v !== null)->count();
    }

    #[Computed]
    public function progressPercent(): int
    {
        $total = ContractEvaluationConfig::indicatorCount();

        return (int) round($this->filledCount / $total * 100);
    }

    #[Computed]
    public function categoryScores(): array
    {
        $scores = [];

        foreach (ContractEvaluationConfig::categories() as $category) {
            $sumWeighted = 0;
            $sumWeight = 0;

            foreach ($category['indicators'] as $indicator) {
                $value = $this->effectiveValue($indicator['field']);

                if ($value !== null) {
                    $sumWeighted += $value * $indicator['weight'];
                    $sumWeight += $indicator['weight'];
                }
            }

            $scores[$category['key']] = [
                'label' => $category['label'],
                'weight' => $category['weight'],
                'score' => $sumWeight > 0 ? round($sumWeighted / $sumWeight, 2) : null,
                'filled' => collect($category['indicators'])->filter(fn ($i) => $this->effectiveValue($i['field']) !== null)->count(),
                'total' => count($category['indicators']),
            ];
        }

        return $scores;
    }

    #[Computed]
    public function finalScore(): float|null
    {
        $sumWeighted = 0;
        $filledCount = 0;

        foreach (ContractEvaluationConfig::categories() as $category) {
            foreach ($category['indicators'] as $indicator) {
                $value = $this->effectiveValue($indicator['field']);

                if ($value !== null) {
                    $sumWeighted += $value * $indicator['weight'];
                    $filledCount++;
                }
            }
        }

        if ($filledCount === 0) {
            return null;
        }

        return round($sumWeighted / ContractEvaluationConfig::totalWeight(), 2);
    }

    #[Computed]
    public function finalPercent(): float|null
    {
        return $this->finalScore === null ? null : round($this->finalScore / ContractEvaluationConfig::SCALE_MAX * 100, 1);
    }

    #[Computed]
    public function scoreLabel(): ?string
    {
        if ($this->finalScore === null) {
            return null;
        }

        return match (true) {
            $this->finalScore >= 3.5 => 'Sangat Baik',
            $this->finalScore >= 2.75 => 'Baik',
            $this->finalScore >= 2.01 => 'Cukup',
            default => 'Kurang',
        };
    }

    public function render()
    {
        return view('livewire.kontrak-evaluasi-workspace', [
            'config' => ContractEvaluationConfig::class,
            'indicatorValues' => $this->indicatorValues,
            'filledCount' => $this->filledCount,
            'progressPercent' => $this->progressPercent,
            'categoryScores' => $this->categoryScores,
            'finalScore' => $this->finalScore,
            'finalPercent' => $this->finalPercent,
            'scoreLabel' => $this->scoreLabel,
        ]);
    }
}
