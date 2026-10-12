<?php

namespace App\Console\Commands;

use App\Models\StoredFile;
use App\Services\FileVault;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileVaultVerify extends Command
{
    protected $signature = 'files:verify {--repair : Pulihkan file yang hilang atau rusak dari salinan MySQL}';

    protected $description = 'Periksa keutuhan file di disk dibandingkan salinan di MySQL';

    public function handle(FileVault $vault): int
    {
        $missing = 0;
        $corrupt = 0;
        $ok = 0;
        $repaired = 0;
        $repair = (bool) $this->option('repair');

        StoredFile::query()->orderBy('id')->chunkById(200, function ($rows) use ($vault, $repair, &$missing, &$corrupt, &$ok, &$repaired) {
            foreach ($rows as $row) {
                $existsOnDisk = false;
                $currentHash = null;

                try {
                    $existsOnDisk = Storage::disk($row->disk)->exists($row->path);

                    if ($existsOnDisk) {
                        $currentHash = hash('sha256', Storage::disk($row->disk)->get($row->path));
                    }
                } catch (Throwable $e) {
                    $existsOnDisk = false;
                }

                if (! $existsOnDisk) {
                    $missing++;

                    if ($repair && $vault->writeToDisk($row->disk, $row->path, $row->content)) {
                        $repaired++;
                    }

                    continue;
                }

                if ($row->checksum && $currentHash !== $row->checksum) {
                    $corrupt++;

                    if ($repair && $vault->writeToDisk($row->disk, $row->path, $row->content)) {
                        $repaired++;
                    }

                    continue;
                }

                $ok++;
            }
        });

        $this->line("Utuh: $ok");
        $this->line("Hilang: $missing");
        $this->line("Rusak (checksum beda): $corrupt");

        if ($repair) {
            $this->info("Dipulihkan: $repaired");
        }

        return ($missing === 0 && $corrupt === 0) ? self::SUCCESS : self::FAILURE;
    }
}
