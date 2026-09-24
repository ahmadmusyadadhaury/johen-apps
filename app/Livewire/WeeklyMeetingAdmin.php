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

    // Tab "Weekly Saya" — saat staff_hr memilih menu Weekly Meeting, tampilkan
    // 2 tab: "QR Code" (kelola daftar rapat) dan "Weekly Saya" (scan QR absen).
    public string $tab = 'qr'; // qr | weekly

    public function setTab(string $tab): void
    {
        $this->tab = $tab === 'weekly' ? 'weekly' : 'qr';
    }

    // Tab "QR Code" / "Weekly Saya" hanya untuk Super Admin & Staff HR.
    public function getShowTabsProperty(): bool
    {
        return auth()->user()->isSuperAdmin() || auth()->user()->isStaffHr();
    }

    // Form fields
    public string $title = 'Weekly Meeting';
    public string $meeting_date = '';
    public string $start_time = '13:00';
    public string $end_time = '14:30';
    public string $location = 'Ruangan Meeting Lantai 1';
    public string $description = '';

    public bool $showModal = false;
    public bool $showAttendanceModal = false;

    // Filter list berdasarkan bulan & tahun
    public string $filterMonth = '';
    public string $filterYear = '';

    public function updatedFilterMonth(): void
    {
        $this->resetPage();
    }

    public function updatedFilterYear(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filterMonth = '';
        $this->filterYear = '';
        $this->resetPage();
    }

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
        $this->start_time = '13:00';
        $this->end_time = '14:30';

        $routeName = request()->route()->getName() ?? '';

        if (str_contains($routeName, '.create')) {
            $this->mode = 'list';
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
        $this->dispatch('close-modal', name: 'meeting-form');
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
        $this->mode = 'list';
        $this->showModal = true;
        $this->dispatch('open-modal', name: 'meeting-form');
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
        $this->dispatch('close-modal', name: 'meeting-form');
        $this->dispatch('notify', type: 'success', message: 'Rapat mingguan berhasil diperbarui.');
    }

    public function viewAttendance(int $meetingId): void
    {
        // Navigasi ke rute detail attendance agar URL ikut berubah. Dengan
        // begitu ketika halaman di-refresh, user tetap di halaman detail
        // (yang berisi QR) bukan kembali ke daftar.
        $this->redirectRoute('hris.weekly-meeting.attendance', ['weeklyMeeting' => $meetingId]);
    }

    public function backToList(): void
    {
        $this->redirectRoute('hris.weekly-meeting.index');
    }

    public function generateQrCode(): void
    {
        $meeting = WeeklyMeeting::findOrFail($this->selectedMeetingId);
        $meeting->generateQrCode();
        $this->dispatch('notify', type: 'success', message: 'QR Code berhasil digenerate ulang.');
    }

    /**
     * Dipanggil otomatis oleh wire:poll.60s pada mode attendance.
     * Regenerate QR setiap menit tanpa spam notifikasi.
     */
    public function regenerateQrAuto(): void
    {
        if ($this->mode === 'attendance' && $this->selectedMeetingId) {
            try {
                $meeting = WeeklyMeeting::findOrFail($this->selectedMeetingId);
                $meeting->generateQrCode();
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * Dipicu wire:poll.3s pada kartu absensi di mode attendance sehingga data
     * peserta yang baru selesai scan langsung muncul tanpa refresh halaman.
     * No-op: Livewire otomatis re-render setelah aksi, dan properti computed
     * ($attendance) diambil fresh dari database.
     */
    public function refreshAttendanceData(): void
    {
    }

    public function deleteMeeting(int $meetingId): void
    {
        WeeklyMeeting::findOrFail($meetingId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Rapat mingguan berhasil dihapus.');
    }

    /**
     * Livewire Live modal delete (x:confirm-delete-modal):
     *  - confirmDelete   => buka modal konfirmasi
     *  - cancelDelete    => batal
     *  - executeDelete   => hapus
     *
     * Semua metode ini DIPANGGIL oleh komponen `confirm-delete-modal`
     * (showDeleteConfirm / cancelDelete / executeDelete) sehingga tombol
     * "Hapus" di bladenya selalu punya handler di sisi komponen.
     */
    public bool $showDeleteConfirm = false;
    public ?int $deleteTargetId = null;

    public function confirmDelete(int $meetingId): void
    {
        $this->deleteTargetId = $meetingId;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteTargetId = null;
        $this->showDeleteConfirm = false;
    }

    public function executeDelete(): void
    {
        if ($this->deleteTargetId) {
            WeeklyMeeting::findOrFail($this->deleteTargetId)->delete();
        }
        $this->cancelDelete();
        $this->dispatch('notify', type: 'success', message: 'Rapat mingguan berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'meeting_date', 'start_time', 'end_time', 'location', 'description', 'selectedMeetingId', 'showModal', 'showAttendanceModal']);
        $this->meeting_date = Carbon::today()->format('Y-m-d');
        $this->start_time = '13:00';
        $this->end_time = '14:30';
        $this->resetValidation();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->mode = 'list';
        $this->showModal = true;
        $this->dispatch('open-modal', name: 'meeting-form');
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->mode = 'list';
        $this->showModal = false;
        $this->dispatch('close-modal', name: 'meeting-form');
    }

    /**
     * Listen for real-time attendance updates via Laravel Echo/Reverb.
     * Livewire auto re-renders after this listener returns, so the
     * computed attendance/meetings properties fetch fresh data.
     */
    #[On('echo:weekly-meeting.admin,WeeklyMeetingAttendanceCreated')]
    public function refreshAttendance(array $data): void
    {
        $this->dispatch('notify', type: 'info', message: "{$data['employee_name']} baru saja hadir ({$data['attended_at']})");
    }

    public function getMeetingsProperty()
    {
        return WeeklyMeeting::with(['creator', 'attendances.employee'])
            ->withCount('attendances')
            ->when($this->filterMonth !== '', fn ($q) => $q->whereMonth('meeting_date', $this->filterMonth))
            ->when($this->filterYear !== '', fn ($q) => $q->whereYear('meeting_date', $this->filterYear))
            ->orderBy('meeting_date', 'asc')
            ->orderBy('created_at', 'asc')
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

    /**
     * QR Code "terkunci" (overlay hitam) selama belum mendekati jadwal rapat,
     * yaitu sebelum 15 menit jelang jam mulai pada hari jadwal. Status ini
     * dihitung fresh tiap render sehingga otomatis terbuka saat polling
     * 3 detik masuk jendela aktif.
     */
    public function getQrLockedProperty(): bool
    {
        if (!$this->selectedMeetingId) {
            return false;
        }

        $meeting = WeeklyMeeting::find($this->selectedMeetingId);

        if (!$meeting || !$meeting->start_time) {
            return false;
        }

        $startAt = Carbon::parse($meeting->meeting_date->toDateString() . ' ' . $meeting->start_time->format('H:i'))->subMinutes(15);

        return Carbon::now()->lt($startAt);
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