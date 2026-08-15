<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$aff = [26030041, 26030050, 26030053, 26030056, 26030070, 26030068, 26030064, 26030016, 26030009, 26030008, 26030033, 26030069, 26030054, 26030017, 26030012, 26030003, 26030004, 26030006, 26030066];
foreach ($aff as $nik) {
    $e = App\Models\Employee::where('nik', $nik)->first();
    if (! $e) continue;
    $punches = App\Models\AttendancePunch::where('employee_id', $e->id)
        ->where('punch_at', '>=', now()->subDays(45)->startOfDay())
        ->get();
    if ($punches->isEmpty()) {
        printf("%-10s %-28s (no punches)\n", $e->nik, $e->nama);
        continue;
    }
    $total = $punches->count();
    $pre07 = $punches->filter(fn ($p) => (int) $p->punch_at->format('G') < 7)->count();
    $after12 = $punches->filter(fn ($p) => (int) $p->punch_at->format('G') >= 12)->count();
    $bothpre07 = 0; // sessions with in AND out before 07:00
    $sess = 0;
    $sorted = $punches->sortBy('punch_at')->values();
    for ($i = 0; $i + 1 < $sorted->count(); $i += 2) {
        $a = $sorted[$i]->punch_at; $b = $sorted[$i + 1]->punch_at;
        $sess++;
        if ((int) $a->format('G') < 7 && (int) $b->format('G') < 7) $bothpre07++;
    }
    printf("%-10s %-28s pos=%-22s n=%2d frac<07=%.2f any>12=%s sessBothPre07=%d/%d\n",
        $e->nik, $e->nama, mb_substr($e->position, 0, 22), $total, $pre07 / max($total, 1),
        $after12 > 0 ? 'Y' : 'N', $bothpre07, $sess);
}
