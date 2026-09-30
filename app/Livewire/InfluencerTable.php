<?php

namespace App\Livewire;

use App\Models\Influencer;
use App\Models\InfluencerPembayaran;
use Livewire\Component;
use Livewire\WithPagination;

class InfluencerTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editId = null;

    public bool $showPaymentModal = false;
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
        }
    }

    public function switchTab(string $tab): void
    {
        abort_unless($this->canSeeSubmissionTab(), 403);
        abort_unless(in_array($tab, ['monitoring', 'pengajuan'], true), 404);

        $this->activeTab = $tab;
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
        return auth()->user()->isKoordinatorCreative() || $this->isCreativeWorkspace();
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
        abort_unless(! auth()->user()->isReadOnlyWorkspace(), 403);
    }

    private function authorizeCreate(): void
    {
        $user = auth()->user();

        abort_unless(! $user->isReadOnlyWorkspace() && ! $user->isKoordinatorCreative(), 403);
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
        $showRequestTabs = $this->canSeeSubmissionTab();
        $items = Influencer::with('payments')->latest()->paginate(10);

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
            'upcomingPayments', 'paymentRecords', 'showRequestTabs',
        ));
    }
}
