<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// hanging night records: in>=12 & out=null
$niks = ['26030021', '26030009', '26030019', '26030023', '26030048', '26030049', '26030057', '26030069', '26030070'];
foreach ($niks as $nik) {
    $e = App\Models\Employee::where('nik', $nik)->first();
    echo "== $nik {$e->nama} | pos={$e->position} | jam_kerja={$e->jam_kerja}\n";
    echo "  Punches 08-12..08-16:\n";
    foreach (App\Models\AttendancePunch::where('employee_id', $e->id)
        ->whereBetween('punch_at', ['2026-08-12 00:00:00', '2026-08-16 00:00:00'])
        ->orderBy('punch_at')->get() as $p) {
        echo "    " . $p->punch_at->format('Y-m-d H:i:s') . "\n";
    }
    echo "  Attendance 08-12..08-16:\n";
    foreach (App\Models\Attendance::where('employee_id', $e->id)
        ->whereBetween('date', ['2026-08-12', '2026-08-16'])->orderBy('date')->get() as $a) {
        echo "    " . $a->date . " in=" . $a->time_in . " out=" . $a->time_out . " st=" . $a->status . "\n";
    }
}
