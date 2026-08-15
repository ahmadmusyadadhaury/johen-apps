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

    $before = Attendance::where('employee_id', $e->id)->orderBy('date')->get();
    $beforeMap = $before->mapWithKeys(fn ($a) => [$a->date->toDateString() => [$a->time_in, $a->time_out]])->all();

    DB::beginTransaction();
    try {
        $svc->rebuildEmployeeAttendance($e);
        $after = Attendance::where('employee_id', $e->id)->orderBy('date')->get();
        $afterMap = $after->mapWithKeys(fn ($a) => [$a->date->toDateString() => [$a->time_in, $a->time_out]])->all();
    } finally {
        DB::rollBack();
    }

    $moved = [];   // date key differs but same logical session
    $changed = []; // in/out values differ materially (>2 sec)
    $onlyBefore = array_diff_key($beforeMap, $afterMap);
    $onlyAfter = array_diff_key($afterMap, $beforeMap);
    $both = array_intersect_key($beforeMap, $afterMap);

    foreach ($onlyBefore as $d => $v) $moved["-$d $v[0]/$v[1]"] = 1;
    foreach ($onlyAfter as $d => $v) $moved["+$d $v[0]/$v[1]"] = 1;
    foreach ($both as $d => $v) {
        $b = $beforeMap[$d]; $a = $afterMap[$d];
        if ($b[0] !== $a[0] || $b[1] !== $a[1]) {
            $changed[$d] = sprintf('%s/%s -> %s/%s', $b[0], $b[1], $a[0], $a[1]);
        }
    }

    if (empty($moved) && empty($changed)) {
        echo "[sama] {$e->nik} {$e->nama}\n";
        continue;
    }

    echo "[ubah] {$e->nik} {$e->nama}  (moved=".count($moved).", changed=".count($changed).")\n";
    foreach ($moved as $m => $_) echo "    $m\n";
    foreach ($changed as $d => $m) echo "    ~ $d $m\n";
}
