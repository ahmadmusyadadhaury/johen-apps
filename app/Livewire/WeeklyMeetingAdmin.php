<?php

namespace App\Livewire;

use App\Models\WeeklyMeeting;
use App\Models\WeeklyMeetingAttendance;
use App\Models\Employee;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class WeeklyMeetingAdmin extends Component
{
    use WithPagination;

    public string $mode = 'list'; // list, create, attendance
    public ?int $selectedMeetingId = null;

    // Form fields
    public string $title = '';
    public string $meeting_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $location = '';
    public string $description = '';

    public bool $showModal = false;
    public bool $showAttendanceModal = false;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:500',
            'description' => 'nullable|string',
        ];
    }

    public function mount(?WeeklyMeeting $weeklyMeeting = null): void
    {
        $this->meeting_date = Carbon::today()->format('Y-m-d');
        $this->start_time = '09:00';
        $this->end_time = '11:00';

        $routeName = request()->route()->getName() ?? '';

        if (str_contains($routeName, '.create')) {
            $this->mode = 'create';
            $this->showModal = true;
        } elseif (str_contains($routeName, '.attendance') && $weeklyMeeting) {
            $this->mode = 'attendance';
            $this->selectedMeetingId = $weeklyMeeting->id;
        } else {
            $this->mode = 'list';
        }
    }

    public function createMeeting(): void
    {
        $this->validate();

        $meeting = WeeklyMeeting::create([
            'title' => $this->title,
            'meeting_date' => $this->meeting_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'description' => $this->description,
            'created_by' => auth()->id(),
            'is_active' => true,
        ]);

        $meeting->generateQrCode();

        $this->resetForm();
        $this->mode = 'list';
        $this->dispatch('notify', type: 'success', message: 'Rapat mingguan berhasil dibuat dan QR code digenerate.');
    }

    public function editMeeting(int $meetingId): void
    {
        $meeting = WeeklyMeeting::findOrFail($meetingId);
        $this->selectedMeetingId = $meeting->id;
        $this->title = $meeting->title;
        $this->meeting_date = $meeting->meeting_date->format('Y-m-d');
        $this->start_time = $meeting->start_time?->format('H:i') ?? '';
        $this->end_time = $meeting->end_time?->format('H:i') ?? '';
        $this->location = $meeting->location ?? '';
        $this->description = $meeting->description ?? '';
        $this->mode = 'create';
        $this->showModal = true;
    }

    public function updateMeeting(): void
    {
        $this->validate();

        $meeting = WeeklyMeeting::findOrFail($this->selectedMeetingId);
        $meeting->update([
            'title' => $this->title,
            'meeting_date' => $this->meeting_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->location,
            'description' => $this->description,
        ]);

        $this->resetForm();
        $this->mode = 'list';
        $this->dispatch('notify', type: 'success', message: 'Rapat mingguan berhasil diperbarui.');
    }

    public function viewAttendance(int $meetingId): void
    {
        $this->selectedMeetingId = $meetingId;
        $this->mode = 'attendance';
    }

    public function generateQrCode(): void
    {
        $meeting = WeeklyMeeting::findOrFail($this->selectedMeetingId);
        $meeting->generateQrCode();
        $this->dispatch('notify', type: 'success', message: 'QR Code berhasil digenerate ulang.');
    }

    public function deleteMeeting(int $meetingId): void
    {
        WeeklyMeeting::findOrFail($meetingId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Rapat mingguan berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'meeting_date', 'start_time', 'end_time', 'location', 'description', 'selectedMeetingId', 'showModal', 'showAttendanceModal']);
        $this->meeting_date = Carbon::today()->format('Y-m-d');
        $this->start_time = '09:00';
        $this->end_time = '11:00';
        $this->resetValidation();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->mode = 'create';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->mode = 'list';
        $this->showModal = false;
    }

    /**
     * Listen for real-time attendance updates via Laravel Echo/Reverb
     */
    #[On('echo:weekly-meeting.admin,WeeklyMeetingAttendanceCreated')]
    public function refreshAttendance(array $data): void
    {
        // Only refresh if the event is for the currently viewed meeting
        if ($this->mode === 'attendance' && $this->selectedMeetingId == ($data['meeting_id'] ?? null)) {
            $this->dispatch('notify', type: 'info', message: "{$data['employee_name']} baru saja hadir ({$data['attended_at']})");
            // Force re-render by touching a dummy property
            $this->render();
        }
    }

    public function getMeetingsProperty()
    {
        return WeeklyMeeting::with(['creator', 'attendances.employee'])
            ->withCount('attendances')
            ->orderBy('meeting_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getAttendanceProperty()
    {
        if (!$this->selectedMeetingId) {
            return collect();
        }

        $meeting = WeeklyMeeting::with(['attendances.employee'])->findOrFail($this->selectedMeetingId);
        return $meeting->attendances()->with('employee')->orderBy('attended_at')->get();
    }

    public function getTotalEmployeesProperty(): int
    {
        return Employee::where('tipe', Employee::TIPE_KARYAWAN_AKTIF)->count();
    }

    public function render()
    {
        return view('livewire.weekly-meeting-admin', [
            'meetings' => $this->meetings,
            'attendance' => $this->attendance,
            'totalEmployees' => $this->totalEmployees,
        ]);
    }
}