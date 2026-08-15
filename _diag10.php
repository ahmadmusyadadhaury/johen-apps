<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$e = App\Models\Employee::where('nik', '26030068')->first();
$punches = App\Models\AttendancePunch::where('employee_id', $e->id)
    ->where('punch_at', '>=', now()->subDays(30)->toDateTimeString())
    ->orderBy('punch_at')->get();
echo "Khairul punches in last 30d: " . $punches->count() . "\n";
$early = 0;
foreach ($punches as $p) {
    $h = (int) $p->punch_at->format('G');
    if ($h < 7) {
        $early++;
    }
    if ($punches->count() <= 30 || true) {
        echo "  " . $p->punch_at->format('Y-m-d H:i:s') . " hour=" . $h . "\n";
    }
}
echo "early=$early total=" . $punches->count() . " frac=" . ($punches->count() ? $early / $punches->count() : 0) . "\n";
