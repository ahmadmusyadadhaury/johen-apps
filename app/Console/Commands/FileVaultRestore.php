<?php

namespace App\Console\Commands;

use App\Services\FileVault;
use Illuminate\Console\Command;

class FileVaultRestore extends Command
{
    protected $signature = 'files:restore
                            {--disk= : Batasi ke disk tertentu (public, local, web)}
                            {--path= : Batasi ke satu path tertentu}';

    protected $description = 'Pulihkan file yang hilang di disk dari salinan di MySQL (stored_files)';

    public function handle(FileVault $vault): int
    {
        $disk = $this->option('disk');
        $path = $this->option('path');

        $restored = $vault->restore($disk, $path);

        $scope = $disk ? " pada disk [$disk]" : '';

        if ($restored === 0) {
            $this->info("Tidak ada file yang perlu dipulihkan$scope. Semua file tersedia.");

            return self::SUCCESS;
        }

        $this->info("Berhasil memulihkan $restored file dari MySQL ke disk$scope.");

        return self::SUCCESS;
    }
}
