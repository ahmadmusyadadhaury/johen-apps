<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$empIds = App\Models\AttendancePunch::where('punch_at', '>=', now()->subDays(45)->toDateTimeString())
    ->whereNotNull('employee_id')->distinct()->pluck('employee_id');

$rows = [];
foreach ($empIds as $id) {
    $e = App\Models\Employee::find($id);
    if (! $e) {
        continue;
    }
    $punches = App\Models\AttendancePunch::where('employee_id', $id)
        ->where('punch_at', '>=', now()->subDays(45)->toDateTimeString())
        ->orderBy('punch_at')->get();
    $total = $punches->count();
    if ($total < 6) {
        continue;
    }
    $early = $punches->filter(fn ($p) => (int) $p->punch_at->format('G') < 7)->count();
    $rows[] = [
        'frac' => $early / $total,
        'total' => $total,
        'early' => $early,
        'nik' => $e->nik,
        'nama' => $e->nama,
        'pos' => $e->position,
    ];
}

usort($rows, fn ($a, $b) => $b['frac'] <=> $a['frac']);
echo "fraksi punch jam<07 (45 hari terakhir), diurutkan:\n";
foreach ($rows as $r) {
    $mark = (str_contains($r['pos'], '(Subuh)') ? 'SUBUH-POS' : '')
        . (str_contains($r['pos'], '(Malam)') ? ' MALAM-POS' : '');
    printf(
        "  %.2f  early=%-3d total=%-3d  %-10s %-28s pos=%s %s\n",
        $r['frac'],
        $r['early'],
        $r['total'],
        $r['nik'],
        $r['nama'],
        $r['pos'],
        $mark
    );
}
