<?php

namespace App\Services;

use App\Models\StoredFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileVault
{
    public function defaultDisk(): string
    {
        return (string) config('filesystems.default', 'local');
    }

    /**
     * Padanan UploadedFile::store() — simpan ke disk lalu salin ke MySQL.
     */
    public function store(UploadedFile $file, string $dir = '', ?string $disk = null): string
    {
        $disk = $disk ?: $this->defaultDisk();

        $path = $file->store($dir, $disk);

        $this->mirror($disk, $path, $file->getClientOriginalName(), $file->getClientMimeType());

        return $path;
    }

    /**
     * Padanan UploadedFile::storeAs() — simpan ke disk lalu salin ke MySQL.
     */
    public function storeAs(UploadedFile $file, string $dir, string $name, ?string $disk = null): string
    {
        $disk = $disk ?: $this->defaultDisk();

        $path = $file->storeAs($dir, $name, $disk);

        $this->mirror($disk, $path, $file->getClientOriginalName(), $file->getClientMimeType());

        return $path;
    }

    /**
     * Tulis konten (mis. hasil generate PDF) ke disk dan MySQL sekaligus.
     */
    public function put(string $disk, string $path, string $content, ?string $originalName = null, ?string $mimeType = null): string
    {
        Storage::disk($disk)->put($path, $content);

        $this->mirrorContent($disk, $path, $content, $originalName, $mimeType);

        return $path;
    }

    /**
     * Salin file yang sudah ada di disk ke MySQL.
     */
    public function mirror(string $disk, string $path, ?string $originalName = null, ?string $mimeType = null): void
    {
        try {
            if ($path === false || $path === null || $path === '') {
                return;
            }

            if (! Storage::disk($disk)->exists($path)) {
                return;
            }

            $size = (int) Storage::disk($disk)->size($path);

            if ($size > (int) config('filevault.max_bytes')) {
                return;
            }

            $content = Storage::disk($disk)->get($path);

            $this->mirrorContent($disk, $path, $content, $originalName, $mimeType, $size);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function mirrorContent(string $disk, string $path, string $content, ?string $originalName = null, ?string $mimeType = null, ?int $size = null): void
    {
        try {
            $mimeType = $mimeType ?: $this->detectMime($content ?? '');

            StoredFile::updateOrCreate(
                ['disk' => $disk, 'path' => $path],
                [
                    'original_name' => $originalName ?: basename($path),
                    'mime_type' => $mimeType,
                    'size' => $size ?? strlen($content),
                    'checksum' => hash('sha256', (string) $content),
                    'content' => $content,
                ]
            );
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Baca file dari disk; bila hilang, ambil dari MySQL dan pulihkan ke disk.
     */
    public function get(string $disk, string $path): ?string
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->get($path);
            }
        } catch (Throwable $e) {
            // lanjut ke fallback database
        }

        $row = StoredFile::where('disk', $disk)->where('path', $path)->first();

        if (! $row || $row->content === null) {
            return null;
        }

        $this->writeToDisk($disk, $path, $row->content);

        return $row->content;
    }

    public function exists(string $disk, string $path): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                return true;
            }
        } catch (Throwable $e) {
            // abaikan, cek database
        }

        return StoredFile::where('disk', $disk)->where('path', $path)->exists();
    }

    /**
     * Hapus file di disk dan barisnya di MySQL agar tidak dipulihkan kembali.
     */
    public function delete(string $disk, string $path): void
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        } catch (Throwable $e) {
            report($e);
        }

        StoredFile::where('disk', $disk)->where('path', $path)->delete();
    }

    /**
     * Pulihkan file yang hilang dari MySQL ke disk.
     */
    public function restore(?string $disk = null, ?string $path = null): int
    {
        $query = StoredFile::query();

        if ($disk) {
            $query->where('disk', $disk);
        }

        if ($path) {
            $query->where('path', $path);
        }

        $restored = 0;

        $query->orderBy('id')->chunkById(200, function ($rows) use (&$restored) {
            foreach ($rows as $row) {
                try {
                    if (Storage::disk($row->disk)->exists($row->path)) {
                        continue;
                    }
                } catch (Throwable $e) {
                    // coba tulis
                }

                if ($this->writeToDisk($row->disk, $row->path, $row->content)) {
                    $restored++;
                }
            }
        });

        return $restored;
    }

    /**
     * Pindai file yang sudah ada di disk dan salin ke MySQL (backfill).
     *
     * @return array{scanned:int,stored:int,restored:int}
     */
    public function syncDisk(string $disk, string $prefix = ''): array
    {
        $diskObj = Storage::disk($disk);
        $exclude = (array) config('filevault.exclude', []);

        $scanned = 0;
        $stored = 0;
        $restored = 0;

        foreach ($diskObj->allFiles($prefix) as $file) {
            if ($this->isExcluded($file, $exclude)) {
                continue;
            }

            $scanned++;

            $size = (int) $diskObj->size($file);

            if ($size > (int) config('filevault.max_bytes')) {
                continue;
            }

            if (StoredFile::where('disk', $disk)->where('path', $file)->exists()) {
                $restored++;
            }

            $this->mirror($disk, $file);
            $stored++;
        }

        return ['scanned' => $scanned, 'stored' => $stored, 'restored' => $restored];
    }

    public function writeToDisk(string $disk, string $path, ?string $content): bool
    {
        if ($content === null) {
            return false;
        }

        try {
            Storage::disk($disk)->put($path, $content);

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    protected function isExcluded(string $path, array $exclude): bool
    {
        $normalized = str_replace('\\', '/', $path);

        foreach ($exclude as $dir) {
            $dir = trim(str_replace('\\', '/', $dir), '/');

            if ($dir !== '' && str_starts_with($normalized, $dir.'/')) {
                return true;
            }
        }

        return false;
    }

    protected function detectMime(string $content): ?string
    {
        if ($content === '') {
            return null;
        }

        try {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($content);

            return $mime ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }
}
