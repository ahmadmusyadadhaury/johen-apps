<?php

namespace App\Livewire;

use App\Models\BirthdayWish;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class BirthdayWishTable extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Karyawan aktif yang ulang tahunnya jatuh dalam "h" hari ke depan
     * (berdasarkan tanggal_lahir di menu Karyawan, abaikan tahun).
     * Diproses di PHP agar benar melintasi pergantian tahun.
     */
    private function getUpcomingBirthdays(Carbon $now, int $days): Collection
    {
        $from = $now->copy()->startOfDay();
        $until = $now->copy()->addDays($days)->endOfDay();

        return Employee::query()
            ->where('tipe', 'karyawan_aktif')
            ->whereNotNull('tanggal_lahir')
            ->get(['id', 'nama', 'position', 'tanggal_lahir', 'foto'])
            ->map(function (Employee $emp) use ($from) {
                $birthday = $emp->tanggal_lahir;
                $nextBirthday = $birthday->copy()->year($from->year);

                // Jika sudah lewat di tahun ini, geser ke tahun depan.
                if ($nextBirthday->lt($from)) {
                    $nextBirthday = $birthday->copy()->year($from->year + 1);
                }

                $emp->next_birthday = $nextBirthday;

                return $emp;
            })
            ->filter(fn (Employee $emp) => $emp->next_birthday->between($from, $until))
            ->sortBy('next_birthday')
            ->values();
    }

    public function render()
    {
        $employees = Employee::withCount('birthdayWishes')
            ->withMax('birthdayWishes as last_wish_at', 'created_at')
            ->whereHas('birthdayWishes')
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('nama', 'like', "%{$this->search}%")
                        ->orWhere('position', 'like', "%{$this->search}%")
                        ->orWhere('nik', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('last_wish_at')
            ->paginate(12);

        $now = now();

        $stats = BirthdayWish::query()
            ->selectRaw('COUNT(*) as total_ucapan')
            ->selectRaw('COUNT(DISTINCT employee_id) as karyawan_diucapkan')
            ->selectRaw('COALESCE(SUM(MONTH(created_at) = ? AND YEAR(created_at) = ?), 0) as ucapan_bulan_ini', [$now->month, $now->year])
            ->first();

        $ultahBulanIni = Employee::query()
            ->where('tipe', 'karyawan_aktif')
            ->whereMonth('tanggal_lahir', $now->month)
            ->count();

        $upcomingBirthdays = $this->getUpcomingBirthdays($now, 7);

        return view('livewire.birthday-wish-table', [
            'employees' => $employees,
            'stats' => $stats,
            'ultahBulanIni' => $ultahBulanIni,
            'upcomingBirthdays' => $upcomingBirthdays,
        ]);
    }
}
