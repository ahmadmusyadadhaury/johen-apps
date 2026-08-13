<?php

namespace App\Livewire;

use App\Models\AttendanceSession;
use Livewire\Component;

class PresensiHostLive extends Component
{
    public string $tanggal = '';

    public int $sesi = 0;

    public string $jamMulai = '';

    public string $jamSelesai = '';

    public function mount(): void
    {
        $this->refreshNow();
    }

    public function refreshNow(): void
    {
        $detect = AttendanceSession::detectNow();
        $this->tanggal = $detect['tanggal'];
        $this->sesi = (int) $detect['sesi'];

        $config = AttendanceSession::sessionConfig($this->sesi);
        if ($config) {
            $this->jamMulai = $config['mulai'];
            $this->jamSelesai = $config['selesai_display'] ?? $config['selesai'];
        }
    }

    public function mulaiSesi(): void
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            $this->dispatch('notify', type: 'error', message: 'Akun Anda tidak terhubung ke data karyawan.');

            return;
        }

        $now = now();
        $hour = (int) $now->format('G');

        // Cek sesi yang masih terbuka (belum check-out).
        $open = AttendanceSession::where('employee_id', $employee->id)
            ->whereNull('clock_out')
            ->first();

        if ($open) {
            // Punch dini hari (00:00-06:59) = check-out "pulang" yang baru
            // melewati tengah malam (misal Sesi 3/Malam mulai kemarin, keluar
            // besok jam 00:46). Tutup otomatis sebagai ABSEN KELUAR pada
            // tanggal yang sama dengan check-in, bukan absen masuk baru.
            if ($hour < 7) {
                $open->clock_out = $now->format('H:i:s');
                $open->save();
                $this->dispatch('notify', type: 'success', message: 'Check-out '.$open->namaSesi().' ('.$open->tanggal->format('d M Y').', keluar '.$open->clock_out.') tercatat sebagai ABSEN PULANG, bukan absen masuk.');

                return;
            }

            $this->dispatch('notify', type: 'error', message: 'Anda masih punya '.$open->namaSesi().' yang belum ditutup ('.$open->tanggal->format('d M Y').', masuk '.$open->clock_in.'). Tutup sesi tersebut terlebih dahulu agar dihitung sebagai check-out.');

            return;
        }

        $detect = AttendanceSession::detectNow();

        $row = AttendanceSession::firstOrNew([
            'employee_id' => $employee->id,
            'tanggal' => $detect['tanggal'],
            'sesi' => (int) $detect['sesi'],
        ]);

        if ($row->exists && $row->clock_in) {
            $this->dispatch('notify', type: 'error', message: 'Anda sudah check-in untuk '.$row->namaSesi().' hari ini.');

            return;
        }

        $row->clock_in = $now->format('H:i:s');
        $row->status = 'hadir';
        $row->created_by = auth()->id();
        $row->late_minutes = AttendanceSession::hitungTelat((int) $detect['sesi'], $row->clock_in);
        $row->save();

        $this->dispatch('notify', type: 'success', message: 'Check-in '.$row->namaSesi().' ('.$this->tanggal.') berhasil.');
    }

    public function selesaiSesi(int $id): void
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            $this->dispatch('notify', type: 'error', message: 'Akun Anda tidak terhubung ke data karyawan.');

            return;
        }

        $row = AttendanceSession::where('id', $id)
            ->where('employee_id', $employee->id)
            ->whereNull('clock_out')
            ->first();

        if (! $row) {
            $this->dispatch('notify', type: 'error', message: 'Sesi tidak ditemukan atau sudah selesai.');

            return;
        }

        $row->clock_out = now()->format('H:i:s');
        $row->save();

        $this->dispatch('notify', type: 'success', message: 'Check-out '.$row->namaSesi().' berhasil.');
    }

    public function render()
    {
        $user = auth()->user();
        $employee = $user?->employee;

        $activeSessions = collect();
        $mySessions = collect();
        $stats = [
            'total' => 0,
            'hadir' => 0,
            'terlambat' => 0,
        ];

        if ($employee) {
            $activeSessions = AttendanceSession::where('employee_id', $employee->id)
                ->whereNull('clock_out')
                ->orderByDesc('tanggal')
                ->orderBy('sesi')
                ->get();

            $mySessions = AttendanceSession::where('employee_id', $employee->id)
                ->where('tanggal', '>=', now()->subDay()->toDateString())
                ->orderByDesc('tanggal')
                ->orderBy('sesi')
                ->get();

            $stats['total'] = $mySessions->count();
            $stats['hadir'] = $mySessions->where('status', 'hadir')->count();
            $stats['terlambat'] = $mySessions->filter(fn ($s) => $s->status === 'hadir' && $s->isTelat())->count();
        }

        return view('livewire.presensi-host-live', compact('employee', 'activeSessions', 'mySessions', 'stats'));
    }
}
