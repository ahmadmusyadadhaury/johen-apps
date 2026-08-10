<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigitalPaymentApiService
{
    private ?Collection $paymentsCache = null;

    private ?Collection $assetsCache = null;

    public function payments(): Collection
    {
        if ($this->paymentsCache !== null) {
            return $this->paymentsCache;
        }

        $items = $this->fetch(config('services.digital_asset_api.payments_path'));

        return $this->paymentsCache = $items
            ->map(fn ($item) => $this->normalizePayment((array) $item))
            ->filter()
            ->values();
    }

    private function assets(): Collection
    {
        if ($this->assetsCache !== null) {
            return $this->assetsCache;
        }

        $items = $this->fetch(config('services.digital_asset_api.path'));

        return $this->assetsCache = $items
            ->mapWithKeys(function ($item) {
                $item = (array) $item;

                return [(string) ($item['id'] ?? '') => $item];
            })
            ->collect();
    }

    private function fetch(?string $path): Collection
    {
        $url = config('services.digital_asset_api.url');

        if (! $url || ! $path) {
            return collect();
        }

        try {
            $request = Http::timeout(5)
                ->withToken(config('services.digital_asset_api.token'))
                ->acceptJson();

            if (! config('services.digital_asset_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.$path);

            if (! $response->successful()) {
                Log::warning('DigitalPaymentApiService: permintaan gagal', [
                    'path' => $path,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return collect();
            }

            return $this->extractItems($response->json());
        } catch (\Throwable $e) {
            Log::warning('DigitalPaymentApiService: exception', ['message' => $e->getMessage()]);
            report($e);

            return collect();
        }
    }

    private function extractItems(?array $payload): Collection
    {
        if (! is_array($payload)) {
            return collect();
        }

        $items = $payload['data'] ?? $payload;

        if (is_array($items) && ! array_is_list($items)) {
            $items = $items['items'] ?? $items['payments'] ?? $items['assets'] ?? $items['data'] ?? [];
        }

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)->values();
    }

    private function normalizePayment(array $item): ?object
    {
        $asset = $this->assets()->get((string) ($item['digital_asset_id'] ?? ''), []);

        $status = $item['status'] ?? 'menunggu';
        $status = match ($status) {
            'pending', 'belum_bayar' => 'menunggu',
            'jatuh_tempo', 'expired', 'telat' => 'terlambat',
            'aktif' => 'lunas',
            default => $status,
        };

        $creatorName = $item['creator_name']
            ?? data_get($item, 'creator.name')
            ?? data_get($item, 'created_by.name')
            ?? null;

        if (is_array($creatorName)) {
            $creatorName = $creatorName['name'] ?? null;
        }

        $mulai = $asset['mulai'] ?? $item['tanggal_tagihan'] ?? $item['mulai'] ?? null;
        $berakhir = $asset['berakhir'] ?? $item['berakhir'] ?? null;

        return (object) [
            'id' => $item['id'] ?? null,
            'digital_asset_id' => $item['digital_asset_id'] ?? null,
            'nama_aset' => $item['nama_aset'] ?? $asset['nama_aset'] ?? null,
            'email' => $item['email'] ?? $asset['email'] ?? null,
            'mulai' => $mulai ? $this->parseDate($mulai) : null,
            'berakhir' => $berakhir ? $this->parseDate($berakhir) : null,
            'tagihan' => $item['periode'] ?? $item['tagihan'] ?? $asset['nama_aset'] ?? '-',
            'jatuh_tempo' => isset($item['jatuh_tempo']) ? $this->parseDate($item['jatuh_tempo']) : null,
            'hari' => $item['hari_digital'] ?? $item['hari'] ?? '-',
            'nominal' => (float) ($item['nominal'] ?? $item['biaya'] ?? 0),
            'pic' => $item['pic'] ?? $asset['pic'] ?? null,
            'jabatan' => $item['jabatan'] ?? $asset['jabatan'] ?? null,
            'keterangan' => $item['notes'] ?? $item['keterangan'] ?? null,
            'status' => $status,
            'tgl_bayar' => isset($item['tanggal_bayar']) ? $this->parseDate($item['tanggal_bayar']) : null,
            'bukti' => $item['bukti_bayar'] ?? $item['bukti_url'] ?? null,
            'creator' => (object) ['name' => $creatorName],
        ];
    }

    private function parseDate(mixed $value): ?Carbon
    {
        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}