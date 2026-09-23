<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WeeklyMeeting;

$meetings = WeeklyMeeting::all();
foreach ($meetings as $meeting) {
    echo $meeting->id . ' - ' . $meeting->title . ' - ' . $meeting->meeting_date->format('Y-m-d') . ' - ' . ($meeting->is_active ? 'active' : 'inactive') . PHP_EOL;
}