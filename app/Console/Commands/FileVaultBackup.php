<?php

namespace App\Console\Commands;

use App\Models\StoredFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FileVaultBackup extends Command
{
    protected $signature = 'files:backup {--path= : Lokasi file backup (default storage/app/backups)}';

    protected $description = 'Backup tabel stored_files (salinan semua dokumen) ke file .sql';

    public function handle(): int
    {
        $directory = $this->option('path') ?: storage_path('app/backups');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file = $directory.DIRECTORY_SEPARATOR.'stored_files_'.now()->format('Ymd_His').'.sql';

        $handle = fopen($file, 'w');

        if ($handle === false) {
            $this->error("Tidak dapat membuat file: $file");

            return self::FAILURE;
        }

        fwrite($handle, '-- Backup stored_files ('.now()->toDateTimeString().")\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "CREATE TABLE IF NOT EXISTS stored_files_writer (\n");
        fwrite($handle, "  id BIGINT UNSIGNED NOT NULL,\n");
        fwrite($handle, "  disk VARCHAR(30) NOT NULL,\n");
        fwrite($handle, "  path VARCHAR(500) NOT NULL,\n");
        fwrite($handle, "  original_name VARCHAR(255) NULL,\n");
        fwrite($handle, "  mime_type VARCHAR(150) NULL,\n");
        fwrite($handle, "  size BIGINT UNSIGNED NOT NULL DEFAULT 0,\n");
        fwrite($handle, "  checksum VARCHAR(64) NULL,\n");
        fwrite($handle, "  content LONGBLOB NOT NULL,\n");
        fwrite($handle, "  created_at TIMESTAMP NULL,\n");
        fwrite($handle, "  updated_at TIMESTAMP NULL,\n");
        fwrite($handle, "  PRIMARY KEY (id), UNIQUE KEY stored_files_disk_path_unique (disk, path)\n");
        fwrite($handle, ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n");

        DB::table('stored_files')->orderBy('id')->chunkById(100, function ($rows) use ($handle) {
            foreach ($rows as $row) {
                $isNull = fn ($v) => $v === null ? 'NULL' : "'".str_replace("'", "''", (string) $v)."'";
                $content = $row->content === null ? "''" : "UNHEX('".bin2hex($row->content)."')";
                $created = $row->created_at ? "'".$row->created_at."'" : 'NULL';
                $updated = $row->updated_at ? "'".$row->updated_at."'" : 'NULL';

                fwrite($handle, "INSERT INTO stored_files_writer (id, disk, path, original_name, mime_type, size, checksum, content, created_at, updated_at) VALUES (\n");
                fwrite($handle, "  {$row->id}, {$isNull($row->disk)}, {$isNull($row->path)}, {$isNull($row->original_name)}, {$isNull($row->mime_type)}, ".((int) $row->size).", {$isNull($row->checksum)}, {$content}, {$created}, {$updated}\n");
                fwrite($handle, ");\n");
            }
        });

        fclose($handle);

        $count = StoredFile::count();

        $this->info("Backup selesai: $file");
        $this->line("Jumlah baris: $count");

        return self::SUCCESS;
    }
}
