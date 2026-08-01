<?php

namespace Database\Seeders;

use App\Models\ItMaintenancePc;
use Illuminate\Database\Seeder;

class ItMaintenancePcSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            ItMaintenancePc::create(['nama' => "PC {$i}"]);
        }
    }
}
