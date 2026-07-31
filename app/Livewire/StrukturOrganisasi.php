<?php

namespace App\Livewire;

use App\Models\Position;
use App\Models\PositionNote;
use App\Models\PositionNoteComment;
use Livewire\Component;

class StrukturOrganisasi extends Component
{
    public bool $showNoteModal = false;
    public ?int $selectedPositionId = null;
    public string $selectedPositionName = '';

    public string $viewState = 'form';

    public string $situasi = '';
    public string $evaluasi = '';
    public string $komitmen = '';
    public string $rekomendasi_jenjang = '';

    public int $bulan;
    public int $tahun;
    public bool $showForm = false;
    public ?array $existingNote = null;
    public array $notesHistory = [];
    public bool $isSuperior = false;

    public ?int $selectedNoteId = null;
    public ?array $noteDetail = null;
    public string $komentar = '';
    public ?int $replyToId = null;
    public array $noteComments = [];
    public bool $canComment = false;
    public ?int $myPositionId = null;

    public function mount(): void
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
        $this->myPositionId = $this->getMyPositionId();
    }

    public function openNoteModal(int $positionId, string $view = 'form'): void
    {
        $this->myPositionId = $this->getMyPositionId();
        $this->selectedPositionId = $positionId;
        $position = Position::findOrFail($positionId);
        $this->selectedPositionName = $position->nama;
        $this->viewState = $view;
        $this->showForm = ($view === 'form');
        $this->selectedNoteId = null;
        $this->noteDetail = null;
        $this->noteComments = [];
        $this->replyToId = null;
        $this->komentar = '';
        $this->loadNoteData();
        $this->dispatch('open-modal', name: 'note-modal');
    }

    public function loadNoteData(): void
    {
        if (!$this->selectedPositionId) return;

        $this->myPositionId = $this->getMyPositionId();

        $note = PositionNote::with('fromPosition', 'creator.employee')
            ->where('to_position_id', $this->selectedPositionId)
            ->where('bulan', $this->bulan)
            ->where('tahun', $this->tahun)
            ->first();

        $this->existingNote = $note?->toArray();
        $this->situasi = $note?->situasi ?? '';
        $this->evaluasi = $note?->evaluasi ?? '';
        $this->komitmen = $note?->komitmen ?? '';
        $this->rekomendasi_jenjang = $note?->rekomendasi_jenjang ?? '';

        $this->notesHistory = PositionNote::with('fromPosition', 'creator.employee')
            ->where('to_position_id', $this->selectedPositionId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get()
            ->toArray();

        $this->selectedNoteId = $note?->id;

        $this->noteComments = $note
            ? PositionNoteComment::with('user.employee', 'replies.user.employee')
                ->where('position_note_id', $note->id)
                ->whereNull('parent_id')
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray()
            : [];

    $position = Position::find($this->selectedPositionId);
    $this->isSuperior = $this->checkIsSuperior($this->myPositionId, $position);
    $this->canComment = $this->isSuperior || ($this->myPositionId && $this->myPositionId === $this->selectedPositionId);
}

    public function showDetail(int $noteId): void
    {
        $this->myPositionId = $this->getMyPositionId();
        $this->selectedNoteId = $noteId;
        $this->viewState = 'detail';

        $note = PositionNote::with('fromPosition', 'creator.employee')->find($noteId);
        $this->noteDetail = $note?->toArray();

        $this->noteComments = PositionNoteComment::with('user.employee', 'replies.user.employee')
            ->where('position_note_id', $noteId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        $this->replyToId = null;
        $this->komentar = '';

        $targetPosition = $note?->to_position_id;
        $this->isSuperior = $this->checkIsSuperior($this->myPositionId, Position::find($targetPosition));
        $this->canComment = $this->isSuperior || ($this->myPositionId && $this->myPositionId === $targetPosition);
    }

    public function deleteNote(int $noteId): void
    {
        $note = PositionNote::withCount('comments')->find($noteId);

        if (!$note) {
            $this->dispatch('notify', type: 'error', message: 'Evaluasi tidak ditemukan.');
            return;
        }

        if ($note->created_by !== auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak berwenang menghapus evaluasi ini.');
            return;
        }

        $note->comments()->delete();
        $note->delete();

        $this->dispatch('notify', type: 'success', message: 'Evaluasi berhasil dihapus.');
        $this->backToHistory();
        $this->loadNoteData();
    }

    public function backToHistory(): void
    {
        $this->viewState = 'history';
        $this->selectedNoteId = null;
        $this->noteDetail = null;
        $this->noteComments = [];
        $this->replyToId = null;
        $this->komentar = '';
    }

    public function switchToForm(): void
    {
        $this->viewState = 'form';
        $this->showForm = true;
        $this->situasi = '';
        $this->evaluasi = '';
        $this->komitmen = '';
        $this->rekomendasi_jenjang = '';
        $this->existingNote = null;
        $this->noteComments = [];
        $this->selectedNoteId = null;
        $this->komentar = '';
        $this->replyToId = null;
    }

    public function replyToComment(int $commentId): void
    {
        $this->replyToId = $commentId;
        $this->komentar = '';
    }

    public function cancelReply(): void
    {
        $this->replyToId = null;
        $this->komentar = '';
    }

    public function saveComment(): void
    {
        $this->validate([
            'selectedNoteId' => ['required', 'exists:position_notes,id'],
            'komentar' => ['required', 'string', 'max:5000'],
        ]);

        PositionNoteComment::create([
            'position_note_id' => $this->selectedNoteId,
            'user_id' => auth()->id(),
            'komentar' => $this->komentar,
            'parent_id' => $this->replyToId,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Komentar berhasil dikirim.');

        $this->noteComments = PositionNoteComment::with('user.employee', 'replies.user.employee')
            ->where('position_note_id', $this->selectedNoteId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        $this->replyToId = null;
        $this->komentar = '';
    }

    private function checkIsSuperior(?int $myPositionId, ?Position $targetPosition): bool
    {
        if (!$targetPosition) return false;

        if ($myPositionId && $targetPosition->parent_id === $myPositionId) {
            return true;
        }

        $user = auth()->user();
        if (!$user) return false;

        if ($user->isSuperAdmin() || $user->isGmCeo()) {
            return true;
        }

        $posDepth = $this->getPositionDepth($targetPosition);
        $roleLevel = $user->roleLevel();

        if ($roleLevel >= 3 && $posDepth >= 3) return true;
        if ($roleLevel >= 2 && $posDepth >= 4) return true;

        return false;
    }

    private function getPositionDepth(Position $position): int
    {
        $depth = 0;
        $current = $position;
        while ($current->parent_id) {
            $depth++;
            $current = $current->parent;
            if (!$current) break;
        }
        return $depth;
    }

    public function updatedBulan(): void
    {
        $this->showForm = false;
        $this->loadNoteData();
    }

    public function updatedTahun(): void
    {
        $this->showForm = false;
        $this->loadNoteData();
    }

    public function saveNote(): void
    {
        $this->validate([
            'selectedPositionId' => ['required', 'exists:positions,id'],
            'situasi' => ['nullable', 'string', 'max:5000'],
            'evaluasi' => ['nullable', 'string', 'max:5000'],
            'komitmen' => ['nullable', 'string', 'max:5000'],
            'rekomendasi_jenjang' => ['nullable', 'string', 'max:5000'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2099'],
        ]);

        $this->myPositionId = $this->getMyPositionId();

        $position = Position::find($this->selectedPositionId);
        if (!$position) {
            $this->dispatch('notify', type: 'error', message: 'Jabatan tidak ditemukan.');
            return;
        }

        if (!$this->checkIsSuperior($this->myPositionId, $position)) {
            $this->dispatch('notify', type: 'error', message: 'Anda tidak berwenang memberi evaluasi untuk jabatan ini.');
            return;
        }

        PositionNote::updateOrCreate(
            [
                'from_position_id' => $this->myPositionId,
                'to_position_id' => $this->selectedPositionId,
                'bulan' => $this->bulan,
                'tahun' => $this->tahun,
            ],
            [
                'situasi' => $this->situasi,
                'evaluasi' => $this->evaluasi,
                'komitmen' => $this->komitmen,
                'rekomendasi_jenjang' => $this->rekomendasi_jenjang,
                'created_by' => auth()->id(),
            ]
        );

        $this->dispatch('notify', type: 'success', message: 'Evaluasi berhasil disimpan.');
        $this->loadNoteData();
    }

    public function render()
    {
        $allPositions = Position::where('is_active', true)->with('employees')->get();

        $roots = $this->buildTree($allPositions, null);
        $roots = $this->splitMonkeyDexNodes($roots);
        $roots = $this->sortTreeByName($roots);

        $flatPositions = $allPositions->mapWithKeys(function ($pos) {
            $emp = $pos->employees->first();
            return [$pos->id => [
                'id' => $pos->id,
                'nama' => $pos->nama,
                'parent_id' => $pos->parent_id,
                'employee_nama' => $emp?->nama,
                'employee_nik' => $emp?->nik,
                'employee_foto' => $emp?->foto_url,
            ]];
        });

        $notesByPosition = PositionNote::selectRaw("to_position_id, COUNT(*) as total, SUM(CASE WHEN bulan = ? AND tahun = ? THEN 1 ELSE 0 END) as has_current", [now()->month, now()->year])
            ->groupBy('to_position_id')
            ->get()
            ->keyBy('to_position_id');

        $canGiveNotesByPosition = $allPositions->mapWithKeys(function ($pos) {
            return [$pos->id => $this->checkIsSuperior($this->myPositionId, $pos)];
        });

        return view('livewire.struktur-organisasi', [
            'roots' => $roots,
            'flatPositions' => $flatPositions,
            'notesByPosition' => $notesByPosition,
            'canGiveNotesByPosition' => $canGiveNotesByPosition,
        ]);
    }

    private function buildTree($positions, $parentId)
    {
        return $positions
            ->where('parent_id', $parentId)
            ->map(function ($pos) use ($positions) {
                return [
                    'id' => $pos->id,
                    'nama' => $pos->nama,
                    'employee' => $pos->employees->first(),
                    'children' => $this->buildTree($positions, $pos->id),
                ];
            })->values();
    }

    private function splitMonkeyDexNodes($nodes)
    {
        $result = collect();

        foreach ($nodes as $node) {
            $node['children'] = $this->splitMonkeyDexNodes($node['children']);

            if ($node['nama'] === 'Admin Monkey & Dex') {
                $result->push(array_merge($node, ['nama' => 'Admin Monkey & Dex (Pagi)']));
                $result->push(array_merge($node, ['nama' => 'Admin Monkey & Dex (Malam)']));
            } else {
                $result->push($node);
            }
        }

        return $result;
    }

    private function sortTreeByName($nodes)
    {
        return $nodes->sortBy('nama')->values()->map(function ($node) {
            $node['children'] = $this->sortTreeByName($node['children']);
            return $node;
        });
    }

    private function getMyPositionId(): ?int
    {
        $user = auth()->user();
        if (!$user || !$user->employee) return null;

        $mainPos = $user->employee->mainPosition();
        return $mainPos?->id;
    }
}
