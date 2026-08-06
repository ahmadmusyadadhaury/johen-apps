<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'content', 'summary', 'event_date', 'event_time', 'is_published'])]
class Announcement extends Model
{
    public function readByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_user')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}
