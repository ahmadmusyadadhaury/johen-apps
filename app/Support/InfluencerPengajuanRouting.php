<?php

namespace App\Support;

use App\Models\InfluencerPengajuan;
use App\Models\Position;
use App\Models\User;

class InfluencerPengajuanRouting
{
    public static function headOfStorePositionForUser(?User $user): ?Position
    {
        $position = $user?->employee?->mainPosition();
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

    public static function isAssignedToHeadOfStore(InfluencerPengajuan $pengajuan, Position $position): bool
    {
        if ($pengajuan->assigned_hos_position_id) {
            return (int) $pengajuan->assigned_hos_position_id === (int) $position->id;
        }

        return self::headOfStorePositionForUser($pengajuan->pengaju)?->id === $position->id;
    }

    public static function pendingCountForHeadOfStore(Position $position): int
    {
        $assignedCount = InfluencerPengajuan::query()
            ->where('status', 'pending_hos1')
            ->where('assigned_hos_position_id', $position->id)
            ->count();

        $legacyCount = InfluencerPengajuan::query()
            ->where('status', 'pending_hos1')
            ->whereNull('assigned_hos_position_id')
            ->with('pengaju.employee')
            ->get()
            ->filter(fn (InfluencerPengajuan $pengajuan) => self::isAssignedToHeadOfStore($pengajuan, $position))
            ->count();

        return $assignedCount + $legacyCount;
    }
}
