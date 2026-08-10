<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InternetApiService
{
    private ?Collection $paymentsCache = null;

    private ?Collection $checksCache = null;

    public function payments(): Collection
    {
        if ($this->paymentsCache !== null) {
            return $this->paymentsCache;
        }

        $items = $this->fetch(config('services.internet_api.payments_path'));

        return $this->paymentsCache = $items
            ->map(fn ($item) => $this->normalizePayment((array) $item))
            ->filter()
            ->values();
    }

    public function checks(): Collection
    {
        if ($this->checksCache !== null) {
            return $this->checksCache;
        }

        $items = $this->fetch(config('services.internet_api.checks_path'));

        return $this->checksCache = $items
            ->map(fn ($item) => $this->normalizeCheck((array) $item))
            ->filter()
            ->values();
    }

    private function fetch(?string $path): Collection
    {
        $url = config('services.internet_api.url');

        if (! $url || ! $path) {
            return collect();
        }

        try {
            $request = Http::timeout(5)
                ->withToken(config('services.internet_api.token'))
                ->acceptJson();

            if (! config('services.internet_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.$path);

            if (! $response->successful()) {
                Log::warning('InternetApiService: permintaan gagal', [
                    'path' => $path,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return collect();
            }

            return $this->extractItems($response->json());
        } catch (\Throwable $e) {
            Log::warning('InternetApiService: exception', ['message' => $e->getMessage()]);
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
            $items = $items['items'] ?? $items['payments'] ?? $items['checks'] ?? $items['data'] ?? [];
        }

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)->values();
    }

    private function normalizePayment(array $item): ?object
    {
        $masaTenggang = $item['masa_tenggang']
            ?? $item['due_date']
            ?? $item['tanggal_jatuh_tempo']
            ?? null;

        $tanggalBayar = $item['tanggal_bayar']
            ?? $item['tgl_bayar']
            ?? $item['payment_date']
            ?? $item['paid_at']
            ?? null;

        $status = $item['status'] ?? 'menunggu';
        $status = match ($status) {
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

        return (object) [
            'id' => $item['id'] ?? null,
            'nama_internet' => $item['nama_internet'] ?? $item['name'] ?? $item['internet_name'] ?? null,
            'provider' => $item['provider'] ?? null,
            'pic' => $item['pic'] ?? null,
            'jabatan' => $item['jabatan'] ?? null,
            'masa_tenggang' => $masaTenggang ? $this->parseDate($masaTenggang) : null,
            'hari' => $item['hari_internet'] ?? $item['hari'] ?? '-',
            'biaya' => (float) ($item['biaya'] ?? $item['amount'] ?? 0),
            'status' => $status,
            'tgl_bayar' => $tanggalBayar ? $this->parseDate($tanggalBayar) : null,
            'keterangan' => $item['notes'] ?? $item['keterangan'] ?? $item['catatan'] ?? null,
            'creator' => (object) ['name' => $creatorName],
        ];
    }

    private function normalizeCheck(array $item): ?object
    {
        $checkerName = $item['checker_name']
            ?? data_get($item, 'checker.name')
            ?? data_get($item, 'checked_by.name')
            ?? null;

        return (object) [
            'id' => $item['id'] ?? null,
            'ruangan' => $item['ruangan'] ?? $item['room'] ?? null,
            'hari' => $item['hari'] ?? $item['day'] ?? '-',
            'tanggal' => isset($item['tanggal']) ? $this->parseDate($item['tanggal']) : null,
            'penggunaan_wifi' => (float) ($item['penggunaan_wifi'] ?? $item['wifi_usage'] ?? 0),
            'penggunaan_ethernet' => (float) ($item['penggunaan_ethernet'] ?? $item['ethernet_usage'] ?? 0),
            'keterangan' => $item['keterangan'] ?? $item['notes'] ?? $item['catatan'] ?? null,
            'checker' => (object) ['name' => $checkerName],
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