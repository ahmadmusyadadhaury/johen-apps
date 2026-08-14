<?php

namespace App\Console\Commands;

use App\Models\Meeting;
use App\Models\User;
use App\Services\ExternalMeetingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('meetings:sync {--fresh : Lewati cache dan ambil langsung dari API}')]
#[Description('Sinkronkan jadwal meeting dari office.johengaming.store ke tabel lokal')]
class SyncMeetings extends Command
{
    private const VALID_STATUSES = ['booked', 'ongoing', 'queue', 'completed', 'cancelled'];

    public function handle(ExternalMeetingService $service): int
    {
        $this->info('Mengambil jadwal meeting dari ' . config('services.meeting.url') . ' ...');

        $external = $service->fetch((bool) $this->option('fresh'));

        if ($external->isEmpty()) {
            $this->warn('Tidak ada data meeting dari API (atau API tidak merespons). Tidak ada perubahan.');

            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;
        $fallbackUserId = $this->fallbackUserId();

        foreach ($external as $item) {
            $data = [
                'title' => $item->title,
                'room' => $item->room,
                'team' => $item->team,
                'date' => $item->date?->toDateString(),
                'start_time' => $item->start_time,
                'end_time' => $item->end_time,
                'actual_end_time' => $item->actual_end_time,
                'status' => $this->mapStatus($item->status),
                'description' => $item->description,
                'recurring_type' => $item->recurring_type,
                'recurring_day' => $item->recurring_day,
            ];

            $existing = $this->findExisting($item);

            if ($existing) {
                $existing->fill($data);

                if (empty($existing->external_id)) {
                    $existing->external_id = $item->external_id;
                }

                if ($existing->isDirty()) {
                    $existing->save();
                    $updated++;
                }

                continue;
            }

            $creatorId = $this->matchUserId($item->creator->name ?? null) ?? $fallbackUserId;

            Meeting::create(array_merge($data, [
                'external_id' => $item->external_id,
                'created_by' => $creatorId,
            ]));

            $created++;
        }

        $this->info("Sinkronisasi selesai: {$created} dibuat, {$updated} diperbarui.");

        return self::SUCCESS;
    }

    private function findExisting(object $item): ?Meeting
    {
        if (! empty($item->external_id)) {
            $byExternal = Meeting::where('external_id', $item->external_id)->first();

            if ($byExternal) {
                return $byExternal;
            }
        }

        return Meeting::query()
            ->where('title', $item->title)
            ->when(! empty($item->recurring_type), function ($q) use ($item) {
                $q->whereNotNull('recurring_type')
                    ->where('recurring_type', $item->recurring_type)
                    ->where('recurring_day', $item->recurring_day);
            }, function ($q) use ($item) {
                $q->whereNull('recurring_type')
                    ->when($item->date, fn ($q) => $q->whereDate('date', $item->date->toDateString()))
                    ->when(empty($item->date), fn ($q) => $q->whereNull('date'))
                    ->where('start_time', $item->start_time);
            })
            ->first();
    }

    private function mapStatus(?string $status): string
    {
        $status = strtolower((string) $status);

        if ($status === 'approved') {
            return 'booked';
        }

        return in_array($status, self::VALID_STATUSES, true) ? $status : 'booked';
    }

    private function matchUserId(?string $name): ?int
    {
        if (! $name || $name === '-') {
            return null;
        }

        $needle = strtolower($name);

        $user = User::query()
            ->select('id', 'name')
            ->get()
            ->first(fn ($u) => $this->nameMatches(strtolower((string) $u->name), $needle));

        return $user?->id;
    }

    private function nameMatches(string $a, string $b): bool
    {
        if ($a === $b) {
            return true;
        }

        return (strlen($a) >= 8 && str_contains($a, $b)) || (strlen($b) >= 8 && str_contains($b, $a));
    }

    private function fallbackUserId(): int
    {
        $user = User::query()
            ->whereIn('role', ['gm_ceo', 'super_admin', 'admin'])
            ->orderBy('id')
            ->value('id');

        return $user ?? 1;
    }
}
