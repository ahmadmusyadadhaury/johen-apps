<?php

namespace App\Livewire;

use App\Models\LeaveRequest;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarLeaveBadge extends Component
{
    public function render()
    {
        $user = auth()->user();
        $leaveRequestMenungguCount = 0;

        if ($user && ($user->isSuperAdmin() || $user->isGmCeo())) {
            $leaveRequestMenungguCount = LeaveRequest::where('persetujuan_hr', 'menunggu')->count();
        } elseif ($user && $user->employee) {
            $employeeId = $user->employee->id;
            $leaveRequestMenungguCount = LeaveRequest::where(function ($q) use ($employeeId) {
                $q->where('atasan_id', $employeeId)
                  ->where('persetujuan_koor', 'menunggu');
            })->orWhere(function ($q) use ($employeeId) {
                $q->where('atasan2_id', $employeeId)
                  ->where('persetujuan_atasan2', 'menunggu');
            })->count();
        }

        return view('livewire.sidebar-leave-badge', [
            'leaveRequestMenungguCount' => $leaveRequestMenungguCount,
        ]);
    }

    #[On('leave-request-updated')]
    public function refresh(): void
    {
        //
    }
}
