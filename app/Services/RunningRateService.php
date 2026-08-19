<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Position;
use App\Models\RunningRatePeriod;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class RunningRateService
{
    /**
     * Hari yang tersisa dalam periode terhitung dari tanggal acuan (default: hari ini).
     * Semua hari kalender (Senin–Minggu) dihitung sebagai hari kerja.
     */
    public function remainingWorkingDays(RunningRatePeriod $period, ?CarbonInterface $asOf = null): int
    {
        $asOf = $this->normalizeAsOf($asOf);
        $start = $period->tanggal_mulai->copy();
        $end = $period->tanggal_selesai->copy();

        if ($asOf->gt($end)) {
            return 0;
        }

        $from = $asOf->lt($start) ? $start : $asOf->copy();

        return $this->countWorkingDays($from, $end);
    }

    /**
     * Total hari dalam periode (Senin–Minggu).
     */
    public function totalWorkingDays(RunningRatePeriod $period): int
    {
        return $this->countWorkingDays($period->tanggal_mulai->copy(), $period->tanggal_selesai->copy());
    }

    /**
     * Hari yang sudah berjalan sejak awal periode hingga tanggal acuan (inclusive).
     */
    public function elapsedWorkingDays(RunningRatePeriod $period, ?CarbonInterface $asOf = null): int
    {
        $asOf = $this->normalizeAsOf($asOf);
        $start = $period->tanggal_mulai->copy();
        $end = $period->tanggal_selesai->copy();

        if ($asOf->lt($start)) {
            return 0;
        }

        $to = $asOf->gt($end) ? $end : $asOf->copy();

        return $this->countWorkingDays($start, $to);
    }

    public function countWorkingDays(CarbonInterface $from, CarbonInterface $to): int
    {
        $days = 0;
        $cursor = $from->copy()->startOfDay();

        while ($cursor->lte($to)) {
            $days++;
            $cursor->addDay();
        }

        return $days;
    }

    /**
     * Target seorang host pada sebuah periode.
     */
    public function targetFor(RunningRatePeriod $period, int $hostId): float
    {
        $target = $period->targets()->where('host_id', $hostId)->first();

        return $target ? (float) $target->target : 0.0;
    }

    /**
     * Total sold seorang host dalam periode.
     * Jika $asOf diberikan, hanya sold dengan tanggal <= $asOf yang dihitung.
     */
    public function soldFor(RunningRatePeriod $period, int $hostId, ?CarbonInterface $asOf = null): float
    {
        return (float) $period->dailySolds()
            ->where('host_id', $hostId)
            ->when($asOf, fn ($q) => $q->whereDate('tanggal', '<=', $asOf->toDateString()))
            ->sum('sold');
    }

    /**
     * Sisa target (tidak pernah negatif).
     */
    public function remainingTarget(float $target, float $sold): float
    {
        return max($target - $sold, 0);
    }

    public function achievement(float $sold, float $target): float
    {
        return $target > 0 ? ($sold / $target) * 100 : 0.0;
    }

    /**
     * RR Harian = Sisa Target / Sisa Hari Kerja.
     */
    public function dailyRunningRate(float $remainingTarget, int $remainingWorkingDays): float
    {
        if ($remainingTarget <= 0 || $remainingWorkingDays <= 0) {
            return 0.0;
        }

        return $remainingTarget / $remainingWorkingDays;
    }

    /**
     * RR Mingguan = Sisa Target / (Sisa Hari Kerja / 5). 1 minggu kerja = 5 hari.
     */
    public function weeklyRunningRate(float $remainingTarget, int $remainingWorkingDays): float
    {
        if ($remainingTarget <= 0 || $remainingWorkingDays <= 0) {
            return 0.0;
        }

        return $remainingTarget / ($remainingWorkingDays / 5);
    }

    /**
     * Metrik lengkap satu host pada sebuah periode.
     *
     * @return array{
     *     host_id: int, host: ?Employee, target: float, sold: float,
     *     achievement: float, remaining: float, remaining_working_days: int,
     *     rr_daily: float, rr_weekly: float, status: string
     * }
     */
    public function hostMetrics(RunningRatePeriod $period, int $hostId, ?CarbonInterface $asOf = null): array
    {
        $target = $this->targetFor($period, $hostId);
        $sold = $this->soldFor($period, $hostId, $asOf);
        $remaining = $this->remainingTarget($target, $sold);
        $remainingDays = $this->remainingWorkingDays($period, $asOf);

        $status = (new RunningRateStatus)->determine(
            sold: $sold,
            target: $target,
            totalWorkingDays: $this->totalWorkingDays($period),
            elapsedWorkingDays: $this->elapsedWorkingDays($period, $asOf),
            remainingWorkingDays: $remainingDays,
        );

        return [
            'host_id' => $hostId,
            'host' => Employee::find($hostId),
            'target' => round($target, 2),
            'sold' => round($sold, 2),
            'achievement' => round($this->achievement($sold, $target), 2),
            'remaining' => round($remaining, 2),
            'remaining_working_days' => $remainingDays,
            'rr_daily' => round($this->dailyRunningRate($remaining, $remainingDays), 2),
            'rr_weekly' => round($this->weeklyRunningRate($remaining, $remainingDays), 2),
            'status' => $status,
        ];
    }

    /**
     * Ringkasan tim untuk satu periode.
     * Jika $asOf diberikan, total sold hanya mencakup sold dengan tanggal <= $asOf.
     *
     * @param  array<int, int>|null  $hostIds
     * @return array{
     *     total_target: float, total_sold: float, achievement: float,
     *     remaining: float, remaining_working_days: int, rr_daily: float, rr_weekly: float
     * }
     */
    public function summary(RunningRatePeriod $period, ?array $hostIds = null, ?CarbonInterface $asOf = null): array
    {
        $targets = $period->targets()
            ->when($hostIds !== null, fn ($q) => $q->whereIn('host_id', $hostIds))
            ->get();

        $totalTarget = (float) $targets->sum('target');

        $soldsQuery = $period->dailySolds();
        if ($hostIds !== null) {
            $soldsQuery->whereIn('host_id', $hostIds);
        }
        $soldsQuery->when($asOf, fn ($q) => $q->whereDate('tanggal', '<=', $asOf->toDateString()));
        $totalSold = (float) $soldsQuery->sum('sold');

        $remaining = $this->remainingTarget($totalTarget, $totalSold);
        $remainingDays = $this->remainingWorkingDays($period, $asOf);

        return [
            'total_target' => round($totalTarget, 2),
            'total_sold' => round($totalSold, 2),
            'achievement' => round($this->achievement($totalSold, $totalTarget), 2),
            'remaining' => round($remaining, 2),
            'remaining_working_days' => $remainingDays,
            'rr_daily' => round($this->dailyRunningRate($remaining, $remainingDays), 2),
            'rr_weekly' => round($this->weeklyRunningRate($remaining, $remainingDays), 2),
        ];
    }

    /**
     * Akumulasi sold per tanggal untuk grafik perkembangan.
     *
     * @return \Illuminate\Support\Collection<int, array{tanggal: string, sold: float, cumulative: float}>
     */
    public function soldByDay(RunningRatePeriod $period, ?array $hostIds = null): \Illuminate\Support\Collection
    {
        $query = $period->dailySolds();
        if ($hostIds !== null) {
            $query->whereIn('host_id', $hostIds);
        }

        $rows = $query->selectRaw('tanggal, SUM(sold) as sold')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $cumulative = 0.0;

        return $rows->map(function ($row) use (&$cumulative) {
            $cumulative += (float) $row->sold;

            return [
                'tanggal' => Carbon::parse($row->tanggal)->format('Y-m-d'),
                'sold' => round((float) $row->sold, 2),
                'cumulative' => round($cumulative, 2),
            ];
        });
    }

    /**
     * Nama posisi root untuk sebuah divisi game.
     */
    public static function divisionRootPositionName(string $division): ?string
    {
        $map = [
            'FC Mobile' => 'Koordinator FC Mobile',
            'MLBB' => 'Koordinator MLBB',
            'PUBG' => 'Koordinator Johen PUBG',
            'Free Fire' => 'Koordinator Free Fire',
            'E-football' => 'Koordinator E-football',
            'Valorant' => 'Koordinator Valorant',
            'Roblox' => 'Koordinator Roblox',
            'Monkey PUBG' => 'Koordinator Monkey PUBG',
        ];

        return $map[$division] ?? null;
    }

    /**
     * Karyawan host dalam sebuah divisi game (dari struktur jabatan).
     */
    public static function hostsForDivision(string $division): Collection
    {
        $rootName = self::divisionRootPositionName($division);
        if (! $rootName) {
            return new Collection;
        }

        $root = Position::where('nama', $rootName)->first();
        if (! $root) {
            return new Collection;
        }

        $positionIds = self::allDescendantIds($root);

        return Employee::whereHas('positions', function ($q) use ($positionIds) {
            $q->whereIn('position_id', $positionIds);
        })
            ->orderBy('nama')
            ->get();
    }

    private static function allDescendantIds(Position $position): array
    {
        $ids = [];
        foreach ($position->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, self::allDescendantIds($child));
        }

        return $ids;
    }

    private function normalizeAsOf(?CarbonInterface $asOf): CarbonInterface
    {
        return $asOf ? $asOf->copy() : now();
    }
}
