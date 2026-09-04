<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndonesiaRegionSeeder extends Seeder
{
    public function run(): void
    {
        $dataDir = database_path('data');
        $chunk = 1000;

        DB::transaction(function () use ($dataDir, $chunk) {
            $this->seedFile("$dataDir/provinces.csv", Region::TYPE_PROVINSI, null, $chunk);
            $this->seedFile("$dataDir/regencies.csv", Region::TYPE_KABUPATEN, fn (array $row): string => $row[1], $chunk);
            $this->seedFile("$dataDir/districts.csv", Region::TYPE_KECAMATAN, fn (array $row): string => $row[1], $chunk);
            $this->seedFile("$dataDir/villages.csv", Region::TYPE_KELURAHAN, fn (array $row): string => $row[1], $chunk);
        });

        $this->command?->info('Indonesia region data seeded.');
    }

    private function seedFile(string $path, string $type, ?callable $parentResolver, int $chunk): void
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Cannot open $path");
        }

        $buffer = [];
        $count = 0;
        $seen = [];

        while (($row = fgetcsv($handle)) !== false) {
            $id = $row[0];

            if (isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;

            $name = trim($row[2] ?? $row[1]);
            $parentId = $parentResolver ? $parentResolver($row) : null;

            $buffer[] = [
                'id' => $id,
                'parent_id' => $parentId,
                'type' => $type,
                'name' => $name,
            ];

            if (count($buffer) >= $chunk) {
                Region::insert($buffer);
                $count += count($buffer);
                $buffer = [];
            }
        }

        if (! empty($buffer)) {
            Region::insert($buffer);
            $count += count($buffer);
        }

        fclose($handle);

        $this->command?->info("  - $type: $count rows.");
    }
}
