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

        $this->info("Memeriksa TCP {$host}:{$port} ...");

        $probe = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (! is_resource($probe)) {
            $this->error("TCP {$host}:{$port} TIDAK terjangkau (errno {$errno}: {$errstr}).");
            $this->line('Kemungkinan: mode komunikasi mesin bukan TCP/IP, IP/subnet/gateway mesin belum aktif,');
            $this->line('kabel LAN mesin tidak tersambung, mesin tersambung lewat WiFi tanpa listener SDK,');
            $this->line('atau port '.$port.' diblokir firewall. Tambat: cek MAC dan mode koneksi di menu mesin.');
            $this->line('Minta mesin menyala di menu Info: jika MACnya BERBEDA dari yang ada di ARP PC, berarti IP conflict.');

            return self::FAILURE;
        }
        fclose($probe);

        $this->info("TCP {$host}:{$port} TERBUKA (mesin mendengarkan). Melanjutkan autentikasi SDK...");

        $client = new ZkClient($host, $port, $commKey, $timeout);

        if (!$client->connect()) {
            $this->error('Autentikasi SDK GAGAL: TCP terbuka tapi handshake ZK ditolak.');
            $this->line('Kemungkinan: comm key di mesin ≠ '.$commKey.', atau aplikasi SDK lain');

            if (config('services.attendance_machine.comm_key') != 0) {
                $this->line('sudah terkoneksi (mis. daemon `attendance:listen` atau software vendor).');
            } else {
                $this->line('sudah terkoneksi memakai comm key ini (mis. daemon `attendance:listen` atau software vendor).');
            }

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
