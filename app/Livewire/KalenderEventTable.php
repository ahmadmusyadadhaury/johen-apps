<?php

namespace App\Livewire;

use App\Models\KalenderEvent;
use Livewire\Component;

class KalenderEventTable extends Component
{
    public int $currentMonth;
    public int $currentYear;
    public ?string $selectedDate = null;

    public string $kegiatan = '';
    public string $waktuMulai = '';
    public string $waktuSelesai = '';
    public string $keterangan = '';
    public ?int $editId = null;
    public bool $showForm = false;
    public bool $showDetailModal = false;

    protected function rules(): array
    {
        return [
            'kegiatan' => 'required|string|max:255',
            'waktuMulai' => 'nullable|string|max:255',
            'waktuSelesai' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }

    public function mount(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function goToday(): void
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->selectedDate = null;
    }

    public function nextMonth(): void
    {
        if ($this->currentMonth === 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
        $this->selectedDate = null;
    }

    public function prevMonth(): void
    {
        if ($this->currentMonth === 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
        $this->selectedDate = null;
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->showForm = false;
        $this->editId = null;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedDate = null;
    }

    public function openNew(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->resetInput();
        if (!$this->selectedDate) {
            $this->selectedDate = now()->format('Y-m-d');
        }
        $this->showForm = true;
        $this->showDetailModal = false;
        $this->editId = null;
    }

    public function openEdit(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $event = KalenderEvent::findOrFail($id);
        $this->editId = $event->id;
        $this->kegiatan = $event->kegiatan;
        $this->waktuMulai = $event->waktu_mulai ?? '';
        $this->waktuSelesai = $event->waktu_selesai ?? '';
        $this->keterangan = $event->keterangan ?? '';
        $this->selectedDate = $event->tanggal->format('Y-m-d');
        $this->showForm = true;
        $this->showDetailModal = false;
    }

    public function save(): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        $this->validate();

        $data = [
            'tanggal' => $this->selectedDate,
            'kegiatan' => $this->kegiatan,
            'waktu_mulai' => $this->waktuMulai ?: null,
            'waktu_selesai' => $this->waktuSelesai ?: null,
            'keterangan' => $this->keterangan ?: null,
        ];

        if ($this->editId) {
            KalenderEvent::where('id', $this->editId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Event diperbarui.');
        } else {
            $data['created_by'] = auth()->id();
            KalenderEvent::create($data);
            $this->dispatch('notify', type: 'success', message: 'Event ditambahkan.');
        }

        $this->showForm = false;
        $this->editId = null;
        $this->resetInput();
    }

    public function delete(int $id): void
    {
        abort_unless(!auth()->user()->isReadOnlyWorkspace(), 403);
        KalenderEvent::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Event dihapus.');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editId = null;
        $this->resetInput();
    }

    private function resetInput(): void
    {
        $this->kegiatan = '';
        $this->waktuMulai = '';
        $this->waktuSelesai = '';
        $this->keterangan = '';
    }

    public function render()
    {
        $startOfMonth = now()->setDate($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);

        $days = [];
        $current = $startOfCalendar->copy();
        while ($current->lte($endOfCalendar)) {
            $days[] = $current->copy();
            $current->addDay();
        }

        $events = KalenderEvent::whereYear('tanggal', $this->currentYear)
            ->whereMonth('tanggal', $this->currentMonth)
            ->get()
            ->groupBy(fn($item) => $item->tanggal->format('Y-m-d'));

        $selectedEvents = $this->selectedDate && isset($events[$this->selectedDate])
            ? $events[$this->selectedDate]
            : collect();

        return view('livewire.kalender-event-table', [
            'days' => $days,
            'events' => $events,
            'selectedEvents' => $selectedEvents,
            'monthName' => $startOfMonth->isoFormat('MMMM YYYY'),
            'today' => now()->format('Y-m-d'),
        ]);
    }
}
