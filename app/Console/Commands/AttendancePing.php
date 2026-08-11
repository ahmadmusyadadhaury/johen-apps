<?php

namespace App\Console\Commands;

use App\Services\ZkMachine\ZkClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:ping')]
#[Description('Tes koneksi ke mesin absensi (connect, auth, versi firmware, jam mesin)')]
class AttendancePing extends Command
{
    public function handle(): int
    {
        $host = config('services.attendance_machine.host');
        $port = (int) config('services.attendance_machine.port');
        $commKey = (int) config('services.attendance_machine.comm_key');
        $timeout = (int) config('services.attendance_machine.timeout', 5);

        $client = new ZkClient($host, $port, $commKey, $timeout);

        $this->info("Menghubungkan ke {$host}:{$port} ...");

        if (!$client->connect()) {
            $this->error('Koneksi GAGAL: mesin tidak merespons atau comm key salah.');

            return self::FAILURE;
        }

        $this->info('Koneksi OK (auth berhasil).');

        $version = $client->getVersion();
        $time = $client->getTime();

        $this->line('  Firmware : ' . ($version ?? 'tidak terbaca'));
        $this->line('  Jam mesin: ' . ($time ?? 'tidak terbaca'));

        $client->disconnect();
        $this->info('Disconnect OK.');

        return self::SUCCESS;
    }
}
