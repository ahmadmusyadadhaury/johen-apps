<?php

namespace App\Livewire;

use App\Support\InfluencerPengajuanRouting;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarInfluencerPengajuanBadge extends Component
{
    #[On('influencer-pengajuan-updated')]
    public function refresh(): void
    {
        // Re-render the current pending count after an approval action.
    }

    public function render()
    {
        $user = auth()->user();
        $position = $user?->isHeadOfStore()
            ? $user->employee?->mainPosition()
            : null;

        $total = $position
            ? InfluencerPengajuanRouting::pendingCountForHeadOfStore($position)
            : 0;

        return view('livewire.sidebar-influencer-pengajuan-badge', compact('total'));
    }
}
