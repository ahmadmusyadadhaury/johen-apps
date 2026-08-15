<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use App\Models\Employee;

$aff = [26030041, 26030050, 26030053, 26030056, 26030070, 26030068, 26030064, 26030016, 26030009, 26030008, 26030033, 26030069, 26030054, 26030017, 26030012];

$svc = app(App\Services\AttendanceSyncService::class);

foreach ($aff as $nik) {
    $e = Employee::where('nik', $nik)->first();
    if (! $e) continue;

    $before = Attendance::where('employee_id', $e->id)->orderBy('date')->get()
        ->map(fn ($a) => sprintf('%s %s/%s', $a->date->toDateString(), $a->time_in, $a->time_out))->all();

    DB::beginTransaction();
    try {
        $svc->rebuildEmployeeAttendance($e);
        $after = Attendance::where('employee_id', $e->id)->orderBy('date')->get()
            ->map(fn ($a) => sprintf('%s %s/%s', $a->date->toDateString(), $a->time_in, $a->time_out))->all();
    } finally {
        DB::rollBack();
    }

    if ($before === $after) {
        echo "[sama] {$e->nik} {$e->nama}\n";
        continue;
    }

    echo "[ubah] {$e->nik} {$e->nama}\n";
    foreach (array_diff($after, $before) as $row) echo "    + $row\n";
    foreach (array_diff($before, $after) as $row) echo "    - $row\n";
}
