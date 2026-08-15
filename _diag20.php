<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;

$e = Employee::where('nik', '26030068')->first();
$svc = app(App\Services\AttendanceSyncService::class);
$ref = new ReflectionMethod($svc, 'isSubuhShift');
$ref->setAccessible(true);

echo "=== Khairul isSubuhShift result for each punch ===\n";
$punches = AttendancePunch::where('employee_id', $e->id)->orderBy('punch_at')->get();
foreach ($punches as $p) {
    $isSubuh = $ref->invoke($svc, $e, Carbon\Carbon::parse($p->punch_at));
    if ($p->punch_at->between('2026-08-05', '2026-08-16')) {
        printf("%s subuh=%s\n", $p->punch_at->format('Y-m-d H:i:s'), $isSubuh ? 'Y' : 'N');
    }
}

DB::beginTransaction();
try {
    $svc->rebuildEmployeeAttendance($e);
    $after = Attendance::where('employee_id', $e->id)->orderBy('date')->get();
    foreach ($after as $a) {
        printf("  %s %s/%s\n", $a->date->toDateString(), $a->time_in, $a->time_out);
    }
} finally {
    DB::rollBack();
}
