<?php

namespace App\Livewire;

use App\Models\Announcement;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editId = null;

    public bool $showDeleteConfirmModal = false;
    public ?int $deleteId = null;

    public bool $showSuccessModal = false;
    public string $successMessage = '';

    public string $title = '';
    public string $summary = '';
    public string $content = '';
    public bool $is_published = true;

    public function openNew(): void
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $this->editId = $announcement->id;
        $this->title = $announcement->title;
        $this->summary = $announcement->summary ?? '';
        $this->content = $announcement->content ?? '';
        $this->is_published = $announcement->is_published;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $data = [
            'title' => $this->title,
            'summary' => $this->summary ?: null,
            'content' => $this->content ?: null,
            'is_published' => $this->is_published,
        ];

        if ($this->editId) {
            $announcement = Announcement::findOrFail($this->editId);
            $announcement->update($data);
            $this->successMessage = 'Pengumuman berhasil diperbarui.';
        } else {
            Announcement::create($data);
            $this->successMessage = 'Pengumuman berhasil ditambahkan.';
        }

        $this->resetInput();
        $this->showModal = false;
        $this->showSuccessModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteConfirmModal = true;
    }

    public function executeDelete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        $announcement = Announcement::findOrFail($this->deleteId);
        $announcement->delete();

        $this->showDeleteConfirmModal = false;
        $this->deleteId = null;
        $this->successMessage = 'Pengumuman berhasil dihapus.';
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
        $this->title = '';
        $this->summary = '';
        $this->content = '';
        $this->is_published = true;
    }

    public function render()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('livewire.announcement-table', ['announcements' => $announcements]);
    }
}
