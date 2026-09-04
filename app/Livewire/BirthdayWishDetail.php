<?php

namespace App\Livewire;

use App\Models\BirthdayWish;
use App\Models\Employee;
use Livewire\Component;

class BirthdayWishDetail extends Component
{
    public Employee $employee;

    public ?string $selectedYear = null;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee->loadCount('birthdayWishes');
        $this->selectedYear = (string) now()->year;
    }

    public function delete(int $id): void
    {
        BirthdayWish::where('employee_id', $this->employee->id)
            ->where('id', $id)
            ->delete();

        $this->employee = $this->employee->fresh();
        $this->employee->loadCount('birthdayWishes');

        $this->dispatch('notify', type: 'success', message: 'Ucapan berhasil dihapus.');
    }

    public function render()
    {
        $query = $this->employee->birthdayWishes()
            ->with(['user.employee']);

        if ($this->selectedYear) {
            $query->whereYear('created_at', $this->selectedYear);
        }

        $wishes = $query->latest()->get();

        $availableYears = $this->employee->birthdayWishes()
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderByRaw('YEAR(created_at) DESC')
            ->pluck('year')
            ->toArray();

        return view('livewire.birthday-wish-detail', [
            'wishes' => $wishes,
            'availableYears' => $availableYears,
        ]);
    }
}
