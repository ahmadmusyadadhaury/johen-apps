<?php

ini_set('memory_limit', '512M');
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;

$user = User::where('role', 'super_admin')->first();
if (! $user) {
    echo 'no super admin'.PHP_EOL;
    exit(1);
}

auth()->login($user);

$request = Request::create('/hris/absensi', 'GET');
$response = $kernel->handle($request);
echo 'STATUS: '.$response->getStatusCode().PHP_EOL;

$content = $response->getContent();
if ($response->getStatusCode() !== 200) {
    echo substr($content, 0, 4000).PHP_EOL;
} else {
    echo 'LEN: '.strlen($content).PHP_EOL;
    // look for error indicators
    foreach (['exception', 'Undefined variable', 'Error', 'Whoops', 'w-full text-sm'] as $needle) {
        if (str_contains($content, $needle)) {
            echo 'FOUND NEEDLE: '.$needle.PHP_EOL;
        }
    }
}