<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;

foreach ([26030031, 26030057, 26030064, 26030009] as $nik) {
    $e = Employee::where('nik', $nik)->first();
    if (! $e) continue;
    printf("\n=== %s %s (pos=%s, jam_kerja=%s, jam_masuk=%s) ===\n",
        $e->nik, $e->nama, $e->position, $e->jam_kerja, $e->jam_masuk);

    // session shape histogram over last 45 days
    $punches = AttendancePunch::where('employee_id', $e->id)
        ->where('punch_at', '>=', now()->subDays(45)->startOfDay())
        ->orderBy('punch_at')->get();
    $shapes = ['subuh' => 0, 'malam' => 0, 'siang' => 0, 'ambiguous' => 0];
    for ($i = 0; $i + 1 < $punches->count(); $i += 2) {
        $a = $punches[$i]->punch_at; $b = $punches[$i + 1]->punch_at;
        $ah = (int) $a->format('G'); $bh = (int) $b->format('G');
        if ($ah < 7 && $bh < 7) $shapes['subuh']++;
        elseif ($ah >= 12 && $bh < 12) $shapes['malam']++;
        else $shapes['siang']++;
    }
    printf("shapes (even-odd rough): %s\n", json_encode($shapes));
}
