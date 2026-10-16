<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItTicketPushSubscription extends Model
{
    protected $fillable = ['user_id', 'endpoint', 'endpoint_hash', 'public_key', 'auth_token'];
}
