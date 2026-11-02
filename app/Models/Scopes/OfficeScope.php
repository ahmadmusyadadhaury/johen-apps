<?php

namespace App\Models\Scopes;

use App\Models\Office;
use App\Support\OfficeContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OfficeScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (! $user || ! app()->bound(OfficeContext::class)) {
            return;
        }

        $officeContext = app(OfficeContext::class);
        $officeContext->resolve($user);

        if ($officeContext->office()) {
            $builder->where(
                $model->qualifyColumn('office_id'),
                $officeContext->office()->getKey(),
            );

            return;
        }

        if (! $officeContext->isAllOffices()) {
            $summareconId = Office::query()
                ->where('code', Office::CODE_SUMMARECON_BANDUNG)
                ->value('id');

            if ($summareconId) {
                $builder->where($model->qualifyColumn('office_id'), $summareconId);
            } else {
                $builder->whereRaw('1 = 0');
            }
        }
    }
}
