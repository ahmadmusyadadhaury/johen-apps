<?php

namespace App\Livewire;

use App\Models\Influencer;
use App\Models\InfluencerPembayaran;
use App\Models\InfluencerPengajuan;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class InfluencerPengajuanTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editId = null;

    public string $no_kontrak = '';
    public string $nama = '';
    public string $mulai_kontrak = '';
    public string $habis_kontrak = '';
    public string $link_sosmed = '';
    public string $biaya = '';

    public string $alasanTolak = '';
    public ?int $tolakId = null;

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'mulai_kontrak' => 'required|date',
            'habis_kontrak' => 'required|date|after_or_equal:mulai_kontrak',
            'link_sosmed' => 'nullable|string|max:500',
            'biaya' => 'nullable|numeric|min:0',
        ];
    }

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama influencer wajib diisi.',
            'mulai_kontrak.required' => 'Mulai kontrak wajib diisi.',
            'habis_kontrak.required' => 'Habis kontrak wajib diisi.',
            'habis_kontrak.after_or_equal' => 'Habis kontrak harus setelah atau sama dengan mulai kontrak.',
        ];
    }

    public function openNew(): void
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        InfluencerPengajuan::create([
            'no_kontrak' => '',
            'nama' => $this->nama,
            'mulai_kontrak' => $this->mulai_kontrak,
            'habis_kontrak' => $this->habis_kontrak,
            'link_sosmed' => $this->link_sosmed ?: null,
            'biaya' => $this->biaya ?: null,
            'status' => 'pending_hos1',
            'pengaju_id' => auth()->id(),
        ]);

        session()->flash('message', 'Pengajuan influencer berhasil dikirim, menunggu persetujuan Head of Store 1.');
        $this->close();
    }

    public function approve(int $id): void
    {
        $pengajuan = InfluencerPengajuan::findOrFail($id);
        $user = auth()->user();

        $isHos1 = $this->isHeadOfStore1($user);
        $isGm = $user->isGmCeo();

        if ($isHos1 && $pengajuan->status === 'pending_hos1') {
            $pengajuan->update([
                'status' => 'pending_gm',
                'approved_hos1_by' => $user->id,
                'approved_hos1_at' => now(),
            ]);
            session()->flash('message', 'Pengajuan disetujui, menunggu persetujuan General Manager.');
        } elseif ($isGm && $pengajuan->status === 'pending_gm') {
            $pengajuan->update([
                'status' => 'approved',
                'approved_gm_by' => $user->id,
                'approved_gm_at' => now(),
            ]);

            $influencer = Influencer::create([
                'no_kontrak' => null,
                'nama' => $pengajuan->nama,
                'mulai_kontrak' => $pengajuan->mulai_kontrak,
                'habis_kontrak' => $pengajuan->habis_kontrak,
                'link_sosmed' => $pengajuan->link_sosmed,
                'biaya' => $pengajuan->biaya,
            ]);
            $this->generatePayments($influencer);

            session()->flash('message', 'Pengajuan disetujui. Data influencer dan pembayaran otomatis dibuat.');
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
        $this->validate([
            'alasanTolak' => 'required|string|min:5',
        ]);

        $pengajuan = InfluencerPengajuan::findOrFail($id);
        $user = auth()->user();

        $pengajuan->update([
            'status' => 'rejected',
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'alasan_penolakan' => $this->alasanTolak,
        ]);

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
        $this->mulai_kontrak = '';
        $this->habis_kontrak = '';
        $this->link_sosmed = '';
        $this->biaya = '';
        $this->alasanTolak = '';
        $this->tolakId = null;
        $this->resetErrorBag();
    }

    public function isHeadOfStore1(User $user): bool
    {
        $employee = $user->employee;
        if (!$employee) return false;
        $position = $employee->mainPosition();
        if (!$position) return false;
        return $position->nama === 'Head of Store 1';
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

    public function render()
    {
        $user = auth()->user();
        $isHos1 = $this->isHeadOfStore1($user);
        $isGm = $user->isGmCeo();

        $query = InfluencerPengajuan::with('pengaju', 'approverHos1', 'approverGm', 'rejector');

        if ($user->isSuperAdminLike()) {
            // lihat semua
        } elseif ($isHos1) {
            $query->where(function ($q) {
                $q->where('status', 'pending_hos1')
                  ->orWhere('approved_hos1_by', auth()->id());
            });
        } elseif ($isGm) {
            $query->where(function ($q) {
                $q->where('status', 'pending_gm')
                  ->orWhere('approved_gm_by', auth()->id());
            });
        } else {
            $query->where('pengaju_id', $user->id);
        }

        $items = $query->latest()->paginate(10);

        $stats = [
            'total' => InfluencerPengajuan::count(),
            'pending_hos1' => InfluencerPengajuan::where('status', 'pending_hos1')->count(),
            'pending_gm' => InfluencerPengajuan::where('status', 'pending_gm')->count(),
            'approved' => InfluencerPengajuan::where('status', 'approved')->count(),
        ];

        return view('livewire.influencer-pengajuan-table', compact('items', 'stats'));
    }
}
