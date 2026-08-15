<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach ([['26030008', 'Raiyatria'], ['26030016', 'Ridwan']] as [$nik, $label]) {
    $e = App\Models\Employee::where('nik', $nik)->first();
    echo "== $label ($nik)\nPunches 08-01..08-16:\n";
    foreach (App\Models\AttendancePunch::where('employee_id', $e->id)
        ->whereBetween('punch_at', ['2026-08-01 00:00:00', '2026-08-16 00:00:00'])
        ->orderBy('punch_at')->get() as $p) {
        echo "  " . $p->punch_at->format('Y-m-d H:i:s') . "\n";
    }
    echo "Attendance:\n";
    foreach (App\Models\Attendance::where('employee_id', $e->id)
        ->whereBetween('date', ['2026-08-01', '2026-08-15'])->orderBy('date')->get() as $a) {
        echo "  " . $a->date . " in=" . $a->time_in . " out=" . $a->time_out . " status=" . $a->status . "\n";
    }
}
