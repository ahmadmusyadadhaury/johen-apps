<?php

ini_set('memory_limit', '512M');
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AttendancePunch;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

// Semua punch 5 hari terakhir, dikelompokkan per employee.
$from = '2026-08-10 00:00:00';
$to = '2026-08-16 23:59:59';

$punches = AttendancePunch::whereBetween('punch_at', [$from, $to])
    ->whereNotNull('employee_id')
    ->orderBy('punch_at')
    ->get()
    ->groupBy('employee_id');

$problems = [];

foreach ($punches as $empId => $list) {
    $e = Employee::find($empId);
    if (! $e) continue;

    // dedup dobel tap <90s
    $clean = [];
    $prevTs = null;
    foreach ($list as $p) {
        $ts = $p->punch_at->getTimestamp();
        if ($prevTs !== null && abs($ts - $prevTs) < 90) {
            continue;
        }
        $clean[] = $p;
        $prevTs = $ts;
    }

    // pasangkan menjadi sesi (in/out)
    $expected = []; // date => ['in'=>time,'out'=>time]
    $i = 0;
    $count = count($clean);
    while ($i < $count) {
        $in = $clean[$i]->punch_at;
        $inTime = $in->format('H:i:s');
        $inH = (int) $in->format('G');
        $out = null;
        $outTime = null;
        $outH = 99;
        if ($i + 1 < $count) {
            $out = $clean[$i + 1]->punch_at;
            $outTime = $out->format('H:i:s');
            $outH = (int) $out->format('G');
        }

        // tanggal sesi: default = tanggal masuk; jika masuk & keluar sama-sama
        // dini hari (00:00-06:59) => ikut hari sebelumnya (sesi subuh/malam).
        $date = $in->toDateString();
        if ($outTime !== null && $inH < 7 && $outH < 7) {
            $date = $in->copy()->subDay()->toDateString();
        } elseif ($outTime === null && $inH < 7) {
            // masuk dini hari tanpa keluar: kemungkinan sesi malam menunggu keluar
            $date = $in->copy()->subDay()->toDateString();
        }

        $expected[$date] = ['in' => $inTime, 'out' => $outTime];

        $i += 2;
    }

    // ambil rekap aktual
    $actual = Attendance::where('employee_id', $empId)
        ->whereBetween('date', ['2026-08-09', '2026-08-16'])
        ->orderBy('date')
        ->get()
        ->keyBy('date');

    // bandingkan
    foreach ($expected as $date => $sess) {
        $act = $actual->get($date);
        $actIn = $act ? $act->time_in : null;
        $actOut = $act ? $act->time_out : null;

        $inOk = $actIn === $sess['in'];
        $outOk = ($sess['out'] === null && ($actOut === null || $actOut !== null))
            ? true // sesi belum selesai: apa pun out aktual masih dianggap ok-ish
            : ($sess['out'] === null || $actOut === $sess['out']);

        if (! $inOk || (! $sess['out'] === null && ! $outOk)) {
            $problems[] = [
                'emp' => $empId.' '.($e->nama ?? '?'),
                'pos' => $e->position ?? '-',
                'jk' => $e->jam_kerja ?? '-',
                'date' => $date,
                'expected' => ($sess['in'] ?? '-').' / '.($sess['out'] ?? '-'),
                'actual' => ($actIn ?? 'NULL').' / '.($actOut ?? 'NULL'),
            ];
        }
    }

    // rekap aktual yang tidak punya sesi expected (salah tanggal / yatim)
    foreach ($actual as $dateKey => $act) {
        if (! isset($expected[$dateKey])) {
            $problems[] = [
                'emp' => $empId.' '.($e->nama ?? '?'),
                'pos' => $e->position ?? '-',
                'jk' => $e->jam_kerja ?? '-',
                'date' => $dateKey,
                'expected' => '-- / --',
                'actual' => ($act->time_in ?? 'NULL').' / '.($act->time_out ?? 'NULL'),
            ];
        }
    }
}

echo 'TOTAL MASALAH: '.count($problems).PHP_EOL;
foreach ($problems as $p) {
    echo sprintf("%-38s | %-30s | %-20s | %s | exp %s | act %s\n",
        $p['emp'], $p['pos'], $p['jk'], $p['date'], $p['expected'], $p['actual']);
}