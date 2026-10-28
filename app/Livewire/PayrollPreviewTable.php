<?php

namespace App\Livewire;

use App\Models\PayrollImport;
use Livewire\Component;
use Livewire\WithPagination;

class PayrollPreviewTable extends Component
{
    use WithPagination;

    public PayrollImport $import;
    public string $search = '';
    public string $sortField = 'nik';
    public string $sortDirection = 'asc';


    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $details = $this->import->payrollDetails()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nik', 'like', "%{$this->search}%")
                      ->orWhere('nama', 'like', "%{$this->search}%")
                      ->orWhere('divisi', 'like', "%{$this->search}%")
                      ->orWhere('jabatan', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.payroll-preview-table', [
            'details' => $details,
            'totalPayroll' => $this->import->payrollDetails->sum('take_home_pay'),
        ]);
    }
}
