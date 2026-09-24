<?php

namespace App\Livewire;

use App\Models\ManualBook;
use App\Models\TrainingVideo;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManualBookTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showModal = false;
    public ?int $editId = null;

    public bool $showDeleteConfirmModal = false;
    public ?int $deleteId = null;

    public bool $showSuccessModal = false;
    public string $successMessage = '';

    public string $filterKategori = '';

    public string $nama = '';
    public string $kategori = '';
    public string $deskripsi = '';
    public $thumbnail = null;
    public $file_pdf = null;
    public $thumbnail_preview = null;

    public bool $showVideoModal = false;
    public ?int $videoEditId = null;

    public bool $showVideoDeleteConfirmModal = false;
    public ?int $videoDeleteId = null;

    public string $videoNama = '';
    public string $videoKategori = '';
    public string $videoUrl = '';

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }

    public function openNew(): void
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $book = ManualBook::findOrFail($id);
        $this->editId = $book->id;
        $this->nama = $book->nama;
        $this->kategori = $book->kategori ?? '';
        $this->deskripsi = $book->deskripsi ?? '';
        $this->thumbnail_preview = $book->thumbnail ? Storage::url($book->thumbnail) : null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'kategori' => ['required', 'in:' . implode(',', ManualBook::KATEGORI_OPTIONS)],
            'deskripsi' => 'nullable|string',
            'thumbnail' => $this->editId ? 'nullable|image|max:2048' : 'nullable|image|max:2048',
            'file_pdf' => $this->editId ? 'nullable|file|mimes:pdf|max:10240' : 'required|file|mimes:pdf|max:10240',
        ];

        $this->validate($rules);

        $data = [
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'deskripsi' => $this->deskripsi ?: null,
        ];

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('manual-books/thumbnails', 'public');
        }

        if ($this->file_pdf) {
            $data['file_pdf'] = $this->file_pdf->store('manual-books/pdf', 'public');
        }

        if ($this->editId) {
            $book = ManualBook::findOrFail($this->editId);
            $book->update($data);
            $this->successMessage = 'Manual book berhasil diperbarui.';
        } else {
            ManualBook::create($data);
            $this->successMessage = 'Manual book berhasil ditambahkan.';
        }

        $this->resetInput();
        $this->showModal = false;
        $this->showSuccessModal = true;
    }

    public function confirmDelete(int $id): void
    {
        if (! auth()->user()->isSuperAdminLike()) {
            abort(403);
        }

        $this->deleteId = $id;
        $this->showDeleteConfirmModal = true;
    }

    public function executeDelete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        $book = ManualBook::findOrFail($this->deleteId);
        if ($book->thumbnail) Storage::disk('public')->delete($book->thumbnail);
        if ($book->file_pdf) Storage::disk('public')->delete($book->file_pdf);
        $book->delete();

        $this->showDeleteConfirmModal = false;
        $this->deleteId = null;
        $this->successMessage = 'Manual book berhasil dihapus.';
        $this->showSuccessModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirmModal = false;
        $this->deleteId = null;
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->successMessage = '';
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetInput();
    }

    public function openVideoNew(): void
    {
        $this->resetVideoInput();
        $this->showVideoModal = true;
    }

    public function openVideoEdit(int $id): void
    {
        $video = TrainingVideo::findOrFail($id);
        $this->videoEditId = $video->id;
        $this->videoNama = $video->nama;
        $this->videoKategori = $video->kategori ?? '';
        $this->videoUrl = $video->url;
        $this->showVideoModal = true;
    }

    public function saveVideo(): void
    {
        $this->validate([
            'videoNama' => 'required|string|max:255',
            'videoKategori' => ['nullable', 'in:' . implode(',', TrainingVideo::KATEGORI_OPTIONS)],
            'videoUrl' => 'required|url|max:255',
        ]);

        $data = [
            'nama' => $this->videoNama,
            'kategori' => $this->videoKategori ?: null,
            'url' => TrainingVideo::toEmbedUrl($this->videoUrl),
        ];

        if ($this->videoEditId) {
            $video = TrainingVideo::findOrFail($this->videoEditId);
            $video->update($data);
            $this->successMessage = 'Video pelatihan berhasil diperbarui.';
        } else {
            TrainingVideo::create($data);
            $this->successMessage = 'Video pelatihan berhasil ditambahkan.';
        }

        $this->resetVideoInput();
        $this->showVideoModal = false;
        $this->showSuccessModal = true;
    }

    public function confirmVideoDelete(int $id): void
    {
        if (! auth()->user()->isSuperAdminLike()) {
            abort(403);
        }

        $this->videoDeleteId = $id;
        $this->showVideoDeleteConfirmModal = true;
    }

    public function executeVideoDelete(): void
    {
        if (! $this->videoDeleteId) {
            return;
        }

        $video = TrainingVideo::findOrFail($this->videoDeleteId);
        $video->delete();

        $this->showVideoDeleteConfirmModal = false;
        $this->videoDeleteId = null;
        $this->successMessage = 'Video pelatihan berhasil dihapus.';
        $this->showSuccessModal = true;
    }

    public function cancelVideoDelete(): void
    {
        $this->showVideoDeleteConfirmModal = false;
        $this->videoDeleteId = null;
    }

    public function closeVideo(): void
    {
        $this->showVideoModal = false;
        $this->resetVideoInput();
    }

    private function resetInput(): void
    {
        $this->editId = null;
        $this->nama = '';
        $this->kategori = '';
        $this->deskripsi = '';
        $this->thumbnail = null;
        $this->file_pdf = null;
        $this->thumbnail_preview = null;
    }

    private function resetVideoInput(): void
    {
        $this->videoEditId = null;
        $this->videoNama = '';
        $this->videoKategori = '';
        $this->videoUrl = '';
    }

    public function render()
    {
        $books = ManualBook::query()
            ->when($this->filterKategori !== '', function ($query) {
                $query->where('kategori', $this->filterKategori);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.manual-book-table', [
            'books' => $books,
            'kategoriOptions' => ManualBook::KATEGORI_OPTIONS,
            'videos' => TrainingVideo::query()->latest()->get(),
            'videoKategoriOptions' => TrainingVideo::KATEGORI_OPTIONS,
        ]);
    }
}
