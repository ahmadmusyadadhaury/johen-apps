<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['26030041', '26030056', '26030070', '26030050', '26030068', '26030069'] as $nik) {
    $e = App\Models\Employee::where('nik', $nik)->first();
    echo "== $nik {$e->nama} | pos={$e->position}\n";
    echo "  Punches 08-12..08-16:\n";
    foreach (App\Models\AttendancePunch::where('employee_id', $e->id)
        ->whereBetween('punch_at', ['2026-08-12 00:00:00', '2026-08-16 00:00:00'])
        ->orderBy('punch_at')->get() as $p) {
        echo "    " . $p->punch_at->format('Y-m-d H:i:s') . "\n";
    }
    echo "  Attendance 08-11..08-16:\n";
    foreach (App\Models\Attendance::where('employee_id', $e->id)
        ->whereBetween('date', ['2026-08-11', '2026-08-16'])->orderBy('date')->get() as $a) {
        echo "    " . $a->date . " in=" . $a->time_in . " out=" . $a->time_out . " st=" . $a->status . "\n";
    }
}
