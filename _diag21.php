<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;

$e = Employee::where('nik', '26030068')->first();

echo "=== Khairul ALL punches (first punch to now) ===\n";
$punches = AttendancePunch::where('employee_id', $e->id)->orderBy('punch_at')->get();
foreach ($punches as $p) {
    printf("%s  %s\n", $p->punch_at->format('Y-m-d H:i:s'), $p->method);
}
echo "\n=== Current attendance ===\n";
foreach (Attendance::where('employee_id', $e->id)->orderBy('date')->get() as $a) {
    printf("%s  in=%s out=%s status=%s method=%s\n", $a->date->toDateString(), $a->time_in, $a->time_out, $a->status, $a->method);
}
echo "\n=== Employee shift config ===\n";
printf("position=%s\njam_kerja=%s\njam_masuk=%s\n", $e->position, $e->jam_kerja, $e->jam_masuk);
foreach ($e->shiftHistories()->get() as $h) {
    printf("history effective=%s jam_kerja=%s jam_masuk=%s\n", $h->effective_date->toDateString(), $h->jam_kerja, $h->jam_masuk);
}
