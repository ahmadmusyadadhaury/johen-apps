<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFaceTemplate extends Model
{
    protected $fillable = ['user_id', 'encrypted_template', 'model_version'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
