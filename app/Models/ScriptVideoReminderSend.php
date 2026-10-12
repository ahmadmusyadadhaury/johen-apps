<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScriptVideoReminderSend extends Model
{
    protected $fillable = ['user_id', 'sent_on'];

    protected function casts(): array
    {
        return ['sent_on' => 'date'];
    }
}
