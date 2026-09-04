<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            IndonesiaRegionSeeder::class,
            StrukturOrganisasiSeeder::class,
            AttendanceSeeder::class,
            AssetCategorySeeder::class,
            RunningRateSeeder::class,
        ]);
    }
}
