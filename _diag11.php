<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// statuses & methods in use
echo "Status counts:\n";
foreach (App\Models\Attendance::selectRaw('status, count(*) c')->groupBy('status')->get() as $r) {
    echo "  {$r->status}: {$r->c}\n";
}
echo "Method counts:\n";
foreach (App\Models\Attendance::selectRaw('method, count(*) c')->groupBy('method')->get() as $r) {
    echo "  {$r->method}: {$r->c}\n";
}
echo "Non-mesin records (manual):\n";
foreach (App\Models\Attendance::where('method', '!=', 'mesin')->limit(20)->get() as $a) {
    echo "  {$a->date} {$a->employee->nik} in={$a->time_in} out={$a->time_out} st={$a->status} method={$a->method}\n";
}

// check: do attendance records' time_in always match a punch time?
echo "\nSample: attendance time_in NOT matching any punch time (potential manual edits):\n";
$c = 0;
foreach (App\Models\Attendance::whereBetween('date', ['2026-08-01', '2026-08-15'])->where('status', 'hadir')->whereNotNull('time_in')->limit(400)->get() as $a) {
    $match = App\Models\AttendancePunch::where('employee_id', $a->employee_id)
        ->where('punch_at', 'like', "{$a->date} {$a->time_in}%")
        ->exists();
    if (! $match && $c < 15) {
        echo "  {$a->date} {$a->employee->nik} in={$a->time_in}\n";
        $c++;
    }
}
echo "sample shown: $c\n";
