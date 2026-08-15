<?php

ini_set('memory_limit', '512M');
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('role', 'super_admin')->first();
if (! $user) {
    echo 'no super admin'.PHP_EOL;
    exit(1);
}
auth()->login($user);

use App\Livewire\AbsensiTable;

foreach (['2026-08-14', '2026-08-15', '2026-08-13'] as $date) {
    try {
        $c = new AbsensiTable;
        $c->date = $date;
        $c->tab = 'tim';
        $c->mount();
        $c->date = $date; // mount() resets to today, set again
        $html = $c->render()->render();
        echo "=== $date OK (len ".strlen($html).") ===\n";
    } catch (\Throwable $e) {
        echo "=== $date ERROR: ".get_class($e).': '.$e->getMessage().' @ '.$e->getFile().':'.$e->getLine()."\n";
    }
}