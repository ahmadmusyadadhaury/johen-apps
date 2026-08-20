<?php

namespace App\Livewire;

use App\Models\ManualBook;
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
        ]);
    }
}
