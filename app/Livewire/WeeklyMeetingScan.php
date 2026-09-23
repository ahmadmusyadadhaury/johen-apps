<?php

namespace App\Livewire;

use App\Models\WeeklyMeeting;
use App\Models\WeeklyMeetingAttendance;
use App\Models\Employee;
use App\Events\WeeklyMeetingAttendanceCreated;
use Livewire\Component;
use Carbon\Carbon;

class WeeklyMeetingScan extends Component
{
    public string $scannedQr = '';
    public string $status = ''; // success, error, info
    public string $message = '';
    public ?WeeklyMeeting $currentMeeting = null;
    public bool $showScanner = false;
    public string $selectedCamera = ''; // 'user' = depan, 'environment' = belakang
    public bool $alreadyAttended = false;

    protected $listeners = ['qrScanned' => 'handleQrScan'];

    public function mount(): void
    {
        $this->checkActiveMeeting();
    }

    public function checkActiveMeeting(): void
    {
        // First try: meeting for today
        $today = Carbon::today();
        $this->currentMeeting = WeeklyMeeting::where('meeting_date', $today)
            ->where('is_active', true)
            ->first();

        // Fallback: most recent active meeting (within last 7 days)
        if (!$this->currentMeeting) {
            $this->currentMeeting = WeeklyMeeting::where('is_active', true)
                ->where('meeting_date', '>=', Carbon::today()->subDays(7))
                ->orderBy('meeting_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
        }
    }

    public function handleQrScan(string $qrCode): void
    {
        $this->scannedQr = $qrCode;
        $this->processAttendance();
    }

    public function processAttendance(): void
    {
        if (!$this->currentMeeting) {
            $this->status = 'error';
            $this->message = 'Tidak ada rapat mingguan aktif saat ini. Rapat paling lama 7 hari lalu akan ditampilkan.';
            return;
        }

        if ($this->scannedQr !== $this->currentMeeting->qr_code) {
            $this->status = 'error';
            $this->message = 'QR Code tidak valid untuk rapat ini.';
            return;
        }

        $employee = auth()->user()->employee;

        if (!$employee) {
            $this->status = 'error';
            $this->message = 'Akun Anda tidak terhubung ke data karyawan.';
            return;
        }

        // Check if already attended
        $existing = WeeklyMeetingAttendance::where('weekly_meeting_id', $this->currentMeeting->id)
            ->where('employee_id', $employee->id)
            ->first();

        if ($existing) {
            $this->alreadyAttended = true;
            $this->status = 'info';
            $this->message = 'Anda sudah absen pada rapat ini pada ' . $existing->attended_at->format('H:i:s');
            return;
        }

        // Record attendance
        $attendance = WeeklyMeetingAttendance::create([
            'weekly_meeting_id' => $this->currentMeeting->id,
            'employee_id' => $employee->id,
            'attended_at' => Carbon::now(),
            'method' => 'qr_scan',
        ]);

        // Broadcast real-time update to admin master.
        // ShouldBroadcastNow -> synchronous delivery to Reverb for instant UI refresh.
        // Wrapped in try/catch so a Reverb outage never breaks the scan flow.
        try {
            WeeklyMeetingAttendanceCreated::dispatch($attendance);
        } catch (\Throwable $e) {
            report($e);
        }

        $this->status = 'success';
        $this->message = 'Absen berhasil! Selamat datang, ' . $employee->nama;
        $this->alreadyAttended = true;
        $this->showScanner = false;
    }

    public function selectCamera(string $camera): void
    {
        $this->selectedCamera = $camera;
        $this->showScanner = true;
    }

    public function resetScanner(): void
    {
        $this->reset(['scannedQr', 'status', 'message', 'showScanner', 'alreadyAttended']);
        $this->selectedCamera = '';
        $this->showScanner = false;
        $this->checkActiveMeeting();
    }

    public function render()
    {
        return view('livewire.weekly-meeting-scan');
    }
}