<?php

namespace App\Livewire;

use App\Models\Pengarsipan;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PengarsipanTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showModal = false;
    public ?int $editId = null;

    public string $jenis = 'surat_edaran';
    public string $nomor = '';
    public string $judul = '';
    public string $tanggal_surat = '';
    public string $keterangan = '';
    public $file = null;

    public function openNew(): void
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $arsip = Pengarsipan::findOrFail($id);
        $this->editId = $arsip->id;
        $this->jenis = $arsip->jenis;
        $this->nomor = $arsip->nomor ?? '';
        $this->judul = $arsip->judul;
        $this->tanggal_surat = $arsip->tanggal_surat ? $arsip->tanggal_surat->format('Y-m-d') : '';
        $this->keterangan = $arsip->keterangan ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'jenis' => ['required', 'in:surat_edaran,surat_keputusan,pemberitahuan'],
            'nomor' => ['nullable', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'file' => $this->editId
                ? ['nullable', 'file', 'mimes:pdf', 'max:10240']
                : ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $data = [
            'jenis' => $this->jenis,
            'nomor' => $this->nomor ?: null,
            'judul' => $this->judul,
            'tanggal_surat' => $this->tanggal_surat,
            'keterangan' => $this->keterangan ?: null,
        ];

        if ($this->file) {
            $data['file'] = $this->file->store('pengarsipan', 'public');
        }

        if ($this->editId) {
            $arsip = Pengarsipan::findOrFail($this->editId);
            $arsip->update($data);
            $this->dispatch('notify', type: 'success', message: 'Arsip berhasil diperbarui.');
        } else {
            Pengarsipan::create($data);
            $this->dispatch('notify', type: 'success', message: 'Arsip berhasil ditambahkan.');
        }

        $this->resetInput();
        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        $arsip = Pengarsipan::findOrFail($id);
        if ($arsip->file) Storage::disk('public')->delete($arsip->file);
        $arsip->delete();
        $this->dispatch('notify', type: 'success', message: 'Arsip berhasil dihapus.');
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetInput();
    }

    private function resetInput(): void
    {
        $this->editId = null;
        $this->jenis = 'surat_edaran';
        $this->nomor = '';
        $this->judul = '';
        $this->tanggal_surat = '';
        $this->keterangan = '';
        $this->file = null;
    }

    public function render()
    {
        $arsips = Pengarsipan::latest()->paginate(10);

        $stats = Pengarsipan::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COALESCE(SUM(jenis = 'surat_edaran'), 0) as surat_edaran")
            ->selectRaw("COALESCE(SUM(jenis = 'surat_keputusan'), 0) as surat_keputusan")
            ->selectRaw("COALESCE(SUM(jenis = 'pemberitahuan'), 0) as pemberitahuan")
            ->first();

        return view('livewire.pengarsipan-table', [
            'arsips' => $arsips,
            'stats' => $stats,
        ]);
    }
}