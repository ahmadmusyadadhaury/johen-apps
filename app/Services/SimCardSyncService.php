<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimCardSyncService
{
    private const CACHE_KEY = 'sim_card_api_payload';

    private const CACHE_TTL_SECONDS = 300;

    public function sync(): array
    {
        $items = $this->fetch();

        if ($items->isEmpty()) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'source' => 'cache'];
        }

        $category = $this->ensureCategory();

        if (! $category) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'source' => 'no_category'];
        }

        $createdBy = auth()->id()
            ?? User::where('role', User::ROLE_SUPER_ADMIN)->value('id')
            ?? User::orderBy('id')->value('id');

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $data = $this->normalize((array) $item);

            if (empty($data['code'])) {
                $skipped++;

                continue;
            }

            $asset = Asset::where('code', $data['code'])->first();

            if ($asset) {
                $asset->update($data);
                $updated++;
            } else {
                Asset::create(array_merge($data, [
                    'category_id' => $category->id,
                    'created_by' => $createdBy,
                ]));
                $created++;
            }
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'source' => 'api'];
    }

    public function fetch(): Collection
    {
        $url = config('services.sim_card_api.url');

        if (! $url) {
            return collect();
        }

        try {
            $request = Http::timeout(15)
                ->withToken(config('services.sim_card_api.token'))
                ->acceptJson();

            if (! config('services.sim_card_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.config('services.sim_card_api.path'));

            if (! $response->successful()) {
                Log::warning('SimCardSyncService: API gagal', [
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
            Log::warning('SimCardSyncService: exception', ['message' => $e->getMessage()]);
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
        $nomor = (string) ($item['nomor_sim_card'] ?? '');
        $statusKartu = filter_var($item['status_kartu'] ?? true, FILTER_VALIDATE_BOOLEAN);

        return [
            'code' => $nomor,
            'name' => $nomor ?: ($this->nullable($item['pic'] ?? null) ?? 'SIM Card'),
            'brand' => null,
            'model' => null,
            'serial_number' => $nomor ?: null,
            'purchase_price' => null,
            'purchase_date' => $this->nullableDate($item['masa_aktif'] ?? null),
            'supplier' => null,
            'location' => null,
            'condition' => 'baik',
            'status' => $statusKartu ? 'tersedia' : 'dihapuskan',
            'description' => $this->buildDescription($item),
        ];
    }

    private function buildDescription(array $item): ?string
    {
        $parts = [];

        if ($pic = $this->nullable($item['pic'] ?? null)) {
            $parts[] = 'PIC: '.$pic;
        }

        if ($jabatan = $this->nullable($item['jabatan'] ?? null)) {
            $parts[] = 'Jabatan: '.$jabatan;
        }

        if ($atasan = $this->nullable($item['atasan'] ?? null)) {
            $parts[] = 'Atasan: '.$atasan;
        }

        if ($masaAktif = $this->nullableDate($item['masa_aktif'] ?? null)) {
            $parts[] = 'Masa Aktif: '.$masaAktif;
        }

        if ($masaTenggang = $this->nullableDate($item['masa_tenggang'] ?? null)) {
            $parts[] = 'Masa Tenggang: '.$masaTenggang;
        }

        if ($keperluan = $this->nullable($item['keperluan'] ?? null)) {
            $parts[] = 'Keperluan: '.$keperluan;
        }

        return $parts ? implode(' | ', $parts) : null;
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

    private function ensureCategory(): ?AssetCategory
    {
        return AssetCategory::firstOrCreate(
            ['name' => 'SIM Card'],
            ['description' => 'Data SIM card tersinkron dari office.johengaming.store', 'is_active' => true]
        );
    }
}
