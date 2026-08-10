<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElectricityApiService
{
    private ?Collection $topupsCache = null;

    private ?Collection $readingsCache = null;

    public function topups(): Collection
    {
        if ($this->topupsCache !== null) {
            return $this->topupsCache;
        }

        $items = $this->fetch(config('services.electricity_api.topups_path'));

        return $this->topupsCache = $items
            ->map(fn ($item) => $this->normalizeTopup((array) $item))
            ->filter()
            ->values();
    }

    public function readings(): Collection
    {
        if ($this->readingsCache !== null) {
            return $this->readingsCache;
        }

        $items = $this->fetch(config('services.electricity_api.readings_path'));

        return $this->readingsCache = $items
            ->map(fn ($item) => $this->normalizeReading((array) $item))
            ->filter()
            ->values();
    }

    public function checks(): Collection
    {
        $topups = $this->topups();
        $readings = $this->readings()
            ->sortBy(fn ($r) => $r->tanggal)
            ->values();

        $checks = $readings->map(function ($r) use ($topups) {
            $baseline = $topups
                ->filter(fn ($t) => $t->tanggal_bayar && $r->tanggal && $t->tanggal_bayar->lte($r->tanggal))
                ->sortByDesc(fn ($t) => $t->tanggal_bayar)
                ->first();

            $terpakai = $baseline
                ? max(0, round($baseline->jumlah_kwh - $r->sisa_kwh, 2))
                : null;

            return (object) [
                'id' => $r->id,
                'tanggal_check' => $r->tanggal,
                'sisa_kwh' => $r->sisa_kwh,
                'terpakai' => $terpakai,
                'status' => $r->status,
                'checker' => $r->checker,
                'catatan' => $r->catatan,
            ];
        });

        return $checks
            ->sortByDesc(fn ($c) => $c->tanggal_check)
            ->values();
    }

    public function sisaToken(): ?float
    {
        $latest = $this->readings()
            ->sortByDesc(fn ($r) => $r->tanggal ?? $r->id ?? 0)
            ->first();

        return $latest?->sisa_kwh;
    }

    private function fetch(?string $path): Collection
    {
        $url = config('services.electricity_api.url');

        if (! $url || ! $path) {
            return collect();
        }

        try {
            $request = Http::timeout(5)
                ->withToken(config('services.electricity_api.token'))
                ->acceptJson();

            if (! config('services.electricity_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.$path);

            if (! $response->successful()) {
                Log::warning('ElectricityApiService: permintaan gagal', [
                    'path' => $path,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return collect();
            }

            return $this->extractItems($response->json());
        } catch (\Throwable $e) {
            Log::warning('ElectricityApiService: exception', ['message' => $e->getMessage()]);
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
            $items = $items['items'] ?? $items['topups'] ?? $items['readings'] ?? $items['data'] ?? [];
        }

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)->values();
    }

    private function normalizeTopup(array $item): ?object
    {
        $tanggalBayar = $item['payment_date']
            ?? $item['tanggal_bayar']
            ?? $item['tgl_bayar']
            ?? $item['tanggal']
            ?? $item['paid_at']
            ?? $item['created_at']
            ?? null;

        if (! $tanggalBayar) {
            return null;
        }

        $tanggal = $this->parseDate($tanggalBayar);

        if (! $tanggal) {
            return null;
        }

        $creatorName = $item['creator_name']
            ?? $item['creator']
            ?? data_get($item, 'creator.name')
            ?? data_get($item, 'created_by.name')
            ?? $item['oleh']
            ?? $item['oleh_nama']
            ?? $item['created_by_name']
            ?? null;

        if (is_array($creatorName)) {
            $creatorName = $creatorName['name'] ?? null;
        }

        $bukti = $item['bukti_bayar']
            ?? $item['bukti_url']
            ?? $item['bukti']
            ?? $item['proof']
            ?? $item['foto']
            ?? null;

        return (object) [
            'id' => $item['id'] ?? null,
            'tanggal_bayar' => $tanggal,
            'periode' => $item['period'] ?? $item['periode'] ?? $item['bulan'] ?? '-',
            'jumlah_kwh' => (float) ($item['amount_kwh'] ?? $item['jumlah_kwh'] ?? $item['kwh'] ?? $item['jumlah_token'] ?? 0),
            'nominal' => (float) ($item['nominal'] ?? $item['harga'] ?? $item['amount'] ?? 0),
            'creator' => (object) ['name' => $creatorName],
            'catatan' => $item['notes'] ?? $item['catatan'] ?? $item['keterangan'] ?? $item['note'] ?? null,
            'bukti' => $bukti,
            'bukti_url' => $this->resolveUrl($bukti),
        ];
    }

    private function normalizeReading(array $item): ?object
    {
        $sisa = $item['remaining_kwh']
            ?? $item['sisa_kwh']
            ?? $item['sisa_token']
            ?? $item['sisa']
            ?? $item['stok']
            ?? null;

        if ($sisa === null) {
            return null;
        }

        $tanggal = $item['checked_date']
            ?? $item['tanggal']
            ?? $item['tanggal_check']
            ?? $item['created_at']
            ?? null;

        $checkerName = $item['checker_name']
            ?? data_get($item, 'checker.name')
            ?? data_get($item, 'checked_by.name')
            ?? null;

        return (object) [
            'id' => $item['id'] ?? null,
            'tanggal' => $tanggal ? $this->parseDate($tanggal) : null,
            'sisa_kwh' => (float) $sisa,
            'status' => $item['status'] ?? null,
            'checker' => (object) ['name' => $checkerName],
            'catatan' => $item['notes'] ?? $item['catatan'] ?? $item['keterangan'] ?? null,
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

    private function resolveUrl(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        $value = (string) $value;

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        $url = config('services.electricity_api.url');

        return rtrim((string) $url, '/').'/'.ltrim($value, '/');
    }
}