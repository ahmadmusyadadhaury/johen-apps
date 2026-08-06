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

    public string $no_kontrak = '';
    public string $nama = '';
    public string $mulai_kontrak = '';
    public string $habis_kontrak = '';
    public string $link_sosmed = '';
    public string $biaya = '';

    protected function rules(): array
    {
        return [
            'no_kontrak' => 'nullable|string|max:255',
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
            'no_kontrak.required' => 'No. Kontrak wajib diisi.',
            'nama.required' => 'Nama influencer wajib diisi.',
            'mulai_kontrak.required' => 'Mulai kontrak wajib diisi.',
            'habis_kontrak.required' => 'Habis kontrak wajib diisi.',
            'habis_kontrak.after_or_equal' => 'Habis kontrak harus setelah atau sama dengan mulai kontrak.',
        ];
    }

    public function openNew(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->resetInput();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $item = Influencer::findOrFail($id);
        $this->editId = $item->id;
        $this->no_kontrak = $item->no_kontrak;
        $this->nama = $item->nama;
        $this->mulai_kontrak = $item->mulai_kontrak->format('Y-m-d');
        $this->habis_kontrak = $item->habis_kontrak->format('Y-m-d');
        $this->link_sosmed = $item->link_sosmed ?? '';
        $this->biaya = $item->biaya ? (string) $item->biaya : '';
        $this->showModal = true;
    }

    public function save(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate();

        if ($this->editId) {
            $item = Influencer::findOrFail($this->editId);
            $item->update([
                'no_kontrak' => $this->no_kontrak,
                'nama' => $this->nama,
                'mulai_kontrak' => $this->mulai_kontrak,
                'habis_kontrak' => $this->habis_kontrak,
                'link_sosmed' => $this->link_sosmed ?: null,
                'biaya' => $this->biaya ?: null,
            ]);
            session()->flash('message', 'Data influencer berhasil diperbarui.');
        } else {
            $influencer = Influencer::create([
                'no_kontrak' => $this->no_kontrak,
                'nama' => $this->nama,
                'mulai_kontrak' => $this->mulai_kontrak,
                'habis_kontrak' => $this->habis_kontrak,
                'link_sosmed' => $this->link_sosmed ?: null,
                'biaya' => $this->biaya ?: null,
            ]);
            $this->generatePayments($influencer);
            session()->flash('message', 'Data influencer berhasil ditambahkan.');
        }

        $this->close();
    }

    public function delete(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        Influencer::findOrFail($id)->delete();
        session()->flash('message', 'Data influencer berhasil dihapus.');
    }

    public function openPaymentModal(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
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
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
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
        $this->link_sosmed = '';
        $this->biaya = '';
        $this->resetErrorBag();
    }

    public function render()
    {
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
            'upcomingPayments', 'paymentRecords',
        ));
    }
}
