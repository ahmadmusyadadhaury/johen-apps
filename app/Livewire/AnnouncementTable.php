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
            $this->dispatch('notify', type: 'success', message: 'Pengumuman berhasil diperbarui.');
        } else {
            Announcement::create($data);
            $this->dispatch('notify', type: 'success', message: 'Pengumuman berhasil ditambahkan.');
        }

        $this->resetInput();
        $this->showModal = false;
    }

    public function delete(int $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        $this->dispatch('notify', type: 'success', message: 'Pengumuman berhasil dihapus.');
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
