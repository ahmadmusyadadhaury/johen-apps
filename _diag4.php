<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$e = App\Models\Employee::where('nik', '26030003')->first();
echo "Punches 07-27..08-16 for {$e->nama}:\n";
foreach (App\Models\AttendancePunch::where('employee_id', $e->id)
    ->whereBetween('punch_at', ['2026-07-27 00:00:00', '2026-08-16 00:00:00'])
    ->orderBy('punch_at')->get() as $p) {
    echo "  " . $p->punch_at->format('Y-m-d H:i:s') . "\n";
}

echo "\nAttendance all:\n";
foreach (App\Models\Attendance::where('employee_id', $e->id)->orderBy('date')->get() as $a) {
    echo "  " . $a->date . " in=" . $a->time_in . " out=" . $a->time_out . " status=" . $a->status . " method=" . $a->method . "\n";
}
