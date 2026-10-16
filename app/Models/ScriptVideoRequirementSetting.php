<?php

namespace App\Models;

use App\Support\GameDivision;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ScriptVideoRequirementSetting extends Model
{
    protected $fillable = ['required_user_ids'];

    protected function casts(): array
    {
        return ['required_user_ids' => 'array'];
    }

    public static function eligibleCoordinators(): Builder
    {
        return User::query()->where(function ($query) {
            $query->whereIn('role', [
                User::ROLE_KOORDINATOR_PUBG,
                User::ROLE_KOORDINATOR_FF,
                User::ROLE_KOORDINATOR_MLBB,
                User::ROLE_KOORDINATOR_EFOOTBALL,
                User::ROLE_KOORDINATOR_VALORANT,
                User::ROLE_KOORDINATOR_ROBLOX,
                User::ROLE_KOORDINATOR_MONKEY_PUBG,
            ])->orWhereHas('employee.positions', function ($positions) {
                // Some game coordinators use the generic `koordinator` role and
                // their position titles vary between installations. Resolve them
                // from their game division and coordinator position instead of
                // requiring an exact title match.
                $positions->where(function ($titles) {
                    $titles->where('nama', 'like', 'Koordinator%')
                        ->orWhere('nama', 'like', 'Coordinator%');
                })->whereHas('division', function ($division) {
                    $division->whereIn('nama', array_values(GameDivision::DIVISION_NAME));
                });
            });
        });
    }

    public static function selectedUserIds(): ?array
    {
        $setting = static::query()->first();

        return $setting?->required_user_ids;
    }

    public static function requiredUsers()
    {
        $eligible = static::eligibleCoordinators();
        $selectedIds = static::selectedUserIds();

        if ($selectedIds !== null) {
            $eligible->whereIn('id', $selectedIds);
        }

        return $eligible->get();
    }

    public static function saveSelectedUserIds(array $ids): void
    {
        static::query()->updateOrCreate(
            ['id' => 1],
            ['required_user_ids' => array_values(array_unique(array_map('intval', $ids)))],
        );
    }
}
