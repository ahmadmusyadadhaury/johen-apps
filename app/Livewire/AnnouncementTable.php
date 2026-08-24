<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementTable extends Component
{
    use WithPagination;

    protected $queryString = ['search', 'statusFilter'];

    public string $search = '';

    public string $statusFilter = 'semua';

    public bool $showModal = false;
    public ?int $editId = null;

    public bool $showDeleteConfirmModal = false;
    public ?int $deleteId = null;
    public string $deleteTitle = '';

    public string $title = '';
    public string $summary = '';
    public string $content = '';
    public string $event_date = '';
    public string $event_time = '';
    public bool $is_published = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
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
        $announcement = Announcement::findOrFail($id);
        $this->editId = $announcement->id;
        $this->title = $announcement->title;
        $this->summary = $announcement->summary ?? '';
        $this->content = $announcement->content ?? '';
        $this->event_date = $announcement->event_date ?? '';
        $this->event_time = $announcement->event_time ? substr($announcement->event_time, 0, 5) : '';
        $this->is_published = $announcement->is_published;
        $this->showModal = true;
    }

    public function togglePublish(int $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_published' => ! $announcement->is_published]);

        $this->notify(
            'Pengumuman "'.$announcement->title.'" '.($announcement->is_published ? 'ditayangkan' : 'disembunyikan').' di dashboard karyawan.'
        );
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'is_published' => 'boolean',
        ]);

        $data = [
            'title' => $this->title,
            'summary' => $this->summary ?: null,
            'content' => $this->content ?: null,
            'event_date' => $this->event_date ?: null,
            'event_time' => $this->event_time ?: null,
            'is_published' => $this->is_published,
        ];

        if ($this->editId) {
            Announcement::findOrFail($this->editId)->update($data);
            $message = 'Perubahan pengumuman berhasil disimpan.';
        } else {
            Announcement::create($data);
            $message = $this->is_published
                ? 'Pengumuman baru berhasil diterbitkan.'
                : 'Pengumuman baru disimpan sebagai draft.';
        }

        $this->resetInput();
        $this->showModal = false;
        $this->notify($message);
    }

    public function confirmDelete(int $id): void
    {
        $announcement = Announcement::findOrFail($id);
        $this->deleteId = $announcement->id;
        $this->deleteTitle = $announcement->title;
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
        $this->deleteTitle = '';

        $this->notify('Pengumuman berhasil dihapus.');
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirmModal = false;
        $this->deleteId = null;
        $this->deleteTitle = '';
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetInput();
    }

    private function notify(string $message): void
    {
        $this->dispatch('notify', type: 'success', message: $message);
    }

    private function resetInput(): void
    {
        $this->editId = null;
        $this->title = '';
        $this->summary = '';
        $this->content = '';
        $this->event_date = '';
        $this->event_time = '';
        $this->is_published = true;
    }

    public function render()
    {
        $query = Announcement::query()->withCount('readByUsers as readers_count');

        if ($this->search !== '') {
            $keyword = '%'.$this->search.'%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('summary', 'like', $keyword)
                    ->orWhere('content', 'like', $keyword);
            });
        }

        if ($this->statusFilter === 'publish') {
            $query->where('is_published', true);
        } elseif ($this->statusFilter === 'draft') {
            $query->where('is_published', false);
        }

        $announcements = $query->latest()->paginate(8);

        $total = Announcement::count();
        $published = Announcement::where('is_published', true)->count();

        return view('livewire.announcement-table', [
            'announcements' => $announcements,
            'statTotal' => $total,
            'statPublished' => $published,
            'statDraft' => $total - $published,
            'statReads' => DB::table('announcement_user')->count(),
            'audience' => User::count(),
        ]);
    }
}
