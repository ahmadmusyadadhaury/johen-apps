<?php

namespace App\Console\Commands;

use App\Facades\FileVault;
use App\Models\PayrollDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SecurePayrollSlips extends Command
{
    protected $signature = 'payroll:secure-slips';

    protected $description = 'Pindahkan slip payroll dari disk publik ke penyimpanan privat';

    public function handle(): int
    {
        $moved = 0;
        $failed = 0;

        PayrollDetail::query()->whereNotNull('pdf_path')->chunkById(100, function ($details) use (&$moved, &$failed): void {
            foreach ($details as $detail) {
                $path = $detail->pdf_path;

                if (Storage::disk('local')->exists($path)) {
                    FileVault::delete('public', $path);
                    continue;
                }

                $content = FileVault::get('public', $path);
                if ($content === null) {
                    $this->warn("Slip #{$detail->id} tidak ditemukan pada disk publik maupun arsip.");
                    $failed++;
                    continue;
                }

                FileVault::put('local', $path, $content, basename($path), 'application/pdf');
                if (! FileVault::exists('local', $path)) {
                    $this->error("Slip #{$detail->id} gagal disalin ke penyimpanan privat; salinan publik dipertahankan.");
                    $failed++;
                    continue;
                }

                FileVault::delete('public', $path);
                $moved++;
            }
        });

        foreach (Storage::disk('public')->allFiles('payroll') as $path) {
            if (Storage::disk('local')->exists($path)) {
                FileVault::delete('public', $path);
                continue;
            }

            $content = FileVault::get('public', $path);
            if ($content === null) {
                $this->warn("File {$path} tidak dapat dibaca; salinan publik dipertahankan.");
                $failed++;
                continue;
            }

            FileVault::put('local', $path, $content, basename($path), 'application/pdf');
            if (! FileVault::exists('local', $path)) {
                $this->error("File {$path} gagal disalin ke penyimpanan privat; salinan publik dipertahankan.");
                $failed++;
                continue;
            }

            FileVault::delete('public', $path);
            $moved++;
        }

        $this->info("Pemindahan selesai: {$moved} slip diamankan; {$failed} slip perlu diperiksa.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
