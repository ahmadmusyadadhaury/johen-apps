<?php

ini_set('memory_limit', '512M');
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use App\Models\Employee;

echo '=== EMPLOYEES with jam_kerja crossing midnight (start>end) or containing >17:00 start ==='.PHP_EOL;
$emps = Employee::where('status', 'aktif')->get();
foreach ($emps as $e) {
    if (! $e->jam_kerja) {
        continue;
    }
    $jk = $e->jam_kerja;
    if (preg_match('/(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})/', $jk, $m)) {
        $startMin = $m[1] * 60 + $m[2];
        $endMin = $m[3] * 60 + $m[4];
        $crosses = $endMin <= $startMin;
        if ($crosses || $startMin >= 12 * 60) {
            echo $e->nik.' | '.$e->nama.' | pos=['.$e->position.'] | jk=['.$jk.'] | start='.$startMin.' end='.$endMin.($crosses ? ' CROSSES' : '').PHP_EOL;
        }
    }
}

echo PHP_EOL.'=== ALL ATTENDANCE 08-14 & 08-15 (in < out means same-day, out < in means overnight session) ==='.PHP_EOL;
$atts = Attendance::whereBetween('date', ['2026-08-14', '2026-08-15'])
    ->where('status', 'hadir')
    ->orderBy('employee_id')->orderBy('date')
    ->get();
foreach ($atts as $a) {
    $e = $a->employee;
    echo $a->date.' | '.$a->employee_id.' | '.($e->nama ?? '?').' | '.($e->position ?? '?').' | in='.$a->time_in.' out='.$a->time_out.PHP_EOL;
}