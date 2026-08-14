<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

Cache::forget('external_meetings');
Cache::forget('external_meeting_token');

$url = config('services.meeting.url');
$token = config('services.meeting.token');
$path = config('services.meeting.path');

var_dump('url', $url, 'has token', (bool) $token);

$request = Http::timeout(5)->acceptJson();

if (! config('services.meeting.verify_ssl')) {
    $request = $request->withoutVerifying();
}

$meetings = $token
    ? $request->withToken($token)->get($url . $path)
    : $request->post($url . config('services.meeting.login_path'), [
        'username' => config('services.meeting.username'),
        'password' => config('services.meeting.password'),
    ]);

var_dump('api status', $meetings->status());

$svc = app(App\Services\ExternalMeetingService::class);
$list = $svc->fetch(true);
var_dump('service count', $list->count());
foreach ($list as $m) {
    var_dump($m->id, $m->title, $m->date?->toDateString(), $m->start_time, $m->recurring_type, $m->recurring_day);
}
