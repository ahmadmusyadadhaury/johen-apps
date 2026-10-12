<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('attendance:push')->everyMinute()->withoutOverlapping();
Schedule::command('script-video:send-reminders')->dailyAt('09:00')->withoutOverlapping();

if ((bool) config('filevault.auto_restore', true)) {
    Schedule::command('files:restore --quiet')->hourly()->withoutOverlapping();
}
