<?php

namespace App\Livewire;

use App\Models\BonusPubg;
use App\Models\Employee;
use App\Models\Position;
use Livewire\Component;
use Livewire\WithFileUploads;

class DailyTrackingTable extends Component
{
    use WithFileUploads;

    private const GAME_KEYS = [
        'PUBG' => 'pubg',
        'Free Fire' => 'free-fire',
        'MLBB' => 'mlbb',
        'E-football' => 'e-football',
        'Valorant' => 'valorant',
        'Roblox' => 'roblox',
        'Monkey PUBG' => 'monkey-pubg',
        'FC Mobile' => 'fc-mobile',
    ];

    private const GAME_PHOTO_EXTS = ['png', 'jpg', 'jpeg', 'webp', 'avif', 'gif'];

    private const DIVISION_POSITION_MAP = [
        'PUBG' => 'koordinator johen pubg',
        'Free Fire' => 'koordinator free fire',
        'MLBB' => 'koordinator mlbb',
        'E-football' => 'koordinator e-football',
        'Valorant' => 'koordinator valorant',
        'Roblox' => 'koordinator roblox',
        'Monkey PUBG' => 'koordinator monkey pubg',
        'FC Mobile' => 'koordinator fc mobile',
    ];

    public bool $showUploadModal = false;
    public string $uploadDivisi = '';
    public $uploadPhoto;

    private function gameKey(string $divisi): string
    {
        return self::GAME_KEYS[$divisi] ?? 'game-' . str($divisi)->slug();
    }

    private function resolveGamePhoto(string $divisi): ?string
    {
        $key = $this->gameKey($divisi);
        foreach (self::GAME_PHOTO_EXTS as $ext) {
            if (is_file(public_path("games/{$key}.{$ext}"))) {
                return asset("games/{$key}.{$ext}");
            }
        }
        return null;
    }

    public function openUploadModal(string $divisi): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->uploadDivisi = $divisi;
        $this->uploadPhoto = null;
        $this->showUploadModal = true;
        $this->resetErrorBag();
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->uploadDivisi = '';
        $this->uploadPhoto = null;
        $this->resetErrorBag();
    }

    public function saveUploadPhoto(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate([
            'uploadPhoto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'uploadPhoto.required' => 'Pilih gambar game terlebih dahulu.',
            'uploadPhoto.image' => 'File harus berupa gambar.',
            'uploadPhoto.mimes' => 'Format harus JPG/PNG/WEBP.',
            'uploadPhoto.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $key = $this->gameKey($this->uploadDivisi);

        $dir = public_path('games');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = strtolower($this->uploadPhoto->getClientOriginalExtension());
        foreach (self::GAME_PHOTO_EXTS as $oldExt) {
            if ($oldExt !== $ext) {
                @unlink(public_path("games/{$key}.{$oldExt}"));
            }
        }

        $this->uploadPhoto->move($dir, "{$key}.{$ext}");

        $this->closeUploadModal();
        $this->dispatch('notify', type: 'success', message: "Foto game {$this->uploadDivisi} berhasil diperbarui.");
    }

    private function getSubordinateIds(Employee $employee): array
    {
        $position = $employee->mainPosition();
        if (!$position) return [];

        $descendantIds = $this->getDescendantIds($position->id);
        $descendantIds = array_diff($descendantIds, [$position->id]);

        if (empty($descendantIds)) return [];

        return Employee::whereIn('id', function ($q) use ($descendantIds) {
            $q->select('employee_id')
              ->from('employee_position')
              ->whereIn('position_id', $descendantIds);
        })->pluck('id')->toArray();
    }

    private function getDescendantIds(int $positionId): array
    {
        $ids = [$positionId];
        $children = Position::where('parent_id', $positionId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getDescendantIds($childId));
        }
        return $ids;
    }

    private function getManagerDivisionNames(Employee $employee): array
    {
        $position = $employee->mainPosition();
        if (!$position) return [];

        $descendantIds = $this->getDescendantIds($position->id);
        $names = Position::whereIn('id', $descendantIds)->pluck('nama')
            ->map(fn ($n) => strtolower($n))->toArray();

        $divisions = [];
        foreach (self::DIVISION_POSITION_MAP as $divisi => $posName) {
            foreach ($names as $name) {
                if (str_contains($name, $posName)) {
                    $divisions[] = $divisi;
                    break;
                }
            }
        }

        return array_values(array_unique($divisions));
    }

    private function getEfootballEmployeeIds(): array
    {
        $efootball = Position::where('nama', 'Koordinator E-football')->first();
        if (!$efootball) return [];

        $positionIds = $this->getDescendantIds($efootball->id);

        return Employee::whereHas('positions', function ($q) use ($positionIds) {
            $q->whereIn('position_id', $positionIds);
        })->pluck('id')->toArray();
    }

    private function isHeadOfStore2(Employee $employee): bool
    {
        $position = $employee->mainPosition();
        if (!$position) return false;

        return str_contains(strtolower($position->nama), 'head of store 2');
    }

    public function render()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $empty = [
            'games' => collect(),
            'totalSold' => 0,
            'totalView' => 0,
            'totalPeak' => 0,
            'totalDurasi' => 0,
        ];

        if (!$employee || !$user->isManager()) {
            return view('livewire.daily-tracking-table', $empty);
        }

        $subordinateIds = $this->getSubordinateIds($employee);
        if (empty($subordinateIds)) {
            return view('livewire.daily-tracking-table', $empty);
        }

        if ($this->isHeadOfStore2($employee)) {
            $efootballIds = $this->getEfootballEmployeeIds();
            if (!empty($efootballIds)) {
                $subordinateIds = array_diff($subordinateIds, $efootballIds);
                if (empty($subordinateIds)) {
                    return view('livewire.daily-tracking-table', $empty);
                }
            }
        }

        $managerDivisionNames = $this->getManagerDivisionNames($employee);

        $baseQuery = BonusPubg::whereIn('bonus_pubgs.employee_id', $subordinateIds)
            ->where('bonus_pubgs.status', 'disetujui')
            ->whereIn('bonus_pubgs.divisi', $managerDivisionNames);

        $games = collect($managerDivisionNames)->map(function ($divisi) use ($baseQuery) {
            $stats = (clone $baseQuery)
                ->where('bonus_pubgs.divisi', $divisi)
                ->selectRaw('COALESCE(SUM(ach_sold), 0) as sold, COALESCE(SUM(ach_view), 0) as view, COALESCE(SUM(peak_view), 0) as peak, COALESCE(SUM(durasi), 0) as durasi, COUNT(*) as total')
                ->first();

            return [
                'divisi' => $divisi,
                'photo' => $this->resolveGamePhoto($divisi),
                'totalSold' => $stats->sold ?? 0,
                'totalView' => $stats->view ?? 0,
                'totalPeak' => $stats->peak ?? 0,
                'totalDurasi' => $stats->durasi ?? 0,
                'total' => $stats->total ?? 0,
            ];
        });

        $totalSold = $games->sum('totalSold');
        $totalView = $games->sum('totalView');
        $totalPeak = $games->sum('totalPeak');
        $totalDurasi = $games->sum('totalDurasi');

        return view('livewire.daily-tracking-table', [
            'games' => $games,
            'totalSold' => $totalSold,
            'totalView' => $totalView,
            'totalPeak' => $totalPeak,
            'totalDurasi' => $totalDurasi,
        ]);
    }
}