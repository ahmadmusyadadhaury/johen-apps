<?php

namespace App\Livewire;

use App\Models\ContractEvaluation;
use App\Models\EmployeeContract;
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

    public ?array $selectedEvaluation = null;

    public ?array $evalContractInfo = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openEvaluasi(int $contractId): void
    {
        $contract = EmployeeContract::with('evaluation')->findOrFail($contractId);

        $this->evalContractId = $contract->id;
        $this->evalContractInfo = [
            'nama' => $contract->employee->nama,
            'nik' => $contract->employee->nik,
            'posisi' => $contract->posisi ?: '-',
            'divisi' => $contract->employee->divisionNames() ?: '-',
            'mulai' => $contract->tanggal_mulai?->isoFormat('D MMM YYYY'),
            'berakhir' => $contract->tanggal_berakhir?->isoFormat('D MMM YYYY'),
        ];
        $this->evalKinerja = $contract->evaluation?->kinerja;
        $this->evalDisiplin = $contract->evaluation?->disiplin;
        $this->evalKerjasama = $contract->evaluation?->kerjasama;
        $this->evalKepatuhan = $contract->evaluation?->kepatuhan;
        $this->evalKeterampilan = $contract->evaluation?->keterampilan;
        $this->evalCatatan = $contract->evaluation?->catatan ?? '';
        $this->evalRekomendasi = $contract->evaluation?->rekomendasi ?? '';
        $this->evaluasiModal = true;
        $this->evaluasiDetailModal = false;
    }

    public function openDetail(int $contractId): void
    {
        $contract = EmployeeContract::with('evaluation.evaluator')->findOrFail($contractId);

        $eval = $contract->evaluation;
        if (!$eval) {
            $this->openEvaluasi($contractId);

            return;
        }

        $this->selectedEvaluation = [
            'id' => $eval->id,
            'contract_id' => $eval->contract_id,
            'employee' => $contract->employee->nama,
            'nik' => $contract->employee->nik,
            'posisi' => $contract->posisi,
            'divisi' => $contract->employee->divisionNames() ?: '-',
            'mulai' => $contract->tanggal_mulai?->isoFormat('D MMM YYYY'),
            'berakhir' => $contract->tanggal_berakhir?->isoFormat('D MMM YYYY'),
            'kinerja' => $eval->kinerja,
            'disiplin' => $eval->disiplin,
            'kerjasama' => $eval->kerjasama,
            'kepatuhan' => $eval->kepatuhan,
            'keterampilan' => $eval->keterampilan,
            'catatan' => $eval->catatan,
            'rekomendasi' => $eval->rekomendasi,
            'evaluator' => $eval->evaluator?->name ?? '-',
            'evaluator_role' => $eval->evaluator?->role ?? '',
            'created_at' => $eval->created_at?->isoFormat('D MMM YYYY, HH:mm'),
            'can_edit' => auth()->id() === $eval->evaluator_id || auth()->user()?->isSuperAdmin(),
        ];

        $this->evaluasiDetailModal = true;
        $this->evaluasiModal = false;
    }

    public function saveEvaluasi(): void
    {
        abort_unless(auth()->user()?->canEvaluateContract(), 403);

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

        $eval = ContractEvaluation::updateOrCreate(
            ['contract_id' => $this->evalContractId],
            [
                'evaluator_id' => auth()->id(),
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
        $totalAktif = EmployeeContract::where('status', 'berlaku')
            ->whereDate('tanggal_berakhir', '>=', now())->count();
        $akanBerakhir = EmployeeContract::where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(14))
            ->where('tanggal_berakhir', '>=', now())
            ->count();
        $urgent = EmployeeContract::where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(3))
            ->where('tanggal_berakhir', '>=', now())
            ->count();
        $totalSelesai = EmployeeContract::where('status', 'selesai')->count();

        $segeraHabis = EmployeeContract::with('employee.divisions')
            ->where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(14))
            ->where('tanggal_berakhir', '>=', now())
            ->get();

        $contracts = EmployeeContract::with(['employee.divisions', 'evaluation.evaluator'])
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

        $canEvaluate = auth()->user()?->canEvaluateContract() ?? false;

        return view('livewire.kontrak-kerja-table', compact(
            'contracts', 'totalAktif', 'akanBerakhir', 'urgent', 'totalSelesai', 'segeraHabis', 'canEvaluate'
        ));
    }
}