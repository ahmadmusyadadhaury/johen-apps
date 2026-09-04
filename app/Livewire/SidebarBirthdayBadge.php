<?php

namespace App\Livewire;

use App\Models\Employee;
use Carbon\Carbon;
use Livewire\Component;

class SidebarBirthdayBadge extends Component
{
    public function render()
    {
        $now = Carbon::now();
        $from = $now->copy()->startOfDay();
        $until = $now->copy()->addDays(7)->endOfDay();

        $upcomingCount = Employee::query()
            ->where('tipe', 'karyawan_aktif')
            ->whereNotNull('tanggal_lahir')
            ->get(['id', 'tanggal_lahir'])
            ->filter(function (Employee $emp) use ($from) {
                $birthday = $emp->tanggal_lahir;
                $nextBirthday = $birthday->copy()->year($from->year);
                if ($nextBirthday->lt($from)) {
                    $nextBirthday = $birthday->copy()->year($from->year + 1);
                }
                $emp->next_birthday = $nextBirthday;
                return $emp->next_birthday->between($from, $from->copy()->addDays(7)->endOfDay());
            })
            ->count();

        return view('livewire.sidebar-birthday-badge', [
            'upcomingCount' => $upcomingCount,
        ]);
    }
}
