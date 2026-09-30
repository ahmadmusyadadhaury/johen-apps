<?php

namespace App\Livewire;

use App\Models\InfluencerMonitoring;
use App\Support\InfluencerPengajuanRouting;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarInfluencerMonitoringBadge extends Component
{
    #[On('influencer-monitoring-updated')]
    public function refresh(): void
    {
        // Re-render the pending count after a monitoring is saved.
    }

    public function render()
    {
        return view('livewire.sidebar-influencer-monitoring-badge', [
            'count' => $this->countMissingCurrentMonth(),
        ]);
    }

    /**
     * Jumlah influencer milik Admin KOL yang login yang belum punya monitoring
     * untuk bulan berjalan. Pola diff yang sama dengan SidebarKontrakBadge.
     */
    private function countMissingCurrentMonth(): int
    {
        $user = auth()->user();

        if (! InfluencerPengajuanRouting::isKolSubmitter($user)) {
            return 0;
        }

        $ownInfluencerIds = InfluencerPengajuanRouting::approvedInfluencerIdsFor($user);

        if ($ownInfluencerIds->isEmpty()) {
            return 0;
        }

        $filledInfluencerIds = InfluencerMonitoring::query()
            ->whereIn('influencer_id', $ownInfluencerIds)
            ->whereDate('period_month', now()->startOfMonth()->toDateString())
            ->distinct()
            ->pluck('influencer_id');

        return $ownInfluencerIds->diff($filledInfluencerIds)->count();
    }
}
