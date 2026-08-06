<?php

namespace App\Livewire;

use App\Models\Employee;
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

        return view('livewire.birthday-wish-table', ['employees' => $employees]);
    }
}
