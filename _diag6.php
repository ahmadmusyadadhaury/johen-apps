<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$problem = [35, 5, 68, 54, 51, 26, 16, 33, 101];
foreach ($problem as $id) {
    $e = App\Models\Employee::find($id);
    if (! $e) {
        echo "$id: NOT FOUND\n";
        continue;
    }
    printf(
        "%s | %-28s | pos=%-40s | jam_kerja=%-14s\n",
        $e->nik,
        $e->nama,
        $e->position,
        $e->jam_kerja
    );
}
