<?php

namespace App\Livewire;

use App\Models\PositionNote;
use Livewire\Component;

class SidebarPositionNoteBadge extends Component
{
    public function render()
    {
        $user = auth()->user();
        $count = 0;

        if ($user && $user->employee) {
            $positionIds = $user->employee->positions()->pluck('positions.id')->all();
            $count = PositionNote::unseenCountForPositions($positionIds, $user->id);
        }

        return view('livewire.sidebar-position-note-badge', [
            'count' => $count,
        ]);
    }
}