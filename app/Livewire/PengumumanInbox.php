<?php

namespace App\Livewire;

use App\Models\Announcement;
use App\Models\Pengarsipan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class PengumumanInbox extends Component
{
    use WithPagination;

    public string $search = '';

    /** @var 'semua'|'pengumuman'|'surat_edaran'|'surat_keputusan'|'pemberitahuan' */
    public string $jenisFilter = 'semua';

    protected $queryString = ['search', 'jenisFilter' => ['except' => 'semua']];

    private const ARSIP_JENIS = [
        'surat_edaran' => Pengarsipan::JENIS_SURAT_EDARAN,
        'surat_keputusan' => Pengarsipan::JENIS_SURAT_KEPUTUSAN,
        'pemberitahuan' => Pengarsipan::JENIS_PEMBERITAHUAN,
    ];

    private const BADGES = [
        'pengumuman' => ['label' => 'Pengumuman', 'class' => 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300 ring-primary-600/20'],
        'surat_edaran' => ['label' => 'Surat Edaran', 'class' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300 ring-sky-600/20'],
        'surat_keputusan' => ['label' => 'Surat Keputusan', 'class' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300 ring-violet-600/20'],
        'pemberitahuan' => ['label' => 'Pemberitahuan', 'class' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 ring-amber-600/20'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedJenisFilter(): void
    {
        $this->resetPage();
    }

    public function markRead(int $id): void
    {
        $announcement = Announcement::query()
            ->where('id', $id)
            ->where('is_published', true)
            ->first();

        if (! $announcement) {
            return;
        }

        auth()->user()->readAnnouncements()->syncWithoutDetaching([
            $announcement->id => ['read_at' => now()],
        ]);

        $this->dispatch('notify', type: 'success', message: 'Pengumuman ditandai sebagai sudah dibaca.');
    }

    private function keyword(): ?string
    {
        return $this->search !== '' ? '%'.$this->search.'%' : null;
    }

    private function announcementQuery()
    {
        $keyword = $this->keyword();

        return Announcement::query()
            ->where('is_published', true)
            ->when($keyword !== null, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('title', 'like', $keyword)
                        ->orWhere('summary', 'like', $keyword)
                        ->orWhere('content', 'like', $keyword);
                });
            })
            ->orderByDesc('created_at');
    }

    private function arsipQuery(?string $jenis)
    {
        $keyword = $this->keyword();

        return Pengarsipan::query()
            ->when($jenis !== null, fn ($q) => $q->where('jenis', $jenis))
            ->when($keyword !== null, function ($q) use ($keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('judul', 'like', $keyword)
                        ->orWhere('nomor', 'like', $keyword)
                        ->orWhere('keterangan', 'like', $keyword);
                });
            })
            ->orderByDesc('tanggal_surat')
            ->orderByDesc('created_at');
    }

    private function eventLabel(Announcement $announcement): ?string
    {
        if (! $announcement->event_date) {
            return null;
        }

        $label = Carbon::parse($announcement->event_date)->locale('id')->isoFormat('D MMM YYYY');

        if ($announcement->event_time) {
            $label .= ' · '.substr((string) $announcement->event_time, 0, 5);
        }

        return $label;
    }

    private function buildItems(array $readIds): Collection
    {
        $arsipJenis = self::ARSIP_JENIS[$this->jenisFilter] ?? null;
        $includeAnnouncements = $arsipJenis === null;

        $items = collect();

        if ($includeAnnouncements) {
            foreach ($this->announcementQuery()->get() as $announcement) {
                $items->push([
                    'type' => 'pengumuman',
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'description' => $announcement->summary ?: Str::limit(strip_tags((string) $announcement->content), 160),
                    'date' => $announcement->created_at,
                    'is_read' => isset($readIds[$announcement->id]),
                    'event_label' => $this->eventLabel($announcement),
                    'nomor' => null,
                    'badge_key' => 'pengumuman',
                    'file_url' => null,
                ]);
            }

            if ($this->jenisFilter === 'pengumuman') {
                return $items;
            }
        }

        foreach ($this->arsipQuery($arsipJenis)->get() as $arsip) {
            $items->push([
                'type' => 'arsip',
                'id' => $arsip->id,
                'title' => $arsip->judul,
                'description' => $arsip->keterangan,
                'date' => $arsip->tanggal_surat ?? $arsip->created_at,
                'is_read' => null,
                'event_label' => null,
                'nomor' => $arsip->nomor,
                'badge_key' => $arsip->jenis,
                'file_url' => $arsip->file ? Storage::url($arsip->file) : null,
            ]);
        }

        return $items->sortByDesc(fn (array $item) => optional($item['date'])->timestamp ?? 0)->values();
    }

    public static function badge(string $key): array
    {
        return self::BADGES[$key] ?? self::BADGES['pengumuman'];
    }

    public function render()
    {
        $readIds = auth()->user()->readAnnouncements()
            ->pluck('announcements.id')
            ->flip()
            ->all();

        $items = $this->buildItems($readIds);

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $inbox = new LengthAwarePaginator(
            $items->slice(($page - 1) * $perPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $inbox->withQueryString();

        $statPengumuman = Announcement::query()->where('is_published', true)->count();

        $arsipStats = Pengarsipan::query()
            ->selectRaw("COALESCE(SUM(jenis = 'surat_edaran'), 0) as surat_edaran")
            ->selectRaw("COALESCE(SUM(jenis = 'surat_keputusan'), 0) as surat_keputusan")
            ->selectRaw("COALESCE(SUM(jenis = 'pemberitahuan'), 0) as pemberitahuan")
            ->first();

        return view('livewire.pengumuman-inbox', [
            'inbox' => $inbox,
            'search' => $this->search,
            'jenisFilter' => $this->jenisFilter,
            'statPengumuman' => $statPengumuman,
            'statEdaran' => (int) $arsipStats->surat_edaran,
            'statKeputusan' => (int) $arsipStats->surat_keputusan,
            'statPemberitahuan' => (int) $arsipStats->pemberitahuan,
            'statTotal' => $statPengumuman + (int) $arsipStats->surat_edaran + (int) $arsipStats->surat_keputusan + (int) $arsipStats->pemberitahuan,
        ]);
    }
}
