<?php

namespace App\Models\Concerns;

use App\Models\Office;
use App\Models\Scopes\OfficeScope;
use App\Support\OfficeContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasOfficeOwnership
{
    public static function bootHasOfficeOwnership(): void
    {
        static::addGlobalScope(new OfficeScope);

        static::creating(function ($model): void {
            if ($model->office_id) {
                return;
            }

            $officeId = null;
            $user = auth()->user();

            if ($user && app()->bound(OfficeContext::class)) {
                $officeContext = app(OfficeContext::class);
                $officeContext->resolve($user);
                $officeId = $officeContext->office()?->getKey();
            }

            $model->office_id = $officeId ?: Office::query()
                ->where('code', Office::CODE_SUMMARECON_BANDUNG)
                ->value('id');
        });
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}
