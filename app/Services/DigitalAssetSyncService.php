<?php

namespace App\Services;

use App\Models\DigitalAssetRegistry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigitalAssetSyncService
{
    private const CACHE_KEY = 'digital_asset_api_payload';

    private const CACHE_TTL_SECONDS = 300;

    public function sync(): array
    {
        $items = $this->fetch();

        if ($items->isEmpty()) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'source' => 'cache'];
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $data = $this->normalize((array) $item);

            if (empty($data['source_id'])) {
                $skipped++;

                continue;
            }

            $asset = DigitalAssetRegistry::where('source_id', $data['source_id'])->first();

            if ($asset) {
                $asset->update($data);
                $updated++;
            } else {
                DigitalAssetRegistry::create($data);
                $created++;
            }
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'source' => 'api'];
    }

    public function fetch(): Collection
    {
        $url = config('services.digital_asset_api.url');

        if (! $url) {
            return collect();
        }

        try {
            $request = Http::timeout(15)
                ->withToken(config('services.digital_asset_api.token'))
                ->acceptJson();

            if (! config('services.digital_asset_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.config('services.digital_asset_api.path'));

            if (! $response->successful()) {
                Log::warning('DigitalAssetSyncService: API gagal', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return Cache::get(self::CACHE_KEY, collect());
            }

            $items = $this->extractItems($response->json());

            if ($items->isNotEmpty()) {
                Cache::put(self::CACHE_KEY, $items, self::CACHE_TTL_SECONDS);
            }

            return $items;
        } catch (\Throwable $e) {
            Log::warning('DigitalAssetSyncService: exception', ['message' => $e->getMessage()]);
            report($e);

            return Cache::get(self::CACHE_KEY, collect());
        }
    }

    private function extractItems(?array $payload): Collection
    {
        if (! is_array($payload)) {
            return collect();
        }

        $items = $payload['data'] ?? null;

        return is_array($items) ? collect($items)->values() : collect();
    }

    private function normalize(array $item): array
    {
        $berakhir = $this->nullableDate($item['berakhir'] ?? null);

        return [
            'source_id' => $item['id'] ?? null,
            'nama_aset' => (string) ($item['nama_aset'] ?? ''),
            'email' => $this->nullable($item['email'] ?? null),
            'mulai' => $this->nullableDate($item['mulai'] ?? null),
            'berakhir' => $berakhir,
            'biaya' => $this->nullable($item['biaya'] ?? null) ?? 0,
            'pic' => $this->nullable($item['pic'] ?? null),
            'jabatan' => $this->nullable($item['jabatan'] ?? null),
            'keperluan' => $this->nullable($item['keperluan'] ?? null),
            'is_active' => $berakhir !== null ? $berakhir >= now()->toDateString() : false,
        ];
    }

    private function nullable(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function nullableDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
