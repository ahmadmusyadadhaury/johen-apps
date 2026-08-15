<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use App\Models\Employee;

foreach ([26030031, 26030057, 26030064] as $nik) {
    $e = Employee::where('nik', $nik)->first();
    if (! $e) continue;
    printf("\n=== %s %s ===\n", $e->nik, $e->nama);
    foreach (Attendance::where('employee_id', $e->id)->orderByDesc('date')->limit(14)->get() as $a) {
        printf("  %s  in=%s out=%s\n", $a->date->toDateString(), $a->time_in, $a->time_out);
    }
}
