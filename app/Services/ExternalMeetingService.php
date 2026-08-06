<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExternalMeetingService
{
    private const CACHE_KEY = 'external_meetings';
    private const CACHE_TTL_SECONDS = 300;

    public function fetch(): Collection
    {
        $meetings = collect();

        $url = config('services.meeting.url');

        if (! $url) {
            return $meetings;
        }

        $token = $this->token();

        if (! $token) {
            return $meetings;
        }

        try {
            $request = Http::timeout(2)
                ->withToken($token)
                ->acceptJson();

            if (! config('services.meeting.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url . config('services.meeting.path'));

            if (! $response->successful()) {
                return Cache::get(self::CACHE_KEY, $meetings);
            }

            $payload = $response->json();
            $items = $this->extractItems($payload);
            $meetings = $items->map(fn ($item) => $this->normalize($item))->filter();

            Cache::put(self::CACHE_KEY, $meetings, self::CACHE_TTL_SECONDS);

            return $meetings;
        } catch (\Throwable $e) {
            report($e);

            return Cache::get(self::CACHE_KEY, $meetings);
        }
    }

    private function token(): ?string
    {
        return Cache::remember('external_meeting_token', now()->addHour(), function () {
            $url = config('services.meeting.url');

            if (! $url) {
                return null;
            }

            try {
                $request = Http::timeout(2)
                    ->acceptJson();

                if (! config('services.meeting.verify_ssl')) {
                    $request = $request->withoutVerifying();
                }

                $response = $request->post($url . config('services.meeting.login_path'), [
                    'username' => config('services.meeting.username'),
                    'password' => config('services.meeting.password'),
                ]);

                if (! $response->successful()) {
                    return null;
                }

                return data_get($response->json(), 'data.token');
            } catch (\Throwable $e) {
                report($e);

                return null;
            }
        });
    }

    private function extractItems(?array $payload): Collection
    {
        if (! is_array($payload)) {
            return collect();
        }

        if (isset($payload['data'])) {
            $data = $payload['data'];

            if (is_array($data) && ! array_is_list($data) && isset($data['meetings'])) {
                return collect($data['meetings'])
                    ->when(isset($data['recurring']), fn ($c) => $c->merge(collect($data['recurring'])))
                    ->values();
            }

            if (is_array($data) && array_is_list($data)) {
                return collect($data);
            }

            return collect();
        }

        if (array_is_list($payload)) {
            return collect($payload);
        }

        return collect();
    }

    private function normalize(array $item): ?object
    {
        $date = $item['date'] ?? $item['tanggal'] ?? $item['meeting_date'] ?? null;

        if (! $date && empty($item['recurring_type']) && empty($item['recurring_day'])) {
            return null;
        }

        $creatorName = $item['creator']['name']
            ?? $item['creator_name']
            ?? $item['created_by_name']
            ?? '-';

        $id = $item['id'] ?? 'ext-' . md5(($date ? Carbon::parse($date)->toDateString() : '') . '|' . $item['title'] . '|' . $item['room'] ?? '');

        return (object) [
            'id' => $id,
            'title' => $item['title'] ?? $item['nama'] ?? $item['nama_meeting'] ?? 'Meeting',
            'room' => $item['room'] ?? $item['ruangan'] ?? $item['tempat'] ?? '-',
            'team' => $item['team'] ?? $item['tim'] ?? null,
            'date' => $date ? Carbon::parse($date) : null,
            'start_time' => $item['start_time'] ?? $item['jam_mulai'] ?? '00:00',
            'end_time' => $item['end_time'] ?? $item['jam_selesai'] ?? '00:00',
            'actual_end_time' => ! empty($item['actual_end_time']) ? Carbon::parse($item['actual_end_time']) : null,
            'status' => $item['status'] ?? 'booked',
            'description' => $item['description'] ?? $item['deskripsi'] ?? null,
            'recurring_type' => $item['recurring_type'] ?? $item['jenis'] ?? null,
            'recurring_day' => $item['recurring_day'] ?? null,
            'creator' => (object) ['name' => $creatorName],
        ];
    }
}
