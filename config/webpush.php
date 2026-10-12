<?php

return [
    'public_key' => env('SCRIPT_VIDEO_VAPID_PUBLIC_KEY', ''),
    'private_key' => env('SCRIPT_VIDEO_VAPID_PRIVATE_KEY', ''),
    'subject' => env('SCRIPT_VIDEO_VAPID_SUBJECT', 'mailto:admin@example.com'),
];
