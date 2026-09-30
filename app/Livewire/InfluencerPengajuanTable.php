<?php

namespace App\Livewire;

use App\Models\Influencer;
use App\Models\InfluencerPembayaran;
use App\Models\InfluencerPengajuan;
use App\Models\User;
use App\Support\InfluencerPengajuanRouting;
use Livewire\Component;
use Livewire\WithPagination;

class InfluencerPengajuanTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $showKolCreateButton = false;
    public bool $showSuccessModal = false;
    public string $successMessage = '';
    public bool $showDeleteConfirmation = false;
    public ?int $deletePengajuanId = null;
    public ?int $editId = null;

    public string $no_kontrak = '';
    public string $nama = '';
    public string $divisi = '';
    public string $rekomendasiLamaKontrak = '';
    public string $link_sosmed = '';
    public string $biaya = '';
    public string $keterangan = '';

    public const DIVISI_OPTIONS = [
        'Johen PUBG',
        'Johen MLBB',
        'Johen E-Football',
        'Johen Roblox',
        'Johen Free Fire',
        'Johen FC Mobile',
        'Johen Valorant',
        'Monkey PUBG',
    ];

    public string $alasanTolak = '';
    public ?int $tolakId = null;

    public function mount(bool $showKolCreateButton = false): void
    {
        $this->showKolCreateButton = $showKolCreateButton;
    }

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'divisi' => ['required', 'in:'.implode(',', self::DIVISI_OPTIONS)],
            'rekomendasiLamaKontrak' => 'required|integer|min:1|max:60',
            'link_sosmed' => 'nullable|string|max:500',
            'biaya' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama influencer wajib diisi.',
            'divisi.required' => 'Divisi wajib dipilih.',
            'rekomendasiLamaKontrak.required' => 'Rekomendasi lama kontrak wajib diisi.',
            'rekomendasiLamaKontrak.integer' => 'Lama kontrak harus berupa jumlah bulan.',
            'rekomendasiLamaKontrak.min' => 'Lama kontrak minimal 1 bulan.',
            'rekomendasiLamaKontrak.max' => 'Lama kontrak maksimal 60 bulan.',
        ];
    }

    public function openNew(): void
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $pengajuan = InfluencerPengajuan::findOrFail($id);
        abort_unless($this->canEditSubmission($pengajuan), 403);

        $this->editId = $pengajuan->id;
        $this->no_kontrak = $pengajuan->no_kontrak ?? '';
        $this->nama = $pengajuan->nama;
        $this->divisi = $pengajuan->divisi ?? '';
        $this->rekomendasiLamaKontrak = (string) $pengajuan->rekomendasi_lama_kontrak;
        $this->link_sosmed = $pengajuan->link_sosmed ?? '';
        $this->biaya = $pengajuan->biaya !== null ? (string) $pengajuan->biaya : '';
        $this->keterangan = $pengajuan->keterangan ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            $pengajuan = InfluencerPengajuan::findOrFail($this->editId);
            abort_unless($this->canEditSubmission($pengajuan), 403);
            $this->validate();

            $assignedHos = InfluencerPengajuanRouting::headOfStorePositionForDivision($this->divisi);
            abort_unless($assignedHos, 422, 'Head of Store untuk divisi ini belum tersedia di struktur organisasi.');

            $wasApproved = $pengajuan->status === 'approved';
            $updates = [
                'nama' => $this->nama,
                'divisi' => $this->divisi,
                'rekomendasi_lama_kontrak' => $this->rekomendasiLamaKontrak,
                'biaya' => $this->biaya ?: null,
                'keterangan' => $this->keterangan ?: null,
                'assigned_hos_position_id' => $assignedHos->id,
            ];

            if (!$wasApproved) {
                $updates += [
                    'status' => 'pending_creative',
                    'approved_coordinator_by' => null,
                    'approved_coordinator_at' => null,
                    'approved_hos1_by' => null,
                    'approved_hos1_at' => null,
                    'approved_gm_by' => null,
                    'approved_gm_at' => null,
                ];
            }

            $pengajuan->update($updates);

            if ($wasApproved && $pengajuan->influencer) {
                $pengajuan->influencer->update([
                    'nama' => $this->nama,
                    'divisi' => $this->divisi,
                    'biaya' => $this->biaya ?: null,
                    'keterangan' => $this->keterangan ?: null,
                ]);
            }

            $this->dispatch('influencer-pengajuan-updated');
            $this->successMessage = $wasApproved
                ? 'Pengajuan influencer berhasil diperbarui.'
                : 'Pengajuan influencer berhasil diperbarui dan menunggu persetujuan Koordinator Creative.';
            $this->showSuccessModal = true;
            $this->close();
            return;
        }

        abort_unless(auth()->user()->isKoordinatorCreative(), 403);
        $this->validate();

        $assignedHos = InfluencerPengajuanRouting::headOfStorePositionForUser(auth()->user());
        $assignedHosName = InfluencerPengajuanRouting::headOfStoreNameForUser(auth()->user());
        abort_unless($assignedHosName, 422, 'Head of Store koordinator ini belum dapat ditentukan dari struktur organisasi.');

        InfluencerPengajuan::create([
            'no_kontrak' => '',
            'nama' => $this->nama,
            'divisi' => $this->divisi,
            'rekomendasi_lama_kontrak' => $this->rekomendasiLamaKontrak,
            'link_sosmed' => $this->link_sosmed ?: null,
            'biaya' => $this->biaya ?: null,
            'keterangan' => $this->keterangan ?: null,
            'status' => 'pending_hos1',
            'pengaju_id' => auth()->id(),
            'assigned_hos_position_id' => $assignedHos?->id,
        ]);
        $this->dispatch('influencer-pengajuan-updated');

        $this->successMessage = 'Pengajuan influencer berhasil dikirim dan menunggu persetujuan '.$assignedHosName.'.';
        $this->showSuccessModal = true;
        $this->close();
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->successMessage = '';
    }

    public function confirmDelete(int $id): void
    {
        $pengajuan = InfluencerPengajuan::findOrFail($id);
        abort_unless($this->canDeleteSubmission($pengajuan), 403);

        $this->deletePengajuanId = $pengajuan->id;
        $this->showDeleteConfirmation = true;
    }

    public function deletePengajuan(): void
    {
        abort_unless($this->deletePengajuanId, 403);

        $pengajuan = InfluencerPengajuan::findOrFail($this->deletePengajuanId);
        abort_unless($this->canDeleteSubmission($pengajuan), 403);

        $pengajuan->delete();
        $this->showDeleteConfirmation = false;
        $this->deletePengajuanId = null;
        $this->resetPage();
        $this->dispatch('influencer-pengajuan-updated');
        if ($this->isKolSubmitter()) {
            $this->successMessage = 'Pengajuan influencer berhasil dihapus.';
            $this->showSuccessModal = true;
        } else {
            session()->flash('message', 'Pengajuan influencer berhasil dihapus.');
        }
    }

    public function cancelDeletePengajuan(): void
    {
        $this->showDeleteConfirmation = false;
        $this->deletePengajuanId = null;
    }

    public function approve(int $id): void
    {
        $pengajuan = InfluencerPengajuan::findOrFail($id);
        $user = auth()->user();

        $isAssignedHos = $this->isAssignedHeadOfStore($user, $pengajuan);
        $isGm = $user->isGmCeo();

        if ($user->isKoordinatorCreative() && $pengajuan->status === 'pending_creative') {
            $pengajuan->update([
                'status' => 'pending_hos1',
                'approved_coordinator_by' => $user->id,
                'approved_coordinator_at' => now(),
            ]);
            $this->dispatch('influencer-pengajuan-updated');
            session()->flash('message', 'Pengajuan disetujui Koordinator Creative, menunggu persetujuan '.$pengajuan->assignedHosPosition?->nama.'.');
        } elseif ($isAssignedHos && $pengajuan->status === 'pending_hos1') {
            $pengajuan->update([
                'status' => 'pending_gm',
                'approved_hos1_by' => $user->id,
                'approved_hos1_at' => now(),
            ]);
            $this->dispatch('influencer-pengajuan-updated');
            session()->flash('message', 'Pengajuan disetujui, menunggu persetujuan General Manager.');
        } elseif ($isGm && $pengajuan->status === 'pending_gm') {
            $pengajuan->update([
                'status' => 'approved',
                'approved_gm_by' => $user->id,
                'approved_gm_at' => now(),
            ]);

            if ($pengajuan->is_perpanjangan && $pengajuan->influencer_id) {
                $influencer = Influencer::findOrFail($pengajuan->influencer_id);
                $contractMonths = max(1, (int) $pengajuan->rekomendasi_lama_kontrak);
                $nextContractStart = $influencer->habis_kontrak->copy()->addDay()->startOfDay();
                if ($nextContractStart->lt(now()->startOfDay())) {
                    $nextContractStart = now()->startOfDay();
                }
                $extensionEnd = $nextContractStart->copy()->addMonthsNoOverflow($contractMonths - 1);
                $extensionCost = $pengajuan->biaya ?? $influencer->biaya;

                $influencer->update([
                    'habis_kontrak' => $extensionEnd,
                    'biaya' => $extensionCost,
                    'keterangan' => $pengajuan->keterangan ?? $influencer->keterangan,
                ]);
                $this->generateExtensionPayments($influencer, $nextContractStart, $contractMonths, $extensionCost);
                session()->flash('message', 'Perpanjangan influencer disetujui. Masa kontrak dan jadwal pembayaran telah diperbarui.');
            } else {
                $contractStart = now()->startOfDay();
                $contractMonths = max(1, (int) ($pengajuan->rekomendasi_lama_kontrak
                    ?? ($pengajuan->mulai_kontrak?->diffInMonths($pengajuan->habis_kontrak) + 1)
                    ?? 1));

                $influencer = Influencer::create([
                    'no_kontrak' => null,
                    'nama' => $pengajuan->nama,
                    'divisi' => $pengajuan->divisi,
                    'mulai_kontrak' => $contractStart,
                    'habis_kontrak' => $contractStart->copy()->addMonthsNoOverflow($contractMonths - 1),
                    'link_sosmed' => $pengajuan->link_sosmed,
                    'biaya' => $pengajuan->biaya,
                    'keterangan' => $pengajuan->keterangan,
                ]);
                $pengajuan->update(['influencer_id' => $influencer->id]);
                $this->generatePayments($influencer);
                session()->flash('message', 'Pengajuan disetujui. Data influencer dan pembayaran otomatis dibuat.');
            }
            $this->dispatch('influencer-pengajuan-updated');
        } else {
            session()->flash('error', 'Anda tidak memiliki wewenang untuk menyetujui pengajuan ini.');
        }
    }

    public function openTolak(int $id): void
    {
        $this->tolakId = $id;
        $this->alasanTolak = '';
    }

    public function batalTolak(): void
    {
        $this->tolakId = null;
        $this->alasanTolak = '';
    }

    public function reject(int $id): void
    {
        $pengajuan = InfluencerPengajuan::findOrFail($id);
        $user = auth()->user();
        abort_unless(
            ($pengajuan->status === 'pending_hos1' && $this->isAssignedHeadOfStore($user, $pengajuan))
            || ($pengajuan->status === 'pending_creative' && $user->isKoordinatorCreative())
            || ($pengajuan->status === 'pending_gm' && $user->isGmCeo()),
            403
        );
        $this->validate([
            'alasanTolak' => 'required|string|min:5',
        ]);

        $pengajuan->update([
            'status' => 'rejected',
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'alasan_penolakan' => $this->alasanTolak,
        ]);
        $this->dispatch('influencer-pengajuan-updated');

        session()->flash('message', 'Pengajuan ditolak.');
        $this->batalTolak();
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetInput();
    }

    private function resetInput(): void
    {
        $this->editId = null;
        $this->no_kontrak = '';
        $this->nama = '';
        $this->divisi = '';
        $this->rekomendasiLamaKontrak = '';
        $this->link_sosmed = '';
        $this->biaya = '';
        $this->keterangan = '';
        $this->alasanTolak = '';
        $this->tolakId = null;
        $this->resetErrorBag();
    }

    private function isAssignedHeadOfStore(User $user, InfluencerPengajuan $pengajuan): bool
    {
        if (!$user->isHeadOfStore()) {
            return false;
        }

        return InfluencerPengajuanRouting::isAssignedToHeadOfStore($pengajuan, $user);
    }

    public function canApprove(InfluencerPengajuan $pengajuan): bool
    {
        $user = auth()->user();

        return ($pengajuan->status === 'pending_creative' && $user->isKoordinatorCreative())
            || ($pengajuan->status === 'pending_hos1' && $this->isAssignedHeadOfStore($user, $pengajuan))
            || ($pengajuan->status === 'pending_gm' && $user->isGmCeo());
    }

    public function canDeleteSubmission(InfluencerPengajuan $pengajuan): bool
    {
        $user = auth()->user();

        return ($user->isKoordinatorCreative() || $this->isKolSubmitter())
            && (int) $pengajuan->pengaju_id === (int) $user->id
            && in_array($pengajuan->status, ['pending_creative', 'pending_hos1', 'approved'], true);
    }

    public function canEditSubmission(InfluencerPengajuan $pengajuan): bool
    {
        $user = auth()->user();

        return $this->isKolSubmitter()
            && (int) $pengajuan->pengaju_id === (int) $user->id
            && in_array($pengajuan->status, ['pending_creative', 'pending_hos1', 'approved'], true);
    }

    private function isKolSubmitter(): bool
    {
        $user = auth()->user();
        $positionName = $user->employee?->mainPosition()?->nama ?? $user->employee?->position ?? '';

        return $user->isStaffCreative() && str_starts_with($positionName, 'Admin KOL');
    }

    public function isWaitingForPreviousApproval(InfluencerPengajuan $pengajuan): bool
    {
        $user = auth()->user();

        return ($user->isHeadOfStore() && $pengajuan->status === 'pending_creative')
            || ($user->isGmCeo() && in_array($pengajuan->status, ['pending_creative', 'pending_hos1'], true));
    }

    private function generatePayments(Influencer $influencer): void
    {
        $start = $influencer->mulai_kontrak->copy();
        $end = $influencer->habis_kontrak;
        $jumlah = $influencer->biaya;

        $totalMonths = $start->diffInMonths($end) + 1;

        for ($i = 0; $i < $totalMonths; $i++) {
            $current = $start->copy()->addMonths($i);
            $hari = min($start->day, $current->daysInMonth);
            $jatuhTempo = $current->copy()->day($hari);

            InfluencerPembayaran::create([
                'influencer_id' => $influencer->id,
                'bulan_ke' => $i + 1,
                'tanggal_jatuh_tempo' => $jatuhTempo,
                'jumlah' => $jumlah,
                'status' => 'pending',
            ]);
        }
    }

    private function generateExtensionPayments(Influencer $influencer, \Illuminate\Support\Carbon $start, int $months, ?string $amount): void
    {
        $firstMonthNumber = ((int) $influencer->payments()->max('bulan_ke')) + 1;

        for ($index = 0; $index < $months; $index++) {
            $current = $start->copy()->addMonthsNoOverflow($index);
            $dueDate = $current->copy()->day(min($start->day, $current->daysInMonth));

            InfluencerPembayaran::create([
                'influencer_id' => $influencer->id,
                'bulan_ke' => $firstMonthNumber + $index,
                'tanggal_jatuh_tempo' => $dueDate,
                'jumlah' => $amount ?? 0,
                'status' => 'pending',
            ]);
        }
    }

    public function render()
    {
        $user = auth()->user();
        $isHos = $user->isHeadOfStore();
        $isGm = $user->isGmCeo();

        $query = InfluencerPengajuan::with('pengaju', 'approverCoordinator', 'approverHos1', 'approverGm', 'rejector', 'assignedHosPosition');

        if ($user->isSuperAdminLike()) {
            // lihat semua
        } elseif ($user->isKoordinatorCreative()) {
            $query->where(function ($q) use ($user) {
                $q->where('status', 'pending_creative')
                    ->orWhere('approved_coordinator_by', $user->id)
                    ->orWhere('pengaju_id', $user->id);
            });
        } elseif ($isHos) {
            $assignedPendingIds = InfluencerPengajuan::query()
                ->whereIn('status', ['pending_creative', 'pending_hos1'])
                ->with('pengaju.employee')
                ->get()
                ->filter(fn (InfluencerPengajuan $item) => InfluencerPengajuanRouting::isAssignedToHeadOfStore($item, $user))
                ->pluck('id');

            $query->where(function ($q) use ($assignedPendingIds) {
                $q->whereIn('id', $assignedPendingIds)
                  ->orWhere('approved_hos1_by', auth()->id());
            });
        } elseif ($isGm) {
            $query->where(function ($q) {
                $q->where('status', 'pending_creative')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending_hos1')
                            ->whereNotNull('approved_coordinator_by');
                    })
                    ->orWhere('status', 'pending_gm')
                    ->orWhere('approved_gm_by', auth()->id());
            });
        } else {
            $query->where('pengaju_id', $user->id);
        }

        $items = $query->latest()->paginate(10);

        $stats = [
            'total' => InfluencerPengajuan::count(),
            'pending_creative' => InfluencerPengajuan::where('status', 'pending_creative')->count(),
            'pending_hos1' => InfluencerPengajuan::where('status', 'pending_hos1')->count(),
            'pending_gm' => InfluencerPengajuan::where('status', 'pending_gm')->count(),
            'approved' => InfluencerPengajuan::where('status', 'approved')->count(),
        ];

        return view('livewire.influencer-pengajuan-table', compact('items', 'stats'));
    }
}
