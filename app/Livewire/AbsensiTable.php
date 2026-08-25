<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class AbsensiTable extends Component
{
    use WithPagination;

    protected $queryString = ['date', 'search', 'tab', 'periode'];

    public string $date = '';

    public string $search = '';

    public string $tab = 'saya';

    /** Bulan periode presensi (format Y-m). Periode = tgl 26 bulan lalu s.d. tgl 25 bulan terpilih. */
    public string $periode = '';

    public array $statsMembers = ['tepat' => [], 'terlambat' => []];

    public string $statsDateLabel = '';

    public bool $showJamKerjaModal = false;

    public bool $showDetailModal = false;

    /** Baris punch mentah (jam, tanggal, metode) untuk modal detail absen. */
    public array $detailPunches = [];

    public ?string $detailDate = null;

    public string $detailNama = '';

    public ?int $jamKerjaEmployeeId = null;

    public string $jam_kerja = '';

    public string $jam_masuk = '';

    public string $effective_date = '';

    public function mount(): void
    {
        if ($this->date === '') {
            $this->date = now()->format('Y-m-d');
        }

        if ($this->periode === '') {
            $this->periode = now()->format('Y-m');
        }

        if ($this->tab === 'sinkron' && ! auth()->user()?->isSuperAdminLike()) {
            $this->tab = 'tim';
        }
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function updatedPeriode(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDate(): void
    {
        $this->resetPage();
    }

    public function updatedJamKerja($value): void
    {
        // Pilih shift otomatis mengisi jam masuk sebagai acuan telat.
        if ($value && isset(Employee::SHIFT_OPTIONS[$value])) {
            $this->jam_masuk = Employee::SHIFT_OPTIONS[$value];
        }
    }

    public function openJamKerjaModal(int $employeeId): void
    {
        $user = auth()->user();

        if (! $user->isAnyKoordinator() || $this->tab !== 'tim') {
            abort(403);
        }

        $ids = $this->getSubordinateEmployeeIds();
        if (! in_array($employeeId, $ids)) {
            abort(403);
        }

        $emp = Employee::findOrFail($employeeId);
        $this->jamKerjaEmployeeId = $emp->id;
        $this->jam_kerja = $emp->jam_kerja ?? '';
        $this->jam_masuk = $emp->jam_masuk ? substr($emp->jam_masuk, 0, 5) : '';
        $this->effective_date = now()->toDateString();
        $this->showJamKerjaModal = true;
    }

    public function closeJamKerjaModal(): void
    {
        $this->showJamKerjaModal = false;
        $this->jamKerjaEmployeeId = null;
        $this->jam_kerja = '';
        $this->jam_masuk = '';
        $this->effective_date = '';
        $this->resetErrorBag();
    }

    /**
     * Modal detail absen: tampilkan SEMUA punch mentah karyawan pada hari
     * kerja tersebut (jendela [tgl 00:00, tgl+1 07:00) mengikuti konvensi
     * sesi lintas malam/subuh), sehingga tap berulang tidak saling menimpa —
     * absen datang tetap terlihat walau tap pulang 2-3 kali.
     */
    public function openDetail(int $employeeId, string $date): void
    {
        $user = auth()->user();
        $employee = Employee::find($employeeId);

        if (! $employee || $employee->id !== $user->employee?->id) {
            $allowed = ($user->isAnyKoordinator() || $user->isHeadOfStore())
                && in_array($employeeId, $this->getSubordinateEmployeeIds());

            if (! $allowed && ! $user->isSuperAdminLike()) {
                abort(403);
            }
        }

        try {
            $day = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        } catch (\Throwable) {
            return;
        }

        $start = $day->copy();
        $end = $day->copy()->addDay()->setTime((int) config('attendance.overnight_latest_checkout_hour', 7), 0);

        $punches = AttendancePunch::where('employee_id', $employee->id)
            ->where('punch_at', '>=', $start->toDateTimeString())
            ->where('punch_at', '<', $end->toDateTimeString())
            ->orderBy('punch_at')
            ->get()
            ->values();

        // Rangkaian tap ganda: punch berjarak < window dianggap satu rangkaian
        // (mis. dobel tap datang 2 detik). Hanya pembuka rangkaian pertama
        // berlabel Datang dan pembuka rangkaian terakhir berlabel Pulang
        // (bila ada rangkaian berikutnya); sisanya cukup berlabel Tap.
        $count = $punches->count();
        $window = (int) config('attendance.tap_duplicate_window_seconds', 180);
        $clusterStarts = [0];

        for ($i = 1; $i < $count; $i++) {
            if ($punches[$i]->punch_at->getTimestamp() - $punches[$i - 1]->punch_at->getTimestamp() >= $window) {
                $clusterStarts[] = $i;
            }
        }

        $this->detailPunches = $punches->map(function (AttendancePunch $p, int $i) use ($punches, $start, $clusterStarts) {
            $at = $p->punch_at;

            return [
                'jam' => $at->format('H:i:s'),
                'tanggal' => $at->locale('id')->isoFormat('D MMM'),
                'dini_hari' => $at->toDateString() !== $start->toDateString(),
                'metode' => match ($p->method) {
                    'finger' => 'Fingerprint',
                    'face' => 'Wajah',
                    'card' => 'Kartu',
                    default => ucfirst((string) $p->method),
                },
                'tipe' => match (true) {
                    $i === 0 => 'Datang',
                    $i === $punches->count() - 1 && in_array($i, $clusterStarts, true) => 'Pulang',
                    default => 'Tap',
                },
            ];
        })->all();

        $this->detailNama = $employee->nama;
        $this->detailDate = $day->isoFormat('dddd, D MMMM Y');
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailPunches = [];
        $this->detailDate = null;
        $this->detailNama = '';
    }

    public function saveJamKerja(): void
    {
        $user = auth()->user();

        if (! $user->isAnyKoordinator() || $this->tab !== 'tim') {
            abort(403);
        }

        $ids = $this->getSubordinateEmployeeIds();
        if (! $this->jamKerjaEmployeeId || ! in_array($this->jamKerjaEmployeeId, $ids)) {
            abort(403);
        }

        $this->validate([
            'jam_kerja' => 'nullable|string|max:255',
            'jam_masuk' => 'nullable|date_format:H:i',
            'effective_date' => 'required|date|before_or_equal:today|after_or_equal:2000-01-01',
        ]);

        $emp = Employee::findOrFail($this->jamKerjaEmployeeId);
        $emp->setJamKerja(
            $this->jam_kerja ?: null,
            $this->jam_masuk ? $this->jam_masuk.':00' : null,
            $this->effective_date
        );

        $this->closeJamKerjaModal();
        $this->dispatch('notify', type: 'success', message: 'Jam kerja '.$emp->nama.' berhasil diperbarui.');
    }

    public function render()
    {
        $user = auth()->user();
        $today = $this->date ?: now()->toDateString();

        if ($user->isSuperAdminLike() && $this->tab === 'sinkron') {
            return view('livewire.absensi-table', [
                'sinkronView' => true,
                'tab' => $this->tab,
                'today' => $today,
            ]);
        }

        $ownView = $user->isStaff()
            || $user->isStaffIt()
            || $user->isStaffCreative()
            || ($user->isStaffHostPubg() && ! $user->isAnyKoordinator())
            || ($user->isStaffHostFf() && ! $user->isAnyKoordinator())
            || $user->isStaffHostMlbb()
            || $user->isStaffHostEfootball()
            || $user->isStaffHostValorant()
            || $user->isStaffAdmin()
            || ($user->isSuperAdminLike() && $this->tab === 'saya')
            || ($user->isKoordinator() && $this->tab === 'saya')
            || (($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg()) && $this->tab === 'saya')
            || ($user->isHeadOfStore() && $this->tab === 'saya');

        if ($ownView) {
            $employee = $user->employee;

            if (! $employee) {
                $riwayat = new LengthAwarePaginator([], 0, 10);

                return view('livewire.absensi-table', [
                    'karyawanView' => true,
                    'employee' => null,
                    'totalAbsensi' => 0,
                    'tepatWaktu' => 0,
                    'terlambat' => 0,
                    'totalHadir' => 0,
                    'jumlahHariKerja' => 0,
                    'attendances' => collect(),
                    'riwayat' => $riwayat,
                    'attendanceHariIni' => null,
                    'today' => $today,
                    'periodeLabel' => null,
                    'periodeOptions' => collect(),
                    'mingguLiburHariIni' => false,
                ]);
            }

            // Periode presensi mengikuti siklus gaji: tanggal 26 bulan
            // sebelumnya s.d. tanggal 25 bulan terpilih (mis. Januari = 26 Des
            // s.d. 25 Jan). Riwayat dan statistik hanya menampilkan data dalam
            // rentang tersebut.
            try {
                $periodeBulan = $this->periode !== ''
                    ? Carbon::createFromFormat('Y-m', $this->periode)->startOfMonth()
                    : now()->startOfMonth();
            } catch (\Throwable) {
                $periodeBulan = now()->startOfMonth();
            }

            $periodeMulai = $periodeBulan->copy()->subMonthNoOverflow()->day(26)->startOfDay();
            $periodeSelesai = $periodeBulan->copy()->day(25)->endOfDay();
            $periodeLabel = $periodeMulai->isoFormat('D MMM Y').' - '.$periodeSelesai->isoFormat('D MMM Y');

            $periodeOptions = collect(range(0, 11))
                ->map(fn ($i) => now()->subMonthsNoOverflow($i))
                ->map(fn ($m) => [
                    'value' => $m->format('Y-m'),
                    'label' => $m->locale('id')->isoFormat('MMMM Y'),
                ]);

            $semuaAbsensi = Attendance::with(['employee' => fn ($q) => $q->listSelect()])
                ->where('employee_id', $employee->id)
                ->where('date', '>=', $periodeMulai->toDateString())
                ->where('date', '<', $periodeSelesai->copy()->addDay()->startOfDay()->toDateString())
                ->get();

            $totalAbsensi = $semuaAbsensi->count();
            $mingguLiburHariIni = $employee->isWeeklyDayOff();
            $tepatWaktu = $semuaAbsensi->filter(fn ($a) => $a->status === 'hadir' && (! $a->time_in || $a->time_in <= ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00'))
            )->count();
            $terlambat = $semuaAbsensi->filter(fn ($a) => $a->status === 'hadir' && $a->time_in && $a->time_in > ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00')
            )->count();
            $totalHadir = $tepatWaktu + $terlambat;

            // Jumlah hari kerja = hari unik dengan record absensi (tanpa
            // libur mingguan) dalam periode berjalan.
            $jumlahHariKerja = $semuaAbsensi
                ->filter(fn ($a) => ($a->status ?? '') !== 'libur')
                ->map(fn ($a) => $a->date?->toDateString())
                ->filter()
                ->unique()
                ->count();

            // Riwayat memuat juga hari Minggu karyawan Office sebagai baris
            // "libur" mingguan (tanpa record absensi), sehingga Minggu tetap
            // tampil di daftar Senin-Sabtu.
            $riwayatRows = $semuaAbsensi->values();

            if (($employee->jenis_kerja ?? '') === Employee::JENIS_KERJA_OFFICE) {
                $tanggalAda = $riwayatRows
                    ->map(fn ($a) => $a->date?->toDateString())
                    ->filter()
                    ->all();

                for ($d = $periodeMulai->copy(); $d->lte($periodeSelesai); $d->addDay()) {
                    if ((int) $d->format('N') !== 7 || in_array($d->toDateString(), $tanggalAda, true)) {
                        continue;
                    }

                    $riwayatRows->push(tap(new Attendance([
                        'employee_id' => $employee->id,
                        'date' => $d->toDateString(),
                        'status' => 'libur',
                    ]), fn ($row) => $row->setRelation('employee', $employee)));
                }
            }

            $riwayatRows = $riwayatRows
                ->sortByDesc(fn ($a) => $a->date?->toDateString() ?? '')
                ->values();

            $page = LengthAwarePaginator::resolveCurrentPage();
            $riwayat = new LengthAwarePaginator(
                $riwayatRows->slice(($page - 1) * 10, 10)->values(),
                $riwayatRows->count(),
                10,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            $attendanceHariIni = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $employee->resolveWorkDate())->first();

            return view('livewire.absensi-table', compact(
                'employee', 'totalAbsensi', 'tepatWaktu', 'terlambat', 'totalHadir',
                'jumlahHariKerja', 'riwayat', 'attendanceHariIni', 'today', 'periodeLabel', 'periodeOptions',
                'mingguLiburHariIni'
            ))->with('karyawanView', true);
        }

        // Kolom karyawan dibatasi (tanpa foto base64) agar memori aman saat
        // daftar tim dimuat ulang setiap pagination.
        $attendances = Attendance::with(['employee' => fn ($q) => $q->listSelect()])
            ->whereDate('date', $today)
            ->orderByRaw("CASE WHEN status = 'hadir' THEN 0 ELSE 1 END, id DESC")
            ->get()
            ->keyBy('employee_id');

        $employeeQuery = Employee::query()->where('status', 'aktif')->listSelect();

        if ($user->isKoordinator() && $this->tab === 'tim') {
            $koordinatorEmployee = $user->employee;
            if ($koordinatorEmployee && $koordinatorEmployee->divisions->isNotEmpty()) {
                $divisionIds = $koordinatorEmployee->divisions->pluck('id')->toArray();
                $employeeQuery->whereHas('divisions', fn ($q) => $q->whereIn('divisions.id', $divisionIds))
                    ->where('id', '!=', $koordinatorEmployee->id);
            }
        }

        if (($user->isKoordinatorIt() || $user->isKoordinatorCreative() || $user->isKoordinatorAdmin() || $user->isKoordinatorStock() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorMlbb() || $user->isKoordinatorEfootball() || $user->isKoordinatorValorant() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg()) && $this->tab === 'tim') {
            $subordinateIds = $this->getSubordinateEmployeeIds();
            if (! empty($subordinateIds)) {
                $employeeQuery->whereIn('id', $subordinateIds);
            } else {
                $employeeQuery->whereRaw('1 = 0');
            }
        }

        if ($user->isHeadOfStore() && $this->tab === 'tim') {
            $subordinateIds = $this->getSubordinateEmployeeIds();
            if (! empty($subordinateIds)) {
                $employeeQuery->whereIn('id', $subordinateIds);
            } else {
                $employeeQuery->whereRaw('1 = 0');
            }
        }

        $totalKaryawan = (clone $employeeQuery)->count();

        $teamIds = (clone $employeeQuery)->pluck('id')->toArray();
        $teamAttendances = $attendances->filter(fn ($a) => in_array($a->employee_id, $teamIds));

        $hadir = $teamAttendances->filter(fn ($a) => $a->status === 'hadir' && (! $a->time_in || $a->time_in <= ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00'))
        )->count();

        $terlambat = $teamAttendances->filter(fn ($a) => $a->status === 'hadir' && $a->time_in && $a->time_in > ($a->employee?->jamMasukCutoff($a->date?->toDateString()) ?? '09:00:00')
        )->count();

        $totalHadir = $hadir + $terlambat;

        $statsMembers = $this->buildStatsMembers($teamAttendances);
        $this->statsMembers = $statsMembers;
        $this->statsDateLabel = \Carbon\Carbon::parse($today)->isoFormat('ddd, D MMM Y');

        $employees = $employeeQuery
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nik', 'like', "%{$this->search}%")
                        ->orWhere('nama', 'like', "%{$this->search}%");
                });
            })
            ->orderByRaw('CAST(nik AS UNSIGNED) ASC')
            ->paginate(10);

        return view('livewire.absensi-table', compact(
            'attendances', 'totalKaryawan', 'hadir', 'terlambat', 'totalHadir', 'employees', 'today', 'statsMembers'
        ))->with('karyawanView', false);
    }

    private function buildStatsMembers($teamAttendances): array
    {
        $tepat = [];
        $terlambat = [];

        foreach ($teamAttendances as $a) {
            if ($a->status !== 'hadir') {
                continue;
            }

            $cutoff = $a->employee?->jamMasukCutoff($this->date) ?? '09:00:00';
            $row = [
                'nama' => $a->employee?->nama ?? 'Tidak dikenal',
                'jabatan' => $a->employee?->position ?? '-',
                'time_in' => $a->time_in ? substr($a->time_in, 0, 5) : '-',
                'cutoff' => substr($cutoff, 0, 5),
            ];

            if ($a->time_in && $a->time_in > $cutoff) {
                $terlambat[] = $row;
            } else {
                $tepat[] = $row;
            }
        }

        $sortByTime = fn ($a, $b) => ($a['time_in'] === '-' ? '' : $a['time_in']) <=> ($b['time_in'] === '-' ? '' : $b['time_in']);
        usort($tepat, $sortByTime);
        usort($terlambat, $sortByTime);

        return ['tepat' => $tepat, 'terlambat' => $terlambat];
    }

    private function getSubordinateEmployeeIds(): array
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            return [];
        }

        $mainPosition = $employee->mainPosition();
        if (! $mainPosition) {
            return [];
        }

        $descendantIds = $this->getAllDescendantIds($mainPosition);
        if (empty($descendantIds)) {
            return [];
        }

        return Employee::whereHas('positions', function ($q) use ($descendantIds) {
            $q->whereIn('position_id', $descendantIds)
                ->where('is_main', true);
        })->pluck('id')->toArray();
    }

    private function getAllDescendantIds(Position $position): array
    {
        $ids = [];
        $children = Position::where('parent_id', $position->id)->get();
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllDescendantIds($child));
        }

        return $ids;
    }
}
