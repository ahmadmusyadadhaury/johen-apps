<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\RunningRateDailySold;
use App\Models\RunningRateHostTarget;
use App\Models\RunningRatePeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RunningRateSeeder extends Seeder
{
    public function run(): void
    {
        $divisi = 'Free Fire';

        $period = RunningRatePeriod::updateOrCreate(
            ['divisi' => $divisi, 'nama' => 'Agustus 2026'],
            [
                'tanggal_mulai' => Carbon::parse('2026-08-01'),
                'tanggal_selesai' => Carbon::parse('2026-08-31'),
                'is_active' => true,
            ],
        );

        RunningRatePeriod::where('divisi', $divisi)->where('id', '!=', $period->id)->update(['is_active' => false]);

        $targets = [
            '26030035' => 15, // Fiqri Mauludin (MAUL)
            '26030042' => 18, // Selvianti Amalia (SELVI)
            '26030064' => 7,  // Pratiwi Audina Wijaya (TIWI)
            '26030070' => 7,  // Rian Ardianysah (RIAN)
        ];

        foreach ($targets as $nik => $target) {
            $employee = Employee::where('nik', $nik)->first();
            if (!$employee) {
                continue;
            }

            RunningRateHostTarget::updateOrCreate(
                ['period_id' => $period->id, 'host_id' => $employee->id],
                ['target' => $target],
            );
        }

        $inputBy = User::where('username', 'rafly')->value('id');

        $solds = [
            '26030035' => [['2026-08-17', 2], ['2026-08-18', 3], ['2026-08-19', 3]], // MAUL total 8
            '26030042' => [['2026-08-17', 2], ['2026-08-18', 2], ['2026-08-19', 2]], // SELVI total 6
            '26030064' => [['2026-08-17', 2], ['2026-08-18', 2], ['2026-08-19', 2]], // TIWI total 6
            '26030070' => [['2026-08-17', 2], ['2026-08-19', 2]],                    // RIAN total 4
        ];

        foreach ($solds as $nik => $rows) {
            $employee = Employee::where('nik', $nik)->first();
            if (!$employee) {
                continue;
            }

            foreach ($rows as [$tanggal, $sold]) {
                RunningRateDailySold::updateOrCreate(
                    ['period_id' => $period->id, 'host_id' => $employee->id, 'tanggal' => $tanggal],
                    ['sold' => $sold, 'input_by' => $inputBy],
                );
            }
        }

        $this->command?->info('Running Rate Free Fire seeded.');
    }
}
