<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$aff = [26030041, 26030050, 26030053, 26030056, 26030070, 26030068, 26030064, 26030016, 26030009, 26030008, 26030033, 26030069, 26030054, 26030017, 26030012, 26030002, 26030001];
foreach ($aff as $nik) {
    $e = App\Models\Employee::where('nik', $nik)->first();
    if (! $e) {
        continue;
    }
    $nonHadir = App\Models\Attendance::where('employee_id', $e->id)->where('status', '!=', 'hadir')->count();
    $hadirMesin = App\Models\Attendance::where('employee_id', $e->id)->where('status', 'hadir')->where('method', 'mesin')->count();
    $hadirManual = App\Models\Attendance::where('employee_id', $e->id)->where('status', 'hadir')->where('method', '!=', 'mesin')->count();
    printf("%-10s %-28s non-hadir=%d hadir-mesin=%d hadir-manual=%d\n", $e->nik, $e->nama, $nonHadir, $hadirMesin, $hadirManual);
}
