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

    /**
     * Geolokasi device pengguna saat melakukan scan, dikirim dari
     * navigator.geolocation via JavaScript (dengan fallback ke lokasi rapat
     * bila GPS tidak diizinkan / tidak tersedia).
     */
    public ?string $deviceLocation = null;

    protected $listeners = ['qrScanned' => 'handleQrScan'];

    public function mount(): void
    {
        $this->checkActiveMeeting();
    }

    public function checkActiveMeeting(): void
    {
        $now = Carbon::now();

        // Rapat dianggap "aktif" hanya selama jadwalnya belum lewat jam selesai:
        // - hari ini -> masih berlaku selama end_time belum terlewati
        // - hari berikutnya -> rapat terdekat yang masih di depan
        // - jika semuanya sudah lewat -> null, user melihat "Belum ada weekly meeting"
        $this->currentMeeting = WeeklyMeeting::where('is_active', true)
            ->whereDate('meeting_date', '>=', $now->toDateString())
            ->where(function ($query) use ($now) {
                $query->whereDate('meeting_date', '>', $now->toDateString())
                    ->orWhereNull('end_time')
                    ->orWhereTime('end_time', '>', $now->toTimeString());
            })
            ->orderBy('meeting_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->first();
    }

    public function handleQrScan(string $qrCode, ?string $deviceLocation = null): void
    {
        $this->scannedQr = $qrCode;
        $this->deviceLocation = $deviceLocation;
        $this->processAttendance();
    }

    public function processAttendance(): void
    {
        if (!$this->currentMeeting) {
            $this->status = 'error';
            $this->message = 'Belum ada weekly meeting yang tersedia untuk saat ini.';
            return;
        }

        // QR diubah otomatis oleh admin (auto-regen tiap 1 menit), jadi harus
        // selalu bandingkan dengan nilai QR paling baru dari DB, bukan yang
        // tersimpan di memori komponen saat mount.
        $latestQr = WeeklyMeeting::find($this->currentMeeting->id)?->qr_code;

        if ($this->scannedQr !== $latestQr) {
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
            'device_location' => $this->deviceLocation,
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

        // Beri tahu client bahwa scanner box sudah dirender (#scanner-video ada
        // di DOM) sehingga kamera bisa dinyalakan tanpa race/hilang video.
        $this->dispatch('camera-selected', camera: $camera);
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