<?php

namespace App\Livewire;

use App\Models\Employee;
use Carbon\Carbon;
use Livewire\Component;

class SidebarBirthdayDot extends Component
{
    public function render()
    {
        $now = Carbon::now();
        $from = $now->copy()->startOfDay();
        $until = $now->copy()->addDays(7)->endOfDay();

        $hasUpcoming = Employee::query()
            ->where('tipe', 'karyawan_aktif')
            ->whereNotNull('tanggal_lahir')
            ->get(['id', 'tanggal_lahir'])
            ->filter(function (Employee $emp) use ($from, $until) {
                $birthday = $emp->tanggal_lahir;
                $nextBirthday = $birthday->copy()->year($from->year);
                if ($nextBirthday->lt($from)) {
                    $nextBirthday = $birthday->copy()->year($from->year + 1);
                }
                return $nextBirthday->between($from, $until);
            })
            ->isNotEmpty();

        return view('livewire.sidebar-birthday-dot', [
            'hasUpcoming' => $hasUpcoming,
        ]);
    }
}
