<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerMonitoring extends Model
{
    protected $fillable = [
        'influencer_id',
        'period_month',
        'followers',
        'viewers_last_month',
        'duration_hours',
        'target_duration_hours',
        'notes',
        'benefits',
    ];

    protected function casts(): array
    {
        return [
            'period_month' => 'date',
            'followers' => 'integer',
            'viewers_last_month' => 'integer',
            'duration_hours' => 'decimal:2',
            'target_duration_hours' => 'decimal:2',
        ];
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }
}
