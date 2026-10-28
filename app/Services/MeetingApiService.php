<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MeetingApiService
{
    public function rooms(): array
    {
        return Cache::remember('meeting_api_rooms', now()->addMinutes(5), function () {
            $response = $this->request()->get($this->url().config('services.meeting.rooms_path'));
            $this->ensureSuccessful($response);

            return $response->json() ?? [];
        });
    }

    public function requests(array $filters = []): array
    {
        $response = $this->request()->get($this->url().config('services.meeting.requests_path'), $filters);
        $this->ensureSuccessful($response);

        return $response->json() ?? [];
    }

    public function submit(array $data): array
    {
        $response = $this->request()->post($this->url().config('services.meeting.requests_path'), $data);
        $this->ensureSuccessful($response);

        return $response->json() ?? [];
    }

    public function approve(int $meetingId): array
    {
        $response = $this->request()->put($this->url().config('services.meeting.requests_path')."/{$meetingId}/approve");
        $this->ensureSuccessful($response);

        return $response->json() ?? [];
    }

    public function reject(int $meetingId, string $notes): array
    {
        $response = $this->request()->put($this->url().config('services.meeting.requests_path')."/{$meetingId}/reject", ['notes' => $notes]);
        $this->ensureSuccessful($response);

        return $response->json() ?? [];
    }

    private function request()
    {
        $request = Http::timeout(10)
            ->acceptJson()
            ->withToken($this->token());

        if (! config('services.meeting.verify_ssl')) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }

    private function token(): string
    {
        $configuredToken = config('services.meeting.token');
        if ($configuredToken) {
            return $configuredToken;
        }

        $token = Cache::remember('meeting_api_integration_token', now()->addMinutes(55), function () {
            $url = $this->url().config('services.meeting.login_path');
            $request = Http::timeout(10)->acceptJson();

            if (! config('services.meeting.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->post($url, [
                'username' => config('services.meeting.username'),
                'password' => config('services.meeting.password'),
            ]);

            if ($response->failed() || ! ($value = data_get($response->json(), 'token') ?? data_get($response->json(), 'data.token'))) {
                throw new RuntimeException('Login ke API meeting gagal. Periksa konfigurasi akun integrasi.');
            }

            return $value;
        });

        return (string) $token;
    }

    private function url(): string
    {
        $url = rtrim((string) config('services.meeting.url'), '/');
        if ($url === '') {
            throw new RuntimeException('Alamat API meeting belum dikonfigurasi.');
        }

        return $url;
    }

    private function ensureSuccessful($response): void
    {
        if ($response->successful()) {
            return;
        }

        if ($response->status() === 401) {
            Cache::forget('meeting_api_integration_token');
        }

        $message = data_get($response->json(), 'message')
            ?? data_get($response->json(), 'errors')
            ?? data_get($response->json(), 'error')
            ?? 'API meeting tidak dapat memproses permintaan.';

        if (is_array($message)) {
            $message = collect($message)->flatten()->first() ?? 'Data meeting tidak valid.';
        }

        throw new RuntimeException((string) $message);
    }
}
