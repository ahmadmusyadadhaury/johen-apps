<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItProject extends Model
{
    protected $fillable = ['nama', 'deadline', 'status', 'created_by'];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
