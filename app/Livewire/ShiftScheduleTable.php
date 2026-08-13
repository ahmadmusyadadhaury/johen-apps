<?php

namespace App\Livewire;

use App\Models\Employee;
use Livewire\Component;

class ShiftScheduleTable extends Component
{
    public Employee $employee;

    public bool $showModal = false;

    public bool $editing = false;

    public ?int $historyId = null;

    public string $effective_date = '';

    public string $jam_kerja = '';

    public string $jam_masuk = '';

    public function mount(Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function openCreate(): void
    {
        abort_unless($this->canManage(), 403);

        $this->resetForm();
        $this->effective_date = now()->toDateString();
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless($this->canManage(), 403);

        $history = $this->employee->shiftHistories()->findOrFail($id);

        $this->historyId = $history->id;
        $this->effective_date = $history->effective_date->toDateString();
        $this->jam_kerja = $history->jam_kerja ?? '';
        $this->jam_masuk = $history->jam_masuk ? substr($history->jam_masuk, 0, 5) : '';
        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        abort_unless($this->canManage(), 403);

        $this->validate([
            'effective_date' => 'required|date|after_or_equal:2000-01-01',
            'jam_kerja' => 'nullable|string|max:255',
            'jam_masuk' => 'nullable|date_format:H:i',
        ]);

        $jamMasuk = $this->jam_masuk ? $this->jam_masuk.':00' : null;

        if ($this->editing && $this->historyId) {
            $history = $this->employee->shiftHistories()->findOrFail($this->historyId);
            $history->update([
                'effective_date' => $this->effective_date,
                'jam_kerja' => $this->jam_kerja ?: null,
                'jam_masuk' => $jamMasuk,
            ]);
        } else {
            $this->employee->recordShiftHistory(
                $this->jam_kerja ?: null,
                $jamMasuk,
                $this->effective_date
            );
        }

        $this->employee = $this->employee->fresh();
        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Jadwal shift berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        abort_unless($this->canManage(), 403);

        $this->employee->shiftHistories()->findOrFail($id)->delete();
        $this->employee = $this->employee->fresh();
        $this->dispatch('notify', type: 'success', message: 'Catatan shift dihapus.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->historyId = null;
        $this->editing = false;
        $this->effective_date = '';
        $this->jam_kerja = '';
        $this->jam_masuk = '';
    }

    private function canManage(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdminLike() || $user->isAnyKoordinator());
    }

    public function render()
    {
        return view('livewire.shift-schedule-table', [
            'histories' => $this->employee->shiftHistories()->get(),
            'canManage' => $this->canManage(),
        ]);
    }
}
