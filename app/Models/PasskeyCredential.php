<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasskeyCredential extends Model
{
    protected $fillable = ['user_id', 'name', 'credential_record'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
