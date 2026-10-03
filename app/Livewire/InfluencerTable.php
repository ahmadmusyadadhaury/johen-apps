<?php

namespace App\Livewire;

use App\Models\Influencer;
use App\Models\InfluencerPembayaran;
use App\Models\InfluencerPengajuan;
use App\Models\InfluencerMonitoring;
use App\Services\InfluencerDeletionService;
use App\Support\InfluencerPengajuanRouting;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InfluencerTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public string $kolFormTab = 'pengajuan';
    public ?int $editId = null;

    public bool $showPaymentModal = false;
    public bool $showMonitoringModal = false;
    public bool $showExtensionModal = false;
    public ?int $extensionInfluencerId = null;
    public string $extensionDuration = '';
    public string $extensionCost = '';
    public string $extensionNotes = '';
    public ?int $monitoringInfluencerId = null;
    public string $monitoringMonth = '';
    public string $monitoringFollowers = '';
    public string $monitoringViewers = '';
    public string $monitoringDuration = '';
    public string $monitoringTargetDuration = '130';
    public string $monitoringNotes = '';
    public string $monitoringBenefits = '';
    public string $monitoringModalView = 'detail';
    public bool $monitoringReadOnly = false;
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
    public string $initialMonitoringMonth = '';
    public string $initialMonitoringFollowers = '';
    public string $initialMonitoringViewers = '';
    public string $initialMonitoringDuration = '';
    public string $initialMonitoringTargetDuration = '130';
    public string $initialMonitoringNotes = '';
    public string $initialMonitoringBenefits = '';

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
        $monitoringRequired = filled($this->initialMonitoringMonth);
        $divisionOptions = InfluencerPengajuanRouting::kolDivisionsForUser(auth()->user()) ?? self::DIVISI_OPTIONS;

        return [
            'no_kontrak' => 'nullable|string|max:255',
            'nama' => 'required|string|max:255',
            'divisi' => ['required', 'in:'.implode(',', $divisionOptions)],
            'rekomendasiLamaKontrak' => 'required|integer|min:1|max:60',
            'link_sosmed' => 'nullable|string|max:500',
            'biaya' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'initialMonitoringMonth' => ['nullable', 'date_format:Y-m', 'before_or_equal:'.now()->format('Y-m').'-01'],
            'initialMonitoringFollowers' => [$monitoringRequired ? 'required' : 'nullable', 'integer', 'min:0'],
            'initialMonitoringViewers' => [$monitoringRequired ? 'required' : 'nullable', 'integer', 'min:0'],
            'initialMonitoringDuration' => [$monitoringRequired ? 'required' : 'nullable', 'numeric', 'min:0', 'max:10000'],
            'initialMonitoringTargetDuration' => [$monitoringRequired ? 'required' : 'nullable', 'numeric', 'min:0', 'max:10000'],
            'initialMonitoringNotes' => 'nullable|string|max:2000',
            'initialMonitoringBenefits' => 'nullable|string|max:5000',
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
            'initialMonitoringMonth.date_format' => 'Bulan monitoring harus berformat bulan dan tahun.',
            'initialMonitoringMonth.before_or_equal' => 'Bulan monitoring tidak boleh berada di masa depan.',
            'initialMonitoringFollowers.required' => 'Followers wajib diisi bila bulan monitoring dipilih.',
            'initialMonitoringFollowers.integer' => 'Followers harus berupa angka.',
            'initialMonitoringViewers.required' => 'Viewers wajib diisi bila bulan monitoring dipilih.',
            'initialMonitoringViewers.integer' => 'Viewers harus berupa angka.',
            'initialMonitoringDuration.required' => 'Durasi wajib diisi bila bulan monitoring dipilih.',
            'initialMonitoringDuration.numeric' => 'Durasi harus berupa angka.',
            'initialMonitoringTargetDuration.required' => 'Target durasi wajib diisi bila bulan monitoring dipilih.',
            'initialMonitoringTargetDuration.numeric' => 'Target durasi harus berupa angka.',
        ];
    }

    public function openNew(): void
    {
        $this->authorizeCreate();
        $this->resetInput();
        $this->kolFormTab = 'pengajuan';
        $this->initialMonitoringMonth = now()->format('Y-m');
        $this->showModal = true;
    }

    public function switchKolFormTab(string $tab): void
    {
        abort_unless(in_array($tab, ['pengajuan', 'monitoring'], true), 404);
        abort_unless($this->isKolSubmitter() && ! $this->editId, 403);

        $this->kolFormTab = $tab;
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
                ...$this->initialMonitoringPayload(),
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
        $this->authorizeDeletion();
        $influencer = Influencer::findOrFail($id);
        $this->deleteInfluencerId = $influencer->id;
        $this->deleteInfluencerName = $influencer->nama;
        $this->showDeleteConfirmation = true;
    }

    public function deleteConfirmed(): void
    {
        $this->authorizeDeletion();
        abort_unless($this->deleteInfluencerId, 404);
        $influencer = Influencer::findOrFail($this->deleteInfluencerId);
        app(InfluencerDeletionService::class)->purgeInfluencer($influencer->id);
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
        $this->monitoringModalView = $this->isKolSubmitter() ? 'months' : 'detail';
        $this->monitoringReadOnly = false;
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

    public function selectMonitoringMonth(string $month): void
    {
        abort_unless($this->isKolSubmitter(), 403);
        abort_unless($this->monitoringInfluencerId && $this->isOwnApprovedKolInfluencer($this->monitoringInfluencerId), 403);
        abort_unless(preg_match('/^\\d{4}-\\d{2}$/', $month), 422);

        $this->monitoringMonth = $month;
        $this->loadMonitoringMonth();
        $this->monitoringReadOnly = true;
        $this->monitoringModalView = 'detail';
    }

    public function addMonitoringForCurrentMonth(): void
    {
        abort_unless($this->isKolSubmitter(), 403);
        abort_unless($this->monitoringInfluencerId && $this->isOwnApprovedKolInfluencer($this->monitoringInfluencerId), 403);

        $this->monitoringMonth = now()->format('Y-m');
        $this->monitoringFollowers = '';
        $this->monitoringViewers = '';
        $this->monitoringDuration = '';
        $this->monitoringTargetDuration = '';
        $this->monitoringNotes = '';
        $this->monitoringBenefits = '';
        $this->resetValidation();
        $this->monitoringReadOnly = false;
        $this->monitoringModalView = 'detail';
    }

    public function showMonitoringMonths(): void
    {
        abort_unless($this->isKolSubmitter(), 403);
        $this->monitoringModalView = 'months';
    }

    public function openExtensionRequest(int $id): void
    {
        abort_unless($this->isKolSubmitter(), 403);
        abort_unless($this->isOwnApprovedKolInfluencer($id), 403);

        $influencer = Influencer::findOrFail($id);
        $assignedHos = InfluencerPengajuanRouting::headOfStorePositionForDivision($influencer->divisi);
        abort_unless($assignedHos, 422, 'Head of Store untuk divisi ini belum tersedia di struktur organisasi.');

        $this->extensionInfluencerId = $influencer->id;
        $this->extensionDuration = '';
        $this->extensionCost = $influencer->biaya !== null ? (string) $influencer->biaya : '';
        $this->extensionNotes = $influencer->keterangan ?? '';
        $this->resetValidation();
        $this->showMonitoringModal = false;
        $this->showExtensionModal = true;
    }

    public function submitExtensionRequest(): void
    {
        abort_unless($this->isKolSubmitter(), 403);
        abort_unless($this->extensionInfluencerId && $this->isOwnApprovedKolInfluencer($this->extensionInfluencerId), 403);

        $validated = $this->validate([
            'extensionDuration' => 'required|integer|min:1|max:60',
            'extensionCost' => 'nullable|numeric|min:0',
            'extensionNotes' => 'nullable|string|max:1000',
        ]);

        $influencer = Influencer::findOrFail($this->extensionInfluencerId);
        $assignedHos = InfluencerPengajuanRouting::headOfStorePositionForDivision($influencer->divisi);
        abort_unless($assignedHos, 422, 'Head of Store untuk divisi ini belum tersedia di struktur organisasi.');

        InfluencerPengajuan::create([
            'no_kontrak' => $influencer->no_kontrak ?? '',
            'nama' => $influencer->nama,
            'divisi' => $influencer->divisi,
            'rekomendasi_lama_kontrak' => $validated['extensionDuration'],
            'biaya' => $validated['extensionCost'] !== '' ? ($validated['extensionCost'] ?? null) : null,
            'keterangan' => $validated['extensionNotes'] ?: null,
            'status' => 'pending_creative',
            'pengaju_id' => auth()->id(),
            'influencer_id' => $influencer->id,
            'assigned_hos_position_id' => $assignedHos->id,
            'is_perpanjangan' => true,
        ]);

        $this->dispatch('influencer-pengajuan-updated');
        $this->showExtensionModal = false;
        $this->extensionInfluencerId = null;
        session()->flash('message', 'Pengajuan perpanjangan influencer berhasil dikirim dan menunggu persetujuan Koordinator Creative.');
    }

    public function closeExtensionRequest(): void
    {
        $this->showExtensionModal = false;
        $this->extensionInfluencerId = null;
        $this->extensionDuration = '';
        $this->extensionCost = '';
        $this->extensionNotes = '';
        $this->resetValidation();
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
        $this->dispatch('influencer-monitoring-updated');
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
        $this->initialMonitoringMonth = '';
        $this->initialMonitoringFollowers = '';
        $this->initialMonitoringViewers = '';
        $this->initialMonitoringDuration = '';
        $this->initialMonitoringTargetDuration = '130';
        $this->initialMonitoringNotes = '';
        $this->initialMonitoringBenefits = '';
        $this->resetErrorBag();
    }

    /**
     * Payload monitoring awal yang dititipkan di pengajuan. Disalin menjadi baris
     * InfluencerMonitoring pertama saat GM/CEO menyetujui pengajuan.
     */
    private function initialMonitoringPayload(): array
    {
        return [
            'monitoring_month' => $this->initialMonitoringMonth !== '' ? $this->initialMonitoringMonth.'-01' : null,
            'monitoring_followers' => $this->initialMonitoringFollowers !== '' ? (int) $this->initialMonitoringFollowers : null,
            'monitoring_viewers_last_month' => $this->initialMonitoringViewers !== '' ? (int) $this->initialMonitoringViewers : null,
            'monitoring_duration_hours' => $this->initialMonitoringDuration !== '' ? (float) $this->initialMonitoringDuration : null,
            'monitoring_target_duration_hours' => $this->initialMonitoringTargetDuration !== '' ? (float) $this->initialMonitoringTargetDuration : null,
            'monitoring_notes' => $this->initialMonitoringNotes ?: null,
            'monitoring_benefits' => $this->initialMonitoringBenefits ?: null,
        ];
    }

    private function authorizeEdit(): void
    {
        abort_unless(! auth()->user()->isReadOnlyWorkspace() && !auth()->user()->isHeadOfStore(), 403);
    }

    private function authorizeDeletion(): void
    {
        abort_unless(! auth()->user()->isReadOnlyWorkspace(), 403);
    }

    private function authorizeCreate(): void
    {
        $user = auth()->user();

        abort_unless(! $user->isReadOnlyWorkspace() && ! $user->isKoordinatorCreative() && !$user->isHeadOfStore(), 403);
    }

    private function isKolSubmitter(): bool
    {
        return InfluencerPengajuanRouting::isKolSubmitter(auth()->user());
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
            auth()->user()->isKoordinatorCreative() => InfluencerPengajuanRouting::pendingCountForCoordinator(auth()->user()),
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
        // Admin KOL hanya melihat influencer hasil pengajuannya sendiri.
        $visibleInfluencerIds = $isKolSubmitter
            ? InfluencerPengajuanRouting::approvedInfluencerIdsFor(auth()->user())
            : null;

        $items = Influencer::with(['payments', 'latestMonitoring'])
            ->when($visibleInfluencerIds !== null, fn ($q) => $q->whereIn('id', $visibleInfluencerIds))
            ->latest()
            ->paginate(10);
        $kolApprovedSubmissions = $isKolSubmitter
            ? InfluencerPengajuan::with('influencer.latestMonitoring')
                ->where('pengaju_id', auth()->id())
                ->where('status', 'approved')
                ->where('is_perpanjangan', false)
                ->whereNotNull('influencer_id')
                ->whereHas('influencer')
                ->latest()
                ->paginate(10, ['*'], 'kolMonitoringPage')
            : collect();
        $monitoringHistory = $this->monitoringInfluencerId
            ? InfluencerMonitoring::where('influencer_id', $this->monitoringInfluencerId)->orderByDesc('period_month')->get()
            : collect();

        $now = now()->startOfDay();
        $statsQuery = fn () => Influencer::query()
            ->when($visibleInfluencerIds !== null, fn ($q) => $q->whereIn('id', $visibleInfluencerIds));
        $aktifCount = $statsQuery()->where('habis_kontrak', '>', $now)->count();
        $segeraHabisCount = $statsQuery()->where('habis_kontrak', '>', $now)
            ->where('habis_kontrak', '<=', $now->copy()->addDays(30))
            ->count();
        $tidakAktifCount = $statsQuery()->where('habis_kontrak', '<=', $now)->count();

        $upcomingPayments = InfluencerPembayaran::with('influencer')
            ->where('status', 'pending')
            ->whereBetween('tanggal_jatuh_tempo', [$now, $now->copy()->addDays(7)])
            ->whereHas('influencer')
            ->when($visibleInfluencerIds !== null, fn ($q) => $q->whereIn('influencer_id', $visibleInfluencerIds))
            ->get();

        $paymentRecords = $this->paymentInfluencerId
            ? InfluencerPembayaran::where('influencer_id', $this->paymentInfluencerId)
                ->orderBy('bulan_ke')
                ->get()
            : collect();

        $divisionOptions = InfluencerPengajuanRouting::kolDivisionsForUser(auth()->user()) ?? self::DIVISI_OPTIONS;

        return view('livewire.influencer-table', compact(
            'items', 'aktifCount', 'segeraHabisCount', 'tidakAktifCount',
            'upcomingPayments', 'paymentRecords', 'showRequestTabs', 'monitoringHistory',
            'kolApprovedSubmissions', 'isKolSubmitter', 'canViewMonitoring', 'pendingActionCount', 'divisionOptions',
        ));
    }
}
