<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use App\Models\User;

echo 'now: ' . now()->format('Y-m-d H:i:s') . PHP_EOL;

echo PHP_EOL . '--- 15 punch terakhir (60 menit terakhir) ---' . PHP_EOL;
$punches = AttendancePunch::where('punch_at', '>', now()->subHour())->orderByDesc('punch_at')->limit(15)->get();
foreach ($punches as $p) {
    echo $p->punch_at->format('H:i:s') . ' user=' . $p->machine_user_id
        . ' emp=' . ($p->employee_id ?? '-')
        . ($p->employee ? ' (' . $p->employee->nama . ')' : '')
        . ' created=' . $p->created_at->format('H:i:s') . PHP_EOL;
}

echo PHP_EOL . '--- punch serumit user terakhir? seluruh hari ini - 15 menit terakhir ---' . PHP_EOL;
$recent = AttendancePunch::where('punch_at', '>', now()->subMinutes(15))->orderByDesc('punch_at')->get();
echo 'jumlah punch 15 menit terakhir: ' . $recent->count() . PHP_EOL;

echo PHP_EOL . '--- Attendance hari ini (updated_at desc) ---' . PHP_EOL;
foreach (Attendance::whereDate('date', today())->orderByDesc('updated_at')->limit(10)->get() as $a) {
    echo $a->updated_at->format('H:i:s') . ' emp=' . $a->employee_id
        . ($a->employee ? ' (' . $a->employee->nama . ')' : '')
        . ' in=' . $a->time_in . ' status=' . $a->status . PHP_EOL;
}

echo PHP_EOL . '--- user yang login terakhir (untuk identifikasi akun "saya") ---' . PHP_EOL;
foreach (User::orderByDesc('updated_at')->limit(8)->get(['id', 'name', 'email', 'role']) as $u) {
    echo '#' . $u->id . ' ' . $u->name . ' ' . $u->email . ' role=' . $u->role . PHP_EOL;
}

echo PHP_EOL . '--- karyawan tanpa device_user_id tapi punya user akun ---' . PHP_EOL;
foreach (Employee::whereNull('device_user_id')->get(['id', 'nik', 'nama']) as $e) {
    echo '#' . $e->id . ' ' . $e->nik . ' ' . $e->nama . PHP_EOL;
}