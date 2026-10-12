<?php

namespace App\Livewire;

use App\Facades\FileVault;
use App\Models\ScriptVideoTarget;
use App\Models\ScriptVideoUpload;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ScriptVideoTable extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $bulan;

    public int $tahun;

    public bool $showModal = false;

    public bool $showDeleteConfirmModal = false;

    public ?int $deleteConfirmId = null;

    public string $deleteConfirmTitle = '';
    public bool $showActivateContentModal = false;

    public ?int $activateContentId = null;

    public string $activateContentTitle = '';

    public ?int $editId = null;

    public string $search = '';

    public ?string $filterGame = null;

    public string $judul = '';

    public string $keterangan = '';

    public ?string $kategoriGame = null;

    public $file = null;

    public ?int $detailUserId = null;

    public int $targetInput = 75;

    protected $listeners = ['resetFilter' => 'resetFilter'];

    public function mount(): void
    {
        $this->bulan = ScriptVideoUpload::currentBulanName();
        $this->tahun = ScriptVideoUpload::currentTahun();
        $this->targetInput = ScriptVideoTarget::targetFor($this->bulan, $this->tahun);
    }

    public function updatedBulan(): void
    {
        $this->resetPage();
        $this->syncTarget();
    }

    public function updatedTahun(): void
    {
        $this->resetPage();
        $this->syncTarget();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGame(): void
    {
        $this->resetPage();
    }

    public function requestToggleKontenDibuat(int $id): void
    {
        $user = auth()->user();
        $upload = ScriptVideoUpload::findOrFail($id);

        abort_unless($user && ($user->isStaffCreative() || (int) $upload->user_id === (int) $user->id), 403);

        if (! $upload->konten_dibuat) {
            $this->activateContentId = $upload->id;
            $this->activateContentTitle = $upload->judul;
            $this->showActivateContentModal = true;

            return;
        }

        $upload->konten_dibuat = false;
        $upload->save();

        $this->dispatch('notify', type: 'success', message: 'Konten ditandai sudah dibuat.');
        $this->dispatch('script-video-updated');
    }

    public function confirmActivateContent(): void
    {
        abort_unless($this->showActivateContentModal && $this->activateContentId, 404);

        $user = auth()->user();
        $upload = ScriptVideoUpload::findOrFail($this->activateContentId);
        abort_unless($user && ($user->isStaffCreative() || (int) $upload->user_id === (int) $user->id), 403);

        $upload->konten_dibuat = true;
        $upload->save();
        $this->cancelActivateContent();

        $this->dispatch('notify', type: 'success', message: 'Konten ditandai belum dibuat.');
        $this->dispatch('script-video-updated');
    }

    public function cancelActivateContent(): void
    {
        $this->showActivateContentModal = false;
        $this->activateContentId = null;
        $this->activateContentTitle = '';
    }
    public function isRekap(): bool
    {
        $user = auth()->user();

        return $user && in_array($user->role, [
            User::ROLE_MANAGER,
            User::ROLE_GM_CEO,
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN_MASTER,
        ]);
    }

    public function isVideoAnimator(): bool
    {
        return auth()->user()?->role === User::ROLE_STAFF_CREATIVE;
    }

    public function openNew(): void
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $upload = ScriptVideoUpload::findOrFail($id);
        if (! $this->canManage($upload)) {
            abort(403);
        }

        $this->editId = $upload->id;
        $this->judul = $upload->judul;
        $this->keterangan = $upload->keterangan ?? '';
        $this->kategoriGame = $upload->kategori_game ?? null;
        $this->file = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'bulan' => ['required', 'string', 'in:'.implode(',', ScriptVideoUpload::BULAN_LIST)],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2099'],
            'judul' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'kategoriGame' => ['nullable', 'string', 'in:'.implode(',', ScriptVideoUpload::GAME_CATEGORIES)],
            'file' => $this->editId
                ? ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240']
                : ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $user = auth()->user();

        if ($this->editId) {
            $upload = ScriptVideoUpload::findOrFail($this->editId);
            if (! $this->canManage($upload)) {
                abort(403);
            }

            $upload->judul = $this->judul;
            $upload->keterangan = $this->keterangan ?: null;
            $upload->kategori_game = $this->kategoriGame;

            if ($this->file) {
                if ($upload->file_path) {
                    FileVault::delete('public', $upload->file_path);
                }
                $upload->file_path = FileVault::store($this->file, 'script-video/'.$upload->user_id, 'public');
                $upload->file_name = $this->file->getClientOriginalName();
            }

            $upload->save();

            $this->dispatch('notify', type: 'success', message: 'Script konten berhasil diperbarui.');
        } else {
            ScriptVideoUpload::create([
                'user_id' => $user->id,
                'bulan' => $this->bulan,
                'tahun' => $this->tahun,
                'judul' => $this->judul,
                'keterangan' => $this->keterangan ?: null,
                'kategori_game' => $this->kategoriGame,
                'file_path' => FileVault::store($this->file, 'script-video/'.$user->id, 'public'),
                'file_name' => $this->file->getClientOriginalName(),
            ]);

            $this->dispatch('notify', type: 'success', message: 'Script konten berhasil diunggah.');
        }

        $this->resetInput();
        $this->showModal = false;
        $this->dispatch('script-video-updated');
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $upload = ScriptVideoUpload::findOrFail($id);
        abort_unless($this->canManage($upload), 403);

        $this->deleteConfirmId = $upload->id;
        $this->deleteConfirmTitle = $upload->judul;
        $this->showDeleteConfirmModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirmModal = false;
        $this->deleteConfirmId = null;
        $this->deleteConfirmTitle = '';
    }

    public function executeDelete(): void
    {
        abort_unless($this->showDeleteConfirmModal && $this->deleteConfirmId, 404);

        $id = $this->deleteConfirmId;
        $this->cancelDelete();
        $this->delete($id);
    }
    public function delete(int $id): void
    {
        $upload = ScriptVideoUpload::findOrFail($id);
        if (! $this->canManage($upload)) {
            abort(403);
        }

        if ($upload->file_path) {
            FileVault::delete('public', $upload->file_path);
        }
        $upload->delete();

        $this->dispatch('notify', type: 'success', message: 'Script konten berhasil dihapus.');
        $this->dispatch('script-video-updated');
        $this->resetPage();
    }

    public function saveTarget(): void
    {
        abort_unless($this->isRekap(), 403);

        $this->validate([
            'bulan' => ['required', 'string', 'in:'.implode(',', ScriptVideoUpload::BULAN_LIST)],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2099'],
            'targetInput' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        ScriptVideoTarget::upsertTarget($this->bulan, (int) $this->tahun, (int) $this->targetInput);

        $this->dispatch('notify', type: 'success', message: "Target {$this->bulan} {$this->tahun} disimpan.");
        $this->dispatch('script-video-updated');
    }

    public function openDetail(int $userId): void
    {
        abort_unless($this->isRekap(), 403);

        $this->detailUserId = $userId;
        $this->resetPage();
    }

    public function closeDetail(): void
    {
        $this->detailUserId = null;
        $this->resetPage();
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetInput();
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->filterGame = null;
    }

    private function resetInput(): void
    {
        $this->editId = null;
        $this->judul = '';
        $this->keterangan = '';
        $this->kategoriGame = null;
        $this->file = null;
    }

    private function syncTarget(): void
    {
        $this->targetInput = ScriptVideoTarget::targetFor($this->bulan, $this->tahun);
    }

    private function canManage(ScriptVideoUpload $upload): bool
    {
        $user = auth()->user();

        return $user && ($upload->user_id === $user->id || $this->isRekap());
    }

    private function countFor(int $userId): int
    {
        return ScriptVideoUpload::forPeriod($this->bulan, $this->tahun)
            ->where('user_id', $userId)
            ->count();
    }

    private function weeklySummaryFor(int $userId, int $target): array
    {
        $counts = ScriptVideoUpload::forPeriod($this->bulan, $this->tahun)
            ->where('user_id', $userId)
            ->selectRaw('minggu, COUNT(*) as jumlah')
            ->groupBy('minggu')
            ->pluck('jumlah', 'minggu')
            ->all();

        return $this->weeklySummary($counts, $target);
    }

    private function weeklySummary(array $counts, int $target): array
    {
        $kumulatif = 0;
        $ringkasan = [];

        foreach (range(1, 4) as $minggu) {
            $terisi = (int) ($counts[$minggu] ?? 0);
            $quota = ScriptVideoTarget::weeklyQuotaFor($target, $kumulatif, $minggu);
            $kumulatif += $terisi;

            $ringkasan[$minggu] = [
                'quota' => $quota,
                'terisi' => $terisi,
                'total' => $kumulatif,
                'sisa' => max(0, $quota - $terisi),
            ];
        }

        return $ringkasan;
    }

    public function render()
    {
        $user = auth()->user();
        $target = ScriptVideoTarget::targetFor($this->bulan, $this->tahun);
        $isRekap = $this->isRekap();
        $mingguBerjalan = ScriptVideoUpload::mingguUntukTanggal(now());

        $jumlah = 0;
        $uploads = collect();
        $detailUser = null;
        $rekap = collect();
        $rekapStats = ['total' => 0, 'tercapai' => 0, 'belum' => 0];
        $weeklySummary = [];
        $contributorCount = 0;

        if ($isRekap) {
            if ($this->detailUserId) {
                $detailUser = User::find($this->detailUserId);

                if ($detailUser) {
                    $jumlah = $this->countFor($detailUser->id);
                    $weeklySummary = $this->weeklySummaryFor($detailUser->id, $target);
                    $uploads = $this->queryUploads()
                        ->where('user_id', $detailUser->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(12, ['*'], 'detail_page');
                }
            } else {
                $counts = ScriptVideoUpload::forPeriod($this->bulan, $this->tahun)
                    ->selectRaw('user_id, COUNT(*) as jumlah')
                    ->groupBy('user_id')
                    ->pluck('jumlah', 'user_id');

                $weekCounts = ScriptVideoUpload::forPeriod($this->bulan, $this->tahun)
                    ->selectRaw('user_id, minggu, COUNT(*) as jumlah')
                    ->groupBy('user_id', 'minggu')
                    ->get()
                    ->groupBy('user_id')
                    ->map(fn ($rows) => $rows->pluck('jumlah', 'minggu')->all());

                $koordinators = User::koordinator()
                    ->when($this->search, function ($q) {
                        $search = $this->search;
                        $q->where(function ($w) use ($search) {
                            $w->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                    })
                    ->orderBy('name')
                    ->get();

                foreach ($koordinators as $koordinator) {
                    $koordinator->jumlah = (int) ($counts[$koordinator->id] ?? 0);
                    $koordinator->target = $target;
                    $koordinator->tercapai = $koordinator->jumlah >= $target;
                    $koordinator->weeklySummary = $this->weeklySummary($weekCounts[$koordinator->id] ?? [], $target);

                    $rekapStats['total']++;
                    $koordinator->tercapai ? $rekapStats['tercapai']++ : $rekapStats['belum']++;
                }

                $rekap = $koordinators;
            }
        } elseif ($this->isVideoAnimator()) {
            $jumlah = ScriptVideoUpload::forPeriod($this->bulan, $this->tahun)->count();
            $contributorCount = ScriptVideoUpload::forPeriod($this->bulan, $this->tahun)
                ->distinct('user_id')
                ->count('user_id');
            $uploads = $this->queryUploads()
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        } else {
            $jumlah = $this->countFor($user->id);
            $weeklySummary = $this->weeklySummaryFor($user->id, $target);
            $uploads = $this->queryUploads()
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }

        $progress = $target > 0 ? min(100, round(($jumlah / $target) * 100)) : 0;

        return view('livewire.script-video-table', [
            'isRekap' => $isRekap,
            'target' => $target,
            'jumlah' => $jumlah,
            'progress' => $progress,
            'uploads' => $uploads,
            'rekap' => $rekap,
            'rekapStats' => $rekapStats,
            'detailUser' => $detailUser,
            'weeklySummary' => $weeklySummary,
            'mingguBerjalan' => $mingguBerjalan,
            'isVideoAnimator' => $this->isVideoAnimator(),
            'contributorCount' => $contributorCount,
        ]);
    }

    private function queryUploads()
    {
        $query = ScriptVideoUpload::query()
            ->with('user:id,name,role')
            ->forPeriod($this->bulan, $this->tahun);

        if ($this->filterGame) {
            $query->where('kategori_game', $this->filterGame);
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");

                if ($this->isVideoAnimator()) {
                    $q->orWhereHas('user', fn ($w) => $w->where('name', 'like', "%{$search}%"));
                }
            });
        }

        return $query;
    }
}
