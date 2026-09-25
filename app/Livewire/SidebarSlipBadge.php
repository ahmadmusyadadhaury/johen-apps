<?php

namespace App\Livewire;

use App\Models\PayrollDetail;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarSlipBadge extends Component
{
    public function render()
    {
        $count = 0;

        $user = auth()->user();
        if ($user && $user->employee_id !== null) {
            $count = PayrollDetail::query()
                ->where('employee_id', $user->employee_id)
                ->where('status', 'sent')
                ->whereNull('read_at')
                ->count();
        }

        return view('livewire.sidebar-slip-badge', ['count' => $count]);
    }

    #[On('payroll-slip-read')]
    public function refresh(): void
    {
        //
    }
}