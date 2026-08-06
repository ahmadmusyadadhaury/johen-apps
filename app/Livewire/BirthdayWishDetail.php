<?php

namespace App\Livewire;

use App\Models\BirthdayWish;
use App\Models\Employee;
use Livewire\Component;

class BirthdayWishDetail extends Component
{
    public Employee $employee;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee->loadCount('birthdayWishes');
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
        $wishes = $this->employee->birthdayWishes()
            ->with(['user.employee'])
            ->latest()
            ->get();

        return view('livewire.birthday-wish-detail', ['wishes' => $wishes]);
    }
}
