<?php

namespace App\Support;

use App\Models\InfluencerPengajuan;
use App\Models\Position;
use App\Models\User;

class InfluencerPengajuanRouting
{
    private const DIVISI_KOL = [
        'Admin KOL 1' => ['Johen PUBG', 'Johen Roblox', 'Johen E-Football', 'Johen FC Mobile'],
        'Admin KOL 2' => ['Johen MLBB', 'Johen Free Fire', 'Johen Valorant', 'Monkey PUBG'],
    ];

    private const DIVISI_HEAD_OF_STORE = [
        'Head of Store 1' => ['Johen PUBG', 'Johen E-Football', 'Johen FC Mobile', 'Johen Roblox'],
        'Head of Store 2' => ['Johen MLBB', 'Johen Free Fire', 'Johen Valorant', 'Monkey PUBG'],
    ];

    public static function headOfStorePositionForDivision(string $division): ?Position
    {
        foreach (self::DIVISI_HEAD_OF_STORE as $positionName => $divisions) {
            if (in_array($division, $divisions, true)) {
                return Position::query()->where('nama', $positionName)->first();
            }
        }

        return null;
    }

    public static function headOfStorePositionForUser(?User $user): ?Position
    {
        $employee = $user?->employee;
        $position = $employee?->mainPosition();
        if (!$position && $employee) {
            $legacyPositionName = trim((string) $employee->position);
            $position = Position::query()->where('nama', $legacyPositionName)->first();
            if (!$position) {
                $normalizedPositionName = preg_replace('/^(?:ASKOR|ASST\\.?\\s*COORDINATOR)\\s+/i', '', $legacyPositionName);
                $position = Position::query()->where('nama', $normalizedPositionName)->first();
            }
        }
        $position ??= $employee?->positions()->first();
        $visited = [];

        while ($position && !isset($visited[$position->id])) {
            $visited[$position->id] = true;
            if (preg_match('/^Head of Store\s+[12]$/i', trim($position->nama))) {
                return $position;
            }
            $position = $position->parent;
        }

        return null;
    }

    public static function headOfStoreNameForUser(?User $user): ?string
    {
        $position = self::headOfStorePositionForUser($user);
        if ($position) {
            return $position->nama;
        }

        $legacyName = trim((string) $user?->employee?->position);
        if (preg_match('/^Head of Store\s+[12]$/i', $legacyName)) {
            return $legacyName;
        }

        if ($user?->isKoordinatorCreative() && !self::organizationHasHeadOfStorePositions()) {
            return 'Head of Store 1';
        }

        return null;
    }

    public static function isAssignedToHeadOfStore(InfluencerPengajuan $pengajuan, User $headOfStore): bool
    {
        $headPosition = $headOfStore->employee?->mainPosition() ?? $headOfStore->employee?->positions()->first();
        if ($pengajuan->assigned_hos_position_id && $headPosition) {
            return (int) $pengajuan->assigned_hos_position_id === (int) $headPosition->id;
        }

        $assignedName = $pengajuan->assignedHosPosition?->nama
            ?? self::headOfStoreNameForUser($pengajuan->pengaju);
        $headOfStoreName = self::headOfStoreNameForUser($headOfStore);

        return $assignedName && $headOfStoreName
            && strcasecmp(trim($assignedName), trim($headOfStoreName)) === 0;
    }

    public static function pendingCountForHeadOfStore(User $headOfStore): int
    {
        return InfluencerPengajuan::query()
            ->where('status', 'pending_hos1')
            ->with('pengaju.employee')
            ->get()
            ->filter(fn (InfluencerPengajuan $pengajuan) => self::isAssignedToHeadOfStore($pengajuan, $headOfStore))
            ->count();
    }

    public static function pendingCountForCoordinator(User $coordinator): int
    {
        return InfluencerPengajuan::query()
            ->where('status', 'pending_creative')
            ->with('pengaju.employee')
            ->get()
            ->filter(fn (InfluencerPengajuan $pengajuan) => self::isAssignedToCoordinator($pengajuan, $coordinator))
            ->count();
    }

    public static function coordinatorPositionForSubmitter(?User $submitter): ?Position
    {
        $position = $submitter?->employee?->mainPosition();
        $visited = [];

        while ($position && ! isset($visited[$position->id])) {
            $visited[$position->id] = true;
            $position = $position->parent;
            if ($position && preg_match('/^Koordinator Creative(?:\s+.+)?$/i', trim($position->nama))) {
                return $position;
            }
        }

        return null;
    }

    public static function isAssignedToCoordinator(InfluencerPengajuan $pengajuan, ?User $coordinator): bool
    {
        if (! $coordinator?->isKoordinatorCreative()) {
            return false;
        }

        $assignedPosition = self::coordinatorPositionForSubmitter($pengajuan->pengaju);
        if (! $assignedPosition) {
            // Compatibility for organizations that have not yet split the KOL
            // reporting lines into separate coordinator positions.
            return true;
        }

        $coordinatorPosition = $coordinator->employee?->mainPosition();
        return $coordinatorPosition && (int) $coordinatorPosition->id === (int) $assignedPosition->id;
    }

    public static function pendingCountForGeneralManager(): int
    {
        return InfluencerPengajuan::query()->where('status', 'pending_gm')->count();
    }

    public static function organizationHasHeadOfStorePositions(): bool
    {
        return Position::query()->where('nama', 'like', 'Head of Store%')->exists();
    }

    public static function isKolSubmitter(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        $positionName = $user->employee?->mainPosition()?->nama ?? $user->employee?->position ?? '';

        return $user->isStaffCreative() && str_starts_with($positionName, 'Admin KOL');
    }

    public static function kolDivisionsForUser(?User $user): ?array
    {
        if (! self::isKolSubmitter($user)) {
            return null;
        }

        $positionName = trim((string) ($user->employee?->mainPosition()?->nama ?? $user->employee?->position ?? ''));
        foreach (self::DIVISI_KOL as $adminPosition => $divisions) {
            if (strcasecmp($positionName, $adminPosition) === 0) {
                return $divisions;
            }
        }

        return [];
    }

    /**
     * Influencer milik sendiri yang sudah disetujui, tidak termasuk perpanjangan.
     */
    public static function approvedInfluencerIdsFor(User $user)
    {
        return InfluencerPengajuan::query()
            ->where('pengaju_id', $user->id)
            ->where('status', 'approved')
            ->where('is_perpanjangan', false)
            ->whereNotNull('influencer_id')
            ->pluck('influencer_id');
    }
}
