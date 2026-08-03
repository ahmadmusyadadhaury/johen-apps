<?php

namespace App\Livewire;

use App\Models\EmployeeContract;
use Livewire\Component;
use Livewire\WithPagination;

class KontrakKerjaTable extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $totalAktif = EmployeeContract::where('status', 'berlaku')
            ->whereDate('tanggal_berakhir', '>=', now())->count();
        $akanBerakhir = EmployeeContract::where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(7))
            ->where('tanggal_berakhir', '>=', now())
            ->count();
        $urgent = EmployeeContract::where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(3))
            ->where('tanggal_berakhir', '>=', now())
            ->count();
        $totalSelesai = EmployeeContract::where('status', 'selesai')->count();

        $segeraHabis = EmployeeContract::with('employee.divisions')
            ->where('status', 'berlaku')
            ->where('tanggal_berakhir', '<=', now()->addDays(7))
            ->where('tanggal_berakhir', '>=', now())
            ->get();

        $contracts = EmployeeContract::with('employee.divisions')
            ->where('status', 'berlaku')
            ->whereDate('tanggal_berakhir', '>=', now())
            ->when($this->search, function ($query) {
                $query->whereHas('employee', function ($q) {
                    $q->where('nama', 'like', "%{$this->search}%")
                      ->orWhere('nik', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('tanggal_berakhir', 'asc')
            ->paginate(10);

        return view('livewire.kontrak-kerja-table', compact(
            'contracts', 'totalAktif', 'akanBerakhir', 'urgent', 'totalSelesai', 'segeraHabis'
        ));
    }
}
