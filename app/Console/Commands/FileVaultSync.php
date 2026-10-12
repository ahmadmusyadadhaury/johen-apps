<?php

namespace App\Console\Commands;

use App\Services\FileVault;
use Illuminate\Console\Command;
use Throwable;

class FileVaultSync extends Command
{
    protected $signature = 'files:sync {--disk= : Batasi ke disk tertentu (public, local, web)}';

    protected $description = 'Salin semua file yang sudah ada di disk ke tabel stored_files (MySQL)';

    public function handle(FileVault $vault): int
    {
        $targets = (array) config('filevault.sync', []);
        $only = $this->option('disk');

        if ($only) {
            if (! array_key_exists($only, $targets)) {
                $this->error("Disk [$only] tidak terdaftar di config filevault.sync.");

                return self::FAILURE;
            }

            $targets = [$only => $targets[$only]];
        }

        $totalScanned = 0;
        $totalStored = 0;

        foreach ($targets as $disk => $prefix) {
            $this->info("Memindai disk [$disk]".($prefix ? " (prefix: $prefix)" : '').' ...');

            try {
                $result = $vault->syncDisk($disk, (string) $prefix);
            } catch (Throwable $e) {
                $this->error("Gagal memindai [$disk]: {$e->getMessage()}");

                continue;
            }

            $totalScanned += $result['scanned'];
            $totalStored += $result['stored'];

            $this->line("  {$result['scanned']} file dipindai, {$result['stored']} disalin ke MySQL.");
        }

        $this->newLine();
        $this->info("Selesai. Total {$totalScanned} file dipindai, {$totalStored} salinan tersimpan di MySQL.");

        return self::SUCCESS;
    }
}
