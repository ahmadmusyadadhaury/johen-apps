<?php

namespace App\Livewire;

use App\Models\AttendancePunch;
use App\Models\Employee;
use App\Models\EmployeeMachineUser;
use App\Models\MachineUser;
use App\Services\AttendanceSyncService;
use App\Services\ZkMachine\ZkClient;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MachineUserSyncTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public bool $showMapModal = false;

    public ?string $mapMachineUserId = null;

    public ?int $selectedEmployeeId = null;

    public string $mapSearch = '';

    public bool $showUnmapModal = false;

    public ?string $unmapMachineUserId = null;

    public string $unmapEmployeeName = '';

    public bool $showDeleteModal = false;

    public ?string $deleteMachineUserId = null;

    public string $deleteMachineName = '';

    public bool $showSuccessModal = false;

    public string $successTitle = '';

    public string $successMessage = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openMapModal(string $machineUserId): void
    {
        $this->mapMachineUserId = $machineUserId;
        $this->mapSearch = '';
        $this->selectedEmployeeId = null;
        $this->resetErrorBag();

        $mapped = Employee::findByMachineUserId($machineUserId);
        if ($mapped) {
            $this->selectedEmployeeId = $mapped->id;
        }

        $this->showMapModal = true;
    }

    public function closeMapModal(): void
    {
        $this->showMapModal = false;
        $this->mapMachineUserId = null;
        $this->selectedEmployeeId = null;
        $this->mapSearch = '';
        $this->resetErrorBag();
    }

    public function saveMapping(): void
    {
        if (! $this->mapMachineUserId || ! $this->selectedEmployeeId) {
            $this->addError('selectedEmployeeId', 'Pilih karyawan terlebih dahulu.');

            return;
        }

        $existing = Employee::findByMachineUserId($this->mapMachineUserId);
        if ($existing && $existing->id !== $this->selectedEmployeeId) {
            $this->addError('selectedEmployeeId', 'User ID mesin ini sudah terpetakan ke karyawan lain.');

            return;
        }

        $employee = Employee::find($this->selectedEmployeeId);
        if (! $employee) {
            $this->addError('selectedEmployeeId', 'Karyawan tidak ditemukan.');

            return;
        }

        if (! $employee->device_user_id) {
            $employee->device_user_id = $this->mapMachineUserId;
            $employee->save();
        }

        if ($employee->device_user_id !== $this->mapMachineUserId) {
            EmployeeMachineUser::updateOrCreate(
                ['machine_user_id' => $this->mapMachineUserId],
                ['employee_id' => $employee->id],
            );
        }

        $userId = $this->mapMachineUserId;
        $result = app(AttendanceSyncService::class)->backfillForUser($userId);

        $this->closeMapModal();
        $message = "User ID {$userId} dipetakan ke {$employee->nama}. ";
        $message .= $result['processed'] > 0
            ? "{$result['processed']} punch riwayat diproses."
            : 'Belum ada punch tersimpan. Riwayat otomatis terhubung setelah tap berikutnya.';
        $this->showSuccessModal = true;
        $this->successTitle = 'Mapping Berhasil';
        $this->successMessage = $message;
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->successTitle = '';
        $this->successMessage = '';
    }

    public function openUnmapModal(string $machineUserId): void
    {
        $employee = Employee::findByMachineUserId($machineUserId);
        if (! $employee) {
            $this->dispatch('notify', type: 'error', message: 'Mapping tidak ditemukan.');

            return;
        }

        $this->unmapMachineUserId = $machineUserId;
        $this->unmapEmployeeName = $employee->nama;
        $this->showUnmapModal = true;
    }

    public function closeUnmapModal(): void
    {
        $this->showUnmapModal = false;
        $this->unmapMachineUserId = null;
        $this->unmapEmployeeName = '';
    }

    public function confirmUnmap(): void
    {
        $machineUserId = $this->unmapMachineUserId;
        if (! $machineUserId) {
            return;
        }

        $employee = Employee::findByMachineUserId($machineUserId);
        if (! $employee) {
            $this->dispatch('notify', type: 'error', message: 'Mapping tidak ditemukan.');

            return;
        }

        if ($employee->device_user_id === $machineUserId) {
            $employee->device_user_id = null;
            $employee->save();
        }

        EmployeeMachineUser::where('machine_user_id', $machineUserId)->delete();

        $this->closeUnmapModal();
        $this->showSuccessModal = true;
        $this->successTitle = 'Mapping Dilepas';
        $this->successMessage = "Mapping User ID {$machineUserId} berhasil dilepas dari {$employee->nama}. Punch mesin tetap tersimpan di riwayat.";
    }

    public function openDeleteModal(string $machineUserId): void
    {
        abort_unless(auth()->user()->isSuperAdminLike(), 403);

        $this->deleteMachineUserId = $machineUserId;
        $this->deleteMachineName = MachineUser::where('machine_user_id', $machineUserId)->value('name') ?? $machineUserId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteMachineUserId = null;
        $this->deleteMachineName = '';
    }

    public function confirmDelete(): void
    {
        abort_unless(auth()->user()->isSuperAdminLike(), 403);

        $machineUserId = $this->deleteMachineUserId;
        if (! $machineUserId) {
            return;
        }

        DB::transaction(function () use ($machineUserId) {
            AttendancePunch::where('machine_user_id', $machineUserId)->delete();
            MachineUser::where('machine_user_id', $machineUserId)->delete();
        });

        $this->closeDeleteModal();
        $this->dispatch('notify', type: 'success', message: "User ID mesin {$machineUserId} beserta seluruh punch-nya berhasil dihapus.");
    }

    public function backfill(): void
    {
        $sync = app(AttendanceSyncService::class);
        $punches = AttendancePunch::whereNull('employee_id')
            ->orderBy('punch_at')
            ->get();

        $processed = 0;
        $unmatched = 0;

        foreach ($punches as $punch) {
            $employee = Employee::findByMachineUserId($punch->machine_user_id);
            if (! $employee) {
                $unmatched++;

                continue;
            }

            $sync->recordPunch(
                $punch->machine_user_id,
                $punch->punch_at->format('Y-m-d H:i:s'),
                $punch->method,
                $punch->machine_serial,
                $punch->raw_data,
            );
            $processed++;
        }

        $this->dispatch('notify', type: 'success', message: "Backfill selesai: {$processed} punch diproses, {$unmatched} belum terpetakan.");
    }

    public function syncMachineUsers(): void
    {
        $host = config('services.attendance_machine.host');
        $port = (int) config('services.attendance_machine.port');
        $commKey = (int) config('services.attendance_machine.comm_key');
        $timeout = (int) config('services.attendance_machine.timeout', 5);

        $client = new ZkClient($host, $port, $commKey, $timeout);
        if (! $client->connect()) {
            $this->dispatch('notify', type: 'error', message: "Tidak dapat terhubung ke mesin absen ({$host}:{$port}).");

            return;
        }

        try {
            $users = $client->getUsers();
        } finally {
            $client->disconnect();
        }

        if (empty($users)) {
            $this->dispatch('notify', type: 'error', message: 'Mesin merespons tetapi tidak ada data user.');

            return;
        }

        $saved = 0;
        foreach ($users as $userId => $user) {
            MachineUser::updateOrCreate(
                ['machine_user_id' => (string) $userId],
                [
                    'name' => $user['name'] ?: null,
                    'role' => $user['role'] ?? null,
                    'last_seen_at' => now(),
                ],
            );
            $saved++;
        }

        MachineUser::whereNotIn('machine_user_id', array_map('strval', array_keys($users)))->delete();

        $this->dispatch('notify', type: 'success', message: "Nama user mesin berhasil diambil: {$saved} user.");
    }

    public function render()
    {
        abort_unless(auth()->user()->isSuperAdminLike(), 403);

        $employeeMap = DB::table('employees')
            ->selectRaw('device_user_id as machine_user_id, id as employee_id')
            ->whereNotNull('device_user_id')
            ->union(
                DB::table('employee_machine_users')->select('machine_user_id', 'employee_id')
            );

        $query = DB::table('attendance_punches as p')
            ->leftJoinSub($employeeMap, 'em', function ($join) {
                $join->on('em.machine_user_id', '=', 'p.machine_user_id');
            })
            ->leftJoin('employees as e', 'e.id', '=', 'em.employee_id')
            ->leftJoin('machine_users as mu', 'mu.machine_user_id', '=', 'p.machine_user_id')
            ->selectRaw('p.machine_user_id, count(*) as total_taps, min(p.punch_at) as pertama, max(p.punch_at) as terakhir, max(e.nama) as employee_nama, max(e.nik) as employee_nik, max(e.id) as employee_id, max(mu.name) as machine_name')
            ->groupBy('p.machine_user_id');

        if ($this->search) {
            $query->where('p.machine_user_id', 'like', '%'.$this->search.'%');
        }

        if ($this->filterStatus === 'mapped') {
            $query->whereNotNull('e.id');
        } elseif ($this->filterStatus === 'unmapped') {
            $query->whereNull('e.id');
        }

        $machineUsers = $query->orderBy('p.machine_user_id')->paginate(20);

        $machineIdSub = DB::table('attendance_punches')->distinct()->select('machine_user_id');
        $totalIds = DB::table('attendance_punches')->distinct()->count('machine_user_id');
        $mappedIds = DB::table('attendance_punches as p')
            ->leftJoinSub($employeeMap, 'em', function ($join) {
                $join->on('em.machine_user_id', '=', 'p.machine_user_id');
            })
            ->whereNotNull('em.employee_id')
            ->distinct()
            ->count('p.machine_user_id');
        $totalPunches = AttendancePunch::count();
        $pendingPunches = AttendancePunch::whereNull('employee_id')->count();

        $mapEmployees = collect();
        if ($this->showMapModal) {
            $mapEmployees = Employee::query()
                ->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereNull('device_user_id')
                            ->orWhere('device_user_id', '!=', $this->mapMachineUserId);
                    })
                    ->whereDoesntHave('machineUserMappings', fn ($q3) => $q3->where('machine_user_id', $this->mapMachineUserId))
                    ->orWhere('id', $this->selectedEmployeeId);
                })
                ->when($this->mapSearch, function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('nama', 'like', '%'.$this->mapSearch.'%')
                            ->orWhere('nik', 'like', '%'.$this->mapSearch.'%');
                    });
                })
                ->orderBy('nama')
                ->get();
        }

        return view('livewire.machine-user-sync-table', compact(
            'machineUsers', 'totalIds', 'mappedIds', 'totalPunches', 'pendingPunches', 'mapEmployees'
        ));
    }
}
