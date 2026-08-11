<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\ZkMachine\ZkClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:sync-users {--only-unmapped : Tampilkan hanya user mesin yang belum dipetakan}')]
#[Description('Tarik daftar user dari mesin absensi dan bandingkan dengan mapping device_user_id karyawan')]
class AttendanceSyncUsers extends Command
{
    public function handle(): int
    {
        $host = config('services.attendance_machine.host');
        $port = (int) config('services.attendance_machine.port');
        $commKey = (int) config('services.attendance_machine.comm_key');
        $timeout = (int) config('services.attendance_machine.timeout', 5);

        $this->info("Menghubungkan ke {$host}:{$port} ...");

        $client = new ZkClient($host, $port, $commKey, $timeout);
        if (!$client->connect()) {
            $this->error('Koneksi GAGAL.');

            return self::FAILURE;
        }

        $this->info('Mendownload daftar user mesin...');
        $users = $client->getUsers();
        $client->disconnect();

        $this->info('User mesin: ' . count($users));

        if (empty($users)) {
            $this->warn('Tidak ada user di mesin (atau format data tidak sesuai).');

            return self::SUCCESS;
        }

        $employees = Employee::query()->get()->keyBy('device_user_id');
        $nameIndex = [];
        foreach ($employees as $employee) {
            $nameIndex[strtolower(trim($employee->nama))] = $employee;
        }

        $onlyUnmapped = (bool) $this->option('only-unmapped');
        $rows = [];
        $mapped = 0;

        foreach ($users as $userId => $user) {
            $employee = $employees->get($userId);

            if (!$employee) {
                $suggestion = $nameIndex[strtolower(trim($user['name']))] ?? null;
            }

            $status = $employee
                ? "TERMAPPING -> {$employee->nama} ({$employee->nik})"
                : (($suggestion ?? null)
                    ? "BELUM -> saran: {$suggestion->nama} ({$suggestion->nik})"
                    : 'BELUM');

            if ($employee) {
                $mapped++;
            }

            if ($onlyUnmapped && $employee) {
                continue;
            }

            $rows[] = [$userId, $user['name'], $user['role'], $status];
        }

        $this->table(['User ID Mesin', 'Nama di Mesin', 'Role', 'Mapping'], $rows);
        $this->line("Total: {$mapped} termapping, " . (count($users) - $mapped) . ' belum termapping.');

        if ($mapped < count($users)) {
            $this->warn('Isi kolom `device_user_id` pada karyawan (menu karyawan di aplikasi) sesuai User ID Mesin di atas.');
        }

        return self::SUCCESS;
    }
}
