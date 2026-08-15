<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;

echo "=== RULE 1: Night session recorded on wrong (out) date ===\n";
echo "Record where in-punch exists on date+1 but record dated date+1 with night time_out\n\n";

$att = Attendance::with('employee')->where('status', 'hadir')
    ->whereNotNull('time_in')->whereNotNull('time_out')
    ->orderBy('date')->get();

$violations = 0;

// Night session should be on in-date. Check records whose time_in is evening
// (>= 12:00) — if so it should be recorded on the in-punch date. A record dated
// D with time_in in evening is fine. A record dated D with time_in early but
// time_out late-night *and* a matching evening in-punch on D-1 is a violation.
foreach ($att as $a) {
    $inH = (int) substr((string) $a->time_in, 0, 2);
    $outH = (int) substr((string) $a->time_out, 0, 2);

    // Subuh session (both early morning same day) — checked in rule 2.
    if ($inH < 7 && $outH < 7) continue;

    // A night session is [evening in, early out]. Check record dated D where
    // time_in >= 12 (evening start). It must be dated the punch day. Verify via
    // punches: does a punch equal to time_in exist on the record date?
    if ($inH >= 12) {
        $punch = AttendancePunch::where('employee_id', $a->employee_id)
            ->whereDate('punch_at', $a->date->toDateString())
            ->whereRaw('TIME(punch_at) = ?', [$a->time_in])
            ->first();
        if (! $punch) {
            // time_in on record date D not found as a punch on D => maybe recorded
            // on wrong date (the real in-punch is on D-1 or D+1).
            $p1 = AttendancePunch::where('employee_id', $a->employee_id)
                ->whereDate('punch_at', $a->date->copy()->subDay())
                ->whereRaw('TIME(punch_at) = ?', [$a->time_in])->first();
            $p2 = AttendancePunch::where('employee_id', $a->employee_id)
                ->whereDate('punch_at', $a->date->copy()->addDay())
                ->whereRaw('TIME(punch_at) = ?', [$a->time_in])->first();
            if ($p1) {
                $violations++;
                printf("[NIGHT-WRONG-DATE] %-10s %-26s rec=%s %s/%s  inPunchWasOn=%s\n",
                    $a->employee->nik, $a->employee->nama, $a->date->toDateString(), $a->time_in, $a->time_out, $a->date->copy()->subDay()->toDateString());
            }
        }
    }
}

echo "\n=== RULE 2: Subuh session (in<07 & out<07) recorded on wrong date ===\n";
echo "Per rule, subuh session in-day D out-day D => rekap D-1.\n";
foreach ($att as $a) {
    $inH = (int) substr((string) $a->time_in, 0, 2);
    $outH = (int) substr((string) $a->time_out, 0, 2);
    if (! ($inH < 7 && $outH < 7)) continue;

    // A correctly-recorded subuh session has record date = punch date - 1.
    $inPunch = AttendancePunch::where('employee_id', $a->employee_id)
        ->whereDate('punch_at', $a->date->toDateString())
        ->whereRaw('TIME(punch_at) = ?', [$a->time_in])->first();
    $inPunchPrev = AttendancePunch::where('employee_id', $a->employee_id)
        ->whereDate('punch_at', $a->date->copy()->addDay())
        ->whereRaw('TIME(punch_at) = ?', [$a->time_in])->first();

    if ($inPunch && ! $inPunchPrev) {
        $violations++;
        printf("[SUBUH-WRONG-DATE] %-10s %-26s rec=%s %s/%s  shouldBeDated=%s\n",
            $a->employee->nik, $a->employee->nama, $a->date->toDateString(), $a->time_in, $a->time_out, $a->date->copy()->addDay()->toDateString());
    }
}

echo "\nTotal violations: $violations\n";
