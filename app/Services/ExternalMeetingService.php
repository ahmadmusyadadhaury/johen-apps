<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExternalMeetingService
{
    private const CACHE_KEY = 'external_meetings';

    public function fetch(bool $fresh = false): Collection
    {
        if (! $fresh) {
            $cached = Cache::get(self::CACHE_KEY);

            if ($cached instanceof Collection) {
                return $cached;
            }
        }

        $url = config('services.meeting.url');

        if (! $url) {
            return collect();
        }

        $token = $this->authToken();

        if (! $token) {
            return collect();
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
                return Cache::get(self::CACHE_KEY, collect());
            }

            $payload = $response->json();
            $items = $this->extractItems($payload);
            $meetings = $items->map(fn ($item) => $this->normalize($item))->filter();

            if ($meetings->isNotEmpty()) {
                Cache::put(self::CACHE_KEY, $meetings, $this->cacheTtl());
            }

            return $meetings;
        } catch (\Throwable $e) {
            report($e);

            return Cache::get(self::CACHE_KEY, collect());
        }
    }

    private function cacheTtl(): int
    {
        $ttl = (int) config('services.meeting.cache_ttl', 60);

        return max(0, $ttl);
    }

    private function authToken(): ?string
    {
        $token = config('services.meeting.token');

        if ($token) {
            return $token;
        }

        return $this->loginToken();
    }

    private function loginToken(): ?string
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

    private function valueOf(array $item, string|int $key, mixed $fallback = null): mixed
    {
        return $item[$key] ?? $fallback;
    }

    private function stringOf(array $item, string|int $key, mixed $fallback = null): mixed
    {
        $value = $item[$key] ?? null;

        if (is_string($value)) {
            return $value;
        }

        if (is_array($value) && isset($value['name'])) {
            return $value['name'];
        }

        if (is_object($value) && isset($value->name)) {
            return $value->name;
        }

        return $fallback;
    }

    private function normalize(array $item): ?object
    {
        $date = $item['date'] ?? $item['tanggal'] ?? $item['meeting_date'] ?? null;

        $isWeekly = ! empty($item['is_weekly']);

        if (! $date && ! $isWeekly && empty($item['recurring_type']) && empty($item['recurring_day'])) {
            return null;
        }

        $creatorName = $item['requester']['name']
            ?? $item['requester_name']
            ?? $item['creator']['name']
            ?? $item['creator_name']
            ?? $item['created_by_name']
            ?? '-';

        $rawId = $item['id'] ?? null;

        if (is_numeric($rawId)) {
            $id = 'ext-' . $rawId;
            $externalId = (string) $rawId;
        } else {
            $externalId = md5(($date ? Carbon::parse($date)->toDateString() : '') . '|' . $item['title'] . '|' . ($item['room']['name'] ?? $item['room'] ?? ''));
            $id = 'ext-' . $externalId;
        }

        $recurringType = $isWeekly
            ? 'weekly'
            : ($item['recurring_type'] ?? $item['jenis'] ?? null);

        $recurringDay = $isWeekly
            ? $this->normalizeDay($item['weekly_day'] ?? null)
            : ($item['recurring_day'] ?? null);

        $startTime = $item['start_time'] ?? $item['jam_mulai'] ?? null;

        if (empty($startTime) && $isWeekly && ! empty($item['weekly_time'])) {
            $startTime = $item['weekly_time'];
        }

        return (object) [
            'id' => $id,
            'external_id' => $externalId,
            'title' => $item['title'] ?? $item['nama'] ?? $item['nama_meeting'] ?? 'Meeting',
            'room' => $this->stringOf($item, 'room', $item['ruangan'] ?? $item['tempat'] ?? '-'),
            'team' => $this->stringOf($item, 'team', $item['tim'] ?? null),
            'date' => $date ? Carbon::parse($date) : null,
            'start_time' => $startTime ?: '00:00',
            'end_time' => $item['end_time'] ?? $item['jam_selesai'] ?? '00:00',
            'actual_end_time' => ! empty($item['actual_end_time']) ? Carbon::parse($item['actual_end_time']) : null,
            'status' => $item['status'] ?? 'booked',
            'description' => $item['description'] ?? $item['deskripsi'] ?? null,
            'recurring_type' => $recurringType,
            'recurring_day' => $recurringDay,
            'creator' => (object) ['name' => $creatorName],
        ];
    }

    private function normalizeDay(mixed $day): ?string
    {
        if (! $day) {
            return null;
        }

        $map = [
            'senin' => 'monday',
            'selasa' => 'tuesday',
            'rabu' => 'wednesday',
            'kamis' => 'thursday',
            'jumat' => 'friday',
            'jum\'at' => 'friday',
            'sabtu' => 'saturday',
            'minggu' => 'sunday',
            'monday' => 'monday',
            'tuesday' => 'tuesday',
            'wednesday' => 'wednesday',
            'thursday' => 'thursday',
            'friday' => 'friday',
            'saturday' => 'saturday',
            'sunday' => 'sunday',
        ];

        return $map[strtolower(trim((string) $day))] ?? strtolower(trim((string) $day));
    }
}
