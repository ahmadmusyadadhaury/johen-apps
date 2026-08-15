<?php

ini_set('memory_limit', '512M');
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AttendancePunch;
use App\Models\Employee;
use App\Models\Attendance;

// Semua punch dari sore tgl 14 s/d siang tgl 15, kelompokkan per karyawan.
$punches = AttendancePunch::whereBetween('punch_at', ['2026-08-14 12:00:00', '2026-08-15 12:00:00'])
    ->orderBy('punch_at')
    ->get()
    ->groupBy('employee_id');

foreach ($punches as $empId => $list) {
    $e = Employee::find($empId);
    echo PHP_EOL.'=== '.$empId.' '.($e->nama ?? '?').' | '.($e->position ?? '?').' | jk=['.($e->jam_kerja ?? '-').'] ==='.PHP_EOL;

    foreach ($list as $p) {
        echo '  P '.$p->punch_at.' '.$p->machine_user_id.PHP_EOL;
    }

    $atts = Attendance::where('employee_id', $empId)
        ->whereBetween('date', ['2026-08-14', '2026-08-15'])
        ->orderBy('date')
        ->get();
    foreach ($atts as $a) {
        echo '  A '.$a->date.' in='.$a->time_in.' out='.($a->time_out ?? '-').PHP_EOL;
    }
}