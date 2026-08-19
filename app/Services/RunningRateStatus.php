<?php

namespace App\Services;

/**
 * Aturan status performa host.
 *
 * Status ditentukan dari perbandingan sold aktual terhadap progress yang
 * seharusnya berdasarkan waktu yang sudah berjalan (time-weighted), bukan
 * threshold persentase achievement yang asal. Aturan ini diisolasi agar mudah
 * diubah di kemudian hari.
 */
class RunningRateStatus
{
    public const TARGET_ACHIEVED = 'target_achieved';
    public const ON_TRACK = 'on_track';
    public const NEED_ATTENTION = 'need_attention';
    public const BEHIND_TARGET = 'behind_target';

    /**
     * Batas kekurangan sold (dalam persen dari target) untuk dikategorikan
     * "Behind Target". Jika kekurangannya lebih kecil, host "Need Attention".
     */
    private const BEHIND_THRESHOLD_RATIO = 0.15;

    /**
     * @return string salah satu konstanta status
     */
    public function determine(
        float $sold,
        float $target,
        int $totalWorkingDays,
        int $elapsedWorkingDays,
        int $remainingWorkingDays,
    ): string {
        if ($target <= 0) {
            return self::ON_TRACK;
        }

        if ($sold >= $target) {
            return self::TARGET_ACHIEVED;
        }

        if ($remainingWorkingDays <= 0) {
            return self::BEHIND_TARGET;
        }

        $basis = max($totalWorkingDays, 1);
        $expected = $target * ($elapsedWorkingDays / $basis);

        if ($sold >= $expected) {
            return self::ON_TRACK;
        }

        $shortfall = $expected - $sold;

        if ($shortfall > $target * self::BEHIND_THRESHOLD_RATIO) {
            return self::BEHIND_TARGET;
        }

        return self::NEED_ATTENTION;
    }

    /**
     * @return array{label: string, badge: string, dot: string}
     */
    public static function display(string $status): array
    {
        return match ($status) {
            self::TARGET_ACHIEVED => [
                'label' => 'Target Achieved',
                'badge' => 'badge-success',
                'dot' => 'bg-emerald-500',
            ],
            self::ON_TRACK => [
                'label' => 'On Track',
                'badge' => 'badge-info',
                'dot' => 'bg-blue-500',
            ],
            self::NEED_ATTENTION => [
                'label' => 'Need Attention',
                'badge' => 'badge-warning',
                'dot' => 'bg-amber-500',
            ],
            default => [
                'label' => 'Behind Target',
                'badge' => 'badge-danger',
                'dot' => 'bg-red-500',
            ],
        };
    }
}
