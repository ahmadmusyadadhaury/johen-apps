<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function t(string $t): int
{
    return (int) substr($t, 0, 2) * 60 + (int) substr($t, 3, 2);
}

$rows = App\Models\Attendance::whereBetween('date', ['2026-08-13', '2026-08-15'])
    ->where('status', 'hadir')
    ->whereNotNull('time_in')
    ->orderBy('employee_id')->orderBy('date')->get();

echo "Records 08-13..08-15 yang MELANGGAR aturan user:\n";
echo "(1) subuh (in<07 & out<07) di tanggal D -> harus D-1\n";
echo "(2) in>=12 & out==null (sesi malam menggantung) di tanggal D\n";
echo "(3) in>=12 & out<07 -> sudah benar (sesi malam di tanggal masuk)\n\n";

$count = 0;
$seen = [];
foreach ($rows as $a) {
    $d = substr($a->date, 0, 10);
    $in = $a->time_in;
    $out = $a->time_out;
    $inMin = t($in);

    $subuh = $out && t($in) < 7 * 60 && t($out) < 7 * 60;
    $hanging = $inMin >= 12 * 60 && ! $out;

    if ($subuh || $hanging) {
        $count++;
        printf(
            "  %s %-28s %s in=%s out=%s  [%s]\n",
            $a->employee->nik,
            $a->employee->nama,
            $d,
            $in,
            $out ?? '-',
            $subuh ? 'SUBUH->prev' : ($hanging ? 'HANGING' : '')
        );
    }
}

echo "\nJumlah melanggar: $count\n";
