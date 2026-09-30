<?php

namespace App\Livewire;

use App\Models\Influencer;
use App\Models\InfluencerPembayaran;
use App\Models\InfluencerPengajuan;
use App\Models\InfluencerMonitoring;
use App\Support\InfluencerPengajuanRouting;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InfluencerTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editId = null;

    public bool $showPaymentModal = false;
    public bool $showMonitoringModal = false;
    public ?int $monitoringInfluencerId = null;
    public string $monitoringMonth = '';
    public string $monitoringFollowers = '';
    public string $monitoringViewers = '';
    public string $monitoringDuration = '';
    public string $monitoringTargetDuration = '130';
    public string $monitoringNotes = '';
    public string $monitoringBenefits = '';
    public ?int $paymentInfluencerId = null;
    public bool $showDeleteConfirmation = false;
    public ?int $deleteInfluencerId = null;
    public string $deleteInfluencerName = '';

    public string $no_kontrak = '';
    public string $nama = '';
    public string $mulai_kontrak = '';
    public string $habis_kontrak = '';
    public string $divisi = '';
    public string $rekomendasiLamaKontrak = '';
    public string $keterangan = '';
    public string $link_sosmed = '';
    public string $biaya = '';

    public string $activeTab = 'monitoring';

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

    public function mount(): void
    {
        if (auth()->user()->isGmCeo() && $this->isCreativeWorkspace()) {
            $this->activeTab = 'pengajuan';
        } elseif (auth()->user()->isKoordinatorCreative() && request()->query('tab') === 'pengajuan') {
            $this->activeTab = 'pengajuan';
        } elseif (auth()->user()->isHeadOfStore() && (request()->query('tab') === 'pengajuan' || request()->routeIs('hris.influencer-pengajuan'))) {
            $this->activeTab = 'pengajuan';
        }
    }

    public function switchTab(string $tab): void
    {
        abort_unless($this->canSeeSubmissionTab() || $this->isKolSubmitter(), 403);
        abort_unless(in_array($tab, ['monitoring', 'pengajuan'], true), 404);

        $this->activeTab = $tab;
    }

    #[On('influencer-pengajuan-updated')]
    public function refreshSubmissionNotifications(): void
    {
        // Re-render the tab badge after submission or approval changes.
    }

    private function isCreativeWorkspace(): bool
    {
        if (!auth()->user()->isGmCeo() || !session()->has('division_menu')) {
            return false;
        }

        return \App\Models\Division::query()
            ->whereKey(session('division_menu'))
            ->whereRaw('LOWER(nama) = ?', ['creative'])
            ->exists();
    }

    private function canSeeSubmissionTab(): bool
    {
        return auth()->user()->isKoordinatorCreative()
            || auth()->user()->isHeadOfStore()
            || $this->isCreativeWorkspace();
    }

    protected function rules(): array
    {
        return [
            'no_kontrak' => 'nullable|string|max:255',
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
            'no_kontrak.required' => 'No. Kontrak wajib diisi.',
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
        $this->authorizeCreate();
        $this->resetInput();
        $this->showModal = true;
    }

    #[On('open-kol-influencer-request')]
    public function openKolRequestFromSubmissionTab(): void
    {
        abort_unless($this->isKolSubmitter(), 403);
        $this->activeTab = 'pengajuan';
        $this->openNew();
    }

    public function openEdit(int $id): void
    {
        $this->authorizeEdit();
        $item = Influencer::findOrFail($id);
        $this->editId = $item->id;
        $this->no_kontrak = $item->no_kontrak;
        $this->nama = $item->nama;
        $this->mulai_kontrak = $item->mulai_kontrak->format('Y-m-d');
        $this->habis_kontrak = $item->habis_kontrak->format('Y-m-d');
        $this->divisi = $item->divisi ?? '';
        $this->rekomendasiLamaKontrak = (string) max(1, $item->mulai_kontrak->diffInMonths($item->habis_kontrak) + 1);
        $this->link_sosmed = $item->link_sosmed ?? '';
        $this->biaya = $item->biaya ? (string) $item->biaya : '';
        $this->keterangan = $item->keterangan ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->editId ? $this->authorizeEdit() : $this->authorizeCreate();
        $this->validate();

        if (!$this->editId && $this->isKolSubmitter()) {
            $assignedHos = InfluencerPengajuanRouting::headOfStorePositionForDivision($this->divisi);
            abort_unless($assignedHos, 422, 'Head of Store untuk divisi ini belum tersedia di struktur organisasi.');

            InfluencerPengajuan::create([
                'no_kontrak' => '',
                'nama' => $this->nama,
                'divisi' => $this->divisi,
                'rekomendasi_lama_kontrak' => $this->rekomendasiLamaKontrak,
                'link_sosmed' => $this->link_sosmed ?: null,
                'biaya' => $this->biaya ?: null,
                'keterangan' => $this->keterangan ?: null,
                'status' => 'pending_creative',
                'pengaju_id' => auth()->id(),
                'assigned_hos_position_id' => $assignedHos->id,
            ]);

            $this->dispatch('influencer-pengajuan-updated');
            session()->flash('message', 'Pengajuan influencer berhasil dikirim dan menunggu persetujuan Koordinator Creative.');
            $this->close();
            return;
        }

        $contractStart = $this->editId && $this->mulai_kontrak
            ? \Illuminate\Support\Carbon::parse($this->mulai_kontrak)->startOfDay()
            : now()->startOfDay();
        $contractEnd = $contractStart->copy()->addMonthsNoOverflow((int) $this->rekomendasiLamaKontrak - 1);

        if ($this->editId) {
            $item = Influencer::findOrFail($this->editId);
            $item->update([
                'no_kontrak' => $this->no_kontrak ?: null,
                'nama' => $this->nama,
                'divisi' => $this->divisi,
                'mulai_kontrak' => $contractStart,
                'habis_kontrak' => $contractEnd,
                'link_sosmed' => $this->link_sosmed ?: null,
                'biaya' => $this->biaya ?: null,
                'keterangan' => $this->keterangan ?: null,
            ]);
            session()->flash('message', 'Data influencer berhasil diperbarui.');
        } else {
            $influencer = Influencer::create([
                'no_kontrak' => $this->no_kontrak ?: null,
                'nama' => $this->nama,
                'divisi' => $this->divisi,
                'mulai_kontrak' => $contractStart,
                'habis_kontrak' => $contractEnd,
                'link_sosmed' => $this->link_sosmed ?: null,
                'biaya' => $this->biaya ?: null,
                'keterangan' => $this->keterangan ?: null,
            ]);
            $this->generatePayments($influencer);
            session()->flash('message', 'Data influencer berhasil ditambahkan.');
        }

        $this->close();
    }

    public function delete(int $id): void
    {
        $this->authorizeEdit();
        $influencer = Influencer::findOrFail($id);
        $this->deleteInfluencerId = $influencer->id;
        $this->deleteInfluencerName = $influencer->nama;
        $this->showDeleteConfirmation = true;
    }

    public function deleteConfirmed(): void
    {
        $this->authorizeEdit();
        abort_unless($this->deleteInfluencerId, 404);
        $influencer = Influencer::findOrFail($this->deleteInfluencerId);
        $influencer->delete();
        $this->showDeleteConfirmation = false;
        $this->deleteInfluencerId = null;
        $this->deleteInfluencerName = '';
        session()->flash('message', 'Data influencer berhasil dihapus.');
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirmation = false;
        $this->deleteInfluencerId = null;
        $this->deleteInfluencerName = '';
    }

    public function openPaymentModal(int $id): void
    {
        $this->authorizePaymentManagement();
        $this->paymentInfluencerId = $id;
        $influencer = Influencer::find($id);
        if ($influencer && $influencer->payments()->count() === 0) {
            $this->generatePayments($influencer);
        }
        $this->showPaymentModal = true;
    }

    public function openMonitoring(int $id): void
    {
        abort_unless($this->canViewInfluencerMonitoring(), 403);
        $influencer = Influencer::findOrFail($id);
        abort_unless(!$this->isKolSubmitter() || $this->isOwnApprovedKolInfluencer($influencer->id), 403);
        $this->monitoringInfluencerId = $influencer->id;
        $latestMonitoring = $influencer->latestMonitoring;
        $this->monitoringMonth = $this->isKolSubmitter() || !$latestMonitoring
            ? now()->format('Y-m')
            : $latestMonitoring->period_month->format('Y-m');
        $this->monitoringFollowers = '';
        $this->monitoringViewers = '';
        $this->monitoringDuration = '';
        $this->monitoringTargetDuration = '130';
        $this->monitoringNotes = '';
        $this->monitoringBenefits = '';
        $this->loadMonitoringMonth();
        $this->showMonitoringModal = true;
    }

    public function updatedMonitoringMonth(): void
    {
        $this->loadMonitoringMonth();
    }

    private function loadMonitoringMonth(): void
    {
        if (!$this->monitoringInfluencerId || !preg_match('/^\\d{4}-\\d{2}$/', $this->monitoringMonth)) {
            return;
        }

        $this->monitoringFollowers = '';
        $this->monitoringViewers = '';
        $this->monitoringDuration = '';
        $this->monitoringTargetDuration = '130';
        $this->monitoringNotes = '';
        $this->monitoringBenefits = '';

        $record = InfluencerMonitoring::query()
            ->where('influencer_id', $this->monitoringInfluencerId)
            ->whereDate('period_month', $this->monitoringMonth.'-01')
            ->first();

        if (!$record) {
            return;
        }

        $this->monitoringFollowers = (string) $record->followers;
        $this->monitoringViewers = (string) $record->viewers_last_month;
        $this->monitoringDuration = (string) $record->duration_hours;
        $this->monitoringTargetDuration = (string) $record->target_duration_hours;
        $this->monitoringNotes = $record->notes ?? '';
        $this->monitoringBenefits = $record->benefits ?? '';
    }

    public function formatAudienceCount(int $count): string
    {
        if ($count >= 1_000_000) {
            return number_format($count / 1_000_000, 1, ',', '.').' M';
        }

        if ($count >= 1_000) {
            return number_format($count / 1_000, 1, ',', '.').' K';
        }

        return number_format($count, 0, ',', '.');
    }

    public function saveMonitoring(): void
    {
        abort_unless($this->isKolSubmitter(), 403);
        abort_unless($this->monitoringInfluencerId, 404);
        abort_unless($this->isOwnApprovedKolInfluencer($this->monitoringInfluencerId), 403);

        $validated = $this->validate([
            'monitoringMonth' => 'required|date_format:Y-m',
            'monitoringFollowers' => 'required|integer|min:0',
            'monitoringViewers' => 'required|integer|min:0',
            'monitoringDuration' => 'required|numeric|min:0|max:10000',
            'monitoringTargetDuration' => 'required|numeric|min:0|max:10000',
            'monitoringNotes' => 'nullable|string|max:2000',
            'monitoringBenefits' => 'nullable|string|max:5000',
        ]);

        InfluencerMonitoring::updateOrCreate(
            [
                'influencer_id' => $this->monitoringInfluencerId,
                'period_month' => $this->monitoringMonth.'-01',
            ],
            [
                'followers' => $validated['monitoringFollowers'],
                'viewers_last_month' => $validated['monitoringViewers'],
                'duration_hours' => $validated['monitoringDuration'],
                'target_duration_hours' => $validated['monitoringTargetDuration'],
                'notes' => $validated['monitoringNotes'] ?: null,
                'benefits' => $validated['monitoringBenefits'] ?: null,
            ]
        );

        $this->showMonitoringModal = false;
        $this->monitoringInfluencerId = null;
        session()->flash('message', 'Monitoring influencer berhasil disimpan.');
    }

    public function closeMonitoring(): void
    {
        $this->showMonitoringModal = false;
        $this->monitoringInfluencerId = null;
        $this->resetErrorBag();
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->paymentInfluencerId = null;
    }

    public function markAsPaid(int $paymentId): void
    {
        $this->authorizePaymentManagement();
        $payment = InfluencerPembayaran::findOrFail($paymentId);
        $payment->update([
            'status' => 'lunas',
            'tanggal_bayar' => now()->format('Y-m-d'),
        ]);
        $this->dispatch('notify', type: 'success', message: 'Pembayaran ditandai lunas.');
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
        $this->mulai_kontrak = '';
        $this->habis_kontrak = '';
        $this->divisi = '';
        $this->rekomendasiLamaKontrak = '';
        $this->keterangan = '';
        $this->link_sosmed = '';
        $this->biaya = '';
        $this->resetErrorBag();
    }

    private function authorizeEdit(): void
    {
        abort_unless(! auth()->user()->isReadOnlyWorkspace() && !auth()->user()->isHeadOfStore(), 403);
    }

    private function authorizeCreate(): void
    {
        $user = auth()->user();

        abort_unless(! $user->isReadOnlyWorkspace() && ! $user->isKoordinatorCreative() && !$user->isHeadOfStore(), 403);
    }

    private function isKolSubmitter(): bool
    {
        $user = auth()->user();
        $positionName = $user->employee?->mainPosition()?->nama ?? $user->employee?->position ?? '';

        return $user->isStaffCreative() && str_starts_with($positionName, 'Admin KOL');
    }

    private function canViewInfluencerMonitoring(): bool
    {
        $user = auth()->user();

        return $this->isKolSubmitter()
            || $user->isKoordinatorCreative()
            || $user->isHeadOfStore()
            || $user->isGmCeo()
            || $user->isSuperAdminLike();
    }

    private function isOwnApprovedKolInfluencer(int $influencerId): bool
    {
        return InfluencerPengajuan::query()
            ->where('pengaju_id', auth()->id())
            ->where('influencer_id', $influencerId)
            ->where('status', 'approved')
            ->exists();
    }

    private function authorizePaymentManagement(): void
    {
        $user = auth()->user();

        abort_unless(
            ! $user->isReadOnlyWorkspace() && ($user->canSeeBiaya() || $user->isKoordinatorCreative()),
            403,
        );
    }

    public function render()
    {
        $isKolSubmitter = $this->isKolSubmitter();
        $showRequestTabs = $this->canSeeSubmissionTab() || $isKolSubmitter;
        $canViewMonitoring = $this->canViewInfluencerMonitoring();
        $pendingActionCount = match (true) {
            auth()->user()->isKoordinatorCreative() => InfluencerPengajuanRouting::pendingCountForCoordinator(),
            auth()->user()->isHeadOfStore() => InfluencerPengajuanRouting::pendingCountForHeadOfStore(auth()->user()),
            auth()->user()->isGmCeo() => InfluencerPengajuanRouting::pendingCountForGeneralManager(),
            default => 0,
        };
        if ($this->showMonitoringModal && (
            !$canViewMonitoring
            || !$this->monitoringInfluencerId
            || ($isKolSubmitter && !$this->isOwnApprovedKolInfluencer($this->monitoringInfluencerId))
        )) {
            $this->showMonitoringModal = false;
            $this->monitoringInfluencerId = null;
        }
        $items = Influencer::with(['payments', 'latestMonitoring'])->latest()->paginate(10);
        $kolApprovedSubmissions = $isKolSubmitter
            ? InfluencerPengajuan::with('influencer.latestMonitoring')
                ->where('pengaju_id', auth()->id())
                ->where('status', 'approved')
                ->whereNotNull('influencer_id')
                ->whereHas('influencer')
                ->latest()
                ->paginate(10, ['*'], 'kolMonitoringPage')
            : collect();
        $monitoringHistory = $this->monitoringInfluencerId
            ? InfluencerMonitoring::where('influencer_id', $this->monitoringInfluencerId)->orderByDesc('period_month')->get()
            : collect();

        $now = now()->startOfDay();
        $aktifCount = Influencer::where('habis_kontrak', '>', $now)->count();
        $segeraHabisCount = Influencer::where('habis_kontrak', '>', $now)
            ->where('habis_kontrak', '<=', $now->copy()->addDays(7))
            ->count();
        $tidakAktifCount = Influencer::where('habis_kontrak', '<=', $now)->count();

        $upcomingPayments = InfluencerPembayaran::with('influencer')
            ->where('status', 'pending')
            ->whereBetween('tanggal_jatuh_tempo', [$now, $now->copy()->addDays(7)])
            ->get();

        $paymentRecords = $this->paymentInfluencerId
            ? InfluencerPembayaran::where('influencer_id', $this->paymentInfluencerId)
                ->orderBy('bulan_ke')
                ->get()
            : collect();

        return view('livewire.influencer-table', compact(
            'items', 'aktifCount', 'segeraHabisCount', 'tidakAktifCount',
            'upcomingPayments', 'paymentRecords', 'showRequestTabs', 'monitoringHistory',
            'kolApprovedSubmissions', 'isKolSubmitter', 'canViewMonitoring', 'pendingActionCount',
        ));
    }
}
