<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$aff = [26030041, 26030050, 26030053, 26030056, 26030070, 26030068, 26030064, 26030016, 26030009, 26030008, 26030033, 26030069, 26030054, 26030017, 26030012];
foreach ($aff as $nik) {
    $e = App\Models\Employee::where('nik', $nik)->first();
    if (! $e) continue;
    $punches = App\Models\AttendancePunch::where('employee_id', $e->id)
        ->where('punch_at', '>=', now()->subDays(45)->startOfDay())
        ->get();
    if ($punches->isEmpty()) continue;
    $total = $punches->count();
    $pre07 = $punches->filter(fn ($p) => (int) $p->punch_at->format('G') < 7)->count();
    $evening = $punches->filter(fn ($p) => (int) $p->punch_at->format('G') >= 17 && (int) $p->punch_at->format('G') < 24)->count();
    $midday = $punches->filter(fn ($p) => (int) $p->punch_at->format('G') >= 7 && (int) $p->punch_at->format('G') < 17)->count();
    printf("%-10s %-28s pre07=%.2f evening=%.2f midday=%.2f n=%d\n",
        $e->nik, $e->nama, $pre07 / $total, $evening / $total, $midday / $total, $total);
}
