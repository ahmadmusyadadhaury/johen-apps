<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AttendancePunch;

echo 'total punches: ' . AttendancePunch::count() . PHP_EOL;
echo 'punches after 20:22:35: ' . AttendancePunch::where('punch_at', '>', '2026-08-11 20:22:35')->count() . PHP_EOL . PHP_EOL;

echo '--- 15 punch terakhir ---' . PHP_EOL;
$last = AttendancePunch::orderByDesc('punch_at')->limit(15)->get(['machine_user_id', 'punch_at']);
foreach ($last as $p) {
    echo $p->machine_user_id . ' @ ' . $p->punch_at->format('Y-m-d H:i:s') . PHP_EOL;
}

echo PHP_EOL . '--- user_id di punches (top 25 by count) ---' . PHP_EOL;
$distinct = AttendancePunch::selectRaw('machine_user_id, COUNT(*) as cnt')
    ->groupBy('machine_user_id')->orderByDesc('cnt')->get();
echo 'distinct user ids: ' . $distinct->count() . PHP_EOL;
foreach ($distinct as $d) {
    echo $d->machine_user_id . ' x' . $d->cnt . PHP_EOL;
}

echo PHP_EOL . '--- cari user tertentu ---' . PHP_EOL;
foreach (['58', '5826', '102', '1026'] as $uid) {
    echo 'user_id=' . var_export($uid, true) . ' -> ' . AttendancePunch::where('machine_user_id', $uid)->count() . ' punches' . PHP_EOL;
}
