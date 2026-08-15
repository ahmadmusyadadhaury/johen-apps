<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// employees with manual records
echo "Manual/izin/alpha/sakit records per employee:\n";
foreach (App\Models\Attendance::where('method', '!=', 'mesin')->select('employee_id')
    ->with('employee')->distinct()->get() as $a) {
    $c = App\Models\Attendance::where('employee_id', $a->employee_id)->where('method', '!=', 'mesin')->count();
    printf("  %-10s %-28s %d records\n", $a->employee->nik, $a->employee->nama, $c);
}

echo "\nAffected night/subuh employees manual records:\n";
$aff = [26030041, 26030050, 26030053, 26030056, 26030070, 26030068, 26030064, 26030016, 26030009, 26030008, 26030033, 26030069, 26030054, 26030017, 26030012];
foreach ($aff as $nik) {
    $e = App\Models\Employee::where('nik', $nik)->first();
    if (! $e) {
        echo "  $nik not found\n";
        continue;
    }
    $manual = App\Models\Attendance::where('employee_id', $e->id)->where('method', '!=', 'mesin')->count();
    $mesin = App\Models\Attendance::where('employee_id', $e->id)->where('method', 'mesin')->count();
    printf("  %-10s %-28s manual=%d mesin=%d\n", $e->nik, $e->nama, $manual, $mesin);
}
