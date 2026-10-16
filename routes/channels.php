<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('it-ticket.user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('weekly-meeting.{meetingId}', function ($user, $meetingId) {
    // Only admin_master can listen to attendance updates
    return $user->isAdminMaster();
});

Broadcast::channel('weekly-meeting.admin', function ($user) {
    // Only admin_master can listen to admin channel
    return $user->isAdminMaster();
});
