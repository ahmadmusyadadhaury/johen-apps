<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IplRukoApiService
{
    private ?Collection $paymentsCache = null;

    public function payments(): Collection
    {
        if ($this->paymentsCache !== null) {
            return $this->paymentsCache;
        }

        $items = $this->fetch(config('services.ipl_ruko_api.payments_path'));

        return $this->paymentsCache = $items
            ->map(fn ($item) => $this->normalizePayment((array) $item))
            ->filter()
            ->values();
    }

    private function fetch(?string $path): Collection
    {
        $url = config('services.ipl_ruko_api.url');

        if (! $url || ! $path) {
            return collect();
        }

        try {
            $request = Http::timeout(5)
                ->withToken(config('services.ipl_ruko_api.token'))
                ->acceptJson();

            if (! config('services.ipl_ruko_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.$path);

            if (! $response->successful()) {
                Log::warning('IplRukoApiService: permintaan gagal', [
                    'path' => $path,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return collect();
            }

            return $this->extractItems($response->json());
        } catch (\Throwable $e) {
            Log::warning('IplRukoApiService: exception', ['message' => $e->getMessage()]);
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
            $items = $items['items'] ?? $items['payments'] ?? $items['data'] ?? [];
        }

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)->values();
    }

    private function normalizePayment(array $item): ?object
    {
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

        return (object) [
            'id' => $item['id'] ?? null,
            'periode' => $item['periode'] ?? $item['period'] ?? '-',
            'tagihan' => $item['tagihan'] ?? $item['keterangan'] ?? $item['notes'] ?? null,
            'jatuh_tempo' => isset($item['jatuh_tempo']) ? $this->parseDate($item['jatuh_tempo']) : null,
            'hari' => $item['hari_ipl'] ?? $item['hari'] ?? '-',
            'nominal' => (float) ($item['nominal'] ?? $item['amount'] ?? 0),
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