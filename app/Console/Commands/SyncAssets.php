<?php

namespace App\Console\Commands;

use App\Services\AsetMesSyncService;
use App\Services\AsetRukoSyncService;
use App\Services\AsetTimSyncService;
use App\Services\DigitalAssetSyncService;
use App\Services\PeralatanKantorSyncService;
use App\Services\SimCardSyncService;
use App\Services\SosialMediaSyncService;
use App\Services\VehicleSyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncAssets extends Command
{
    protected $signature = 'assets:sync {--category=* : Batasi ke kategori tertentu (contoh: sosial-media,peralatan-kantor)}';

    protected $description = 'Sinkronisasi semua data aset dari office.johengaming.store';

    public function handle(): int
    {
        $services = [
            'peralatan-kantor' => [PeralatanKantorSyncService::class, 'Peralatan Kantor'],
            'kendaraan' => [VehicleSyncService::class, 'Kendaraan'],
            'sim-card' => [SimCardSyncService::class, 'SIM Card'],
            'aset-ruko' => [AsetRukoSyncService::class, 'Aset Ruko'],
            'sosial-media' => [SosialMediaSyncService::class, 'Sosial Media'],
            'aset-mes' => [AsetMesSyncService::class, 'Aset MES'],
            'aset-tim' => [AsetTimSyncService::class, 'Aset TIM'],
            'digital-asset' => [DigitalAssetSyncService::class, 'Digital Asset'],
        ];

        $selected = array_values(array_filter((array) $this->option('category')));

        if ($selected !== []) {
            $services = array_intersect_key($services, array_flip($selected));

            if ($services === []) {
                $this->error('Kategori tidak dikenal: '.implode(', ', $selected));

                return self::FAILURE;
            }
        }

        $rows = [];

        foreach ($services as $slug => [$serviceClass, $label]) {
            try {
                $result = app($serviceClass)->sync();

                $rows[] = [
                    $label,
                    $result['source'] ?? '-',
                    $result['created'] ?? 0,
                    $result['updated'] ?? 0,
                    $result['skipped'] ?? 0,
                    $result['deleted'] ?? 0,
                ];
            } catch (Throwable $e) {
                report($e);

                $rows[] = [$label, 'error', 0, 0, 0, 0];
                $this->warn("{$label}: ".$e->getMessage());
            }
        }

        $this->table(['Kategori', 'Sumber', 'Baru', 'Update', 'Lewati', 'Hapus'], $rows);

        return self::SUCCESS;
    }
}
