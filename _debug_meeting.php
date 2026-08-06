<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

config(['services.meeting.verify_ssl' => false]);
config(['services.meeting.username' => 'gm']);
config(['services.meeting.password' => 'password']);
Cache::flush();

$login = Http::withoutVerifying()->post('https://icing-geriatric-idiom.ngrok-free.dev/api/login', [
    'username' => 'gm',
    'password' => 'password',
]);
var_dump('login status', $login->status(), data_get($login->json(), 'data.token'));

$token = data_get($login->json(), 'data.token');

$meetings = Http::withoutVerifying()->withToken($token)->acceptJson()->get('https://icing-geriatric-idiom.ngrok-free.dev/api/meetings');
var_dump('meetings status', $meetings->status());

$svc = app(App\Services\ExternalMeetingService::class);
$list = $svc->fetch();
var_dump('service count', $list->count());
foreach ($list->take(3) as $m) {
    var_dump($m->title, $m->date?->toDateTimeString(), $m->recurring_type, $m->recurring_day);
}
