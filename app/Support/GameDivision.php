<?php

namespace App\Support;

use App\Models\BonusPubg;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class GameDivision
{
    public const NAMES = [
        'PUBG',
        'Free Fire',
        'MLBB',
        'FC Mobile',
        'E-football',
        'Valorant',
        'Roblox',
        'Monkey PUBG',
    ];

    public const PARAM_MAP = [
        'fc_mobile' => 'FC Mobile',
        'mlbb' => 'MLBB',
        'pubg' => 'PUBG',
        'ff' => 'Free Fire',
        'efootball' => 'E-football',
        'valorant' => 'Valorant',
        'roblox' => 'Roblox',
        'monkey_pubg' => 'Monkey PUBG',
    ];

    public const DIVISION_NAME = [
        'PUBG' => 'Johen PUBG',
        'Free Fire' => 'Free Fire',
        'MLBB' => 'Mobile Legend',
        'FC Mobile' => 'FC Mobile',
        'E-football' => 'E-Football',
        'Valorant' => 'Valorant',
        'Roblox' => 'Roblox',
        'Monkey PUBG' => 'Monkey PUBG',
    ];

    public const ROOT_POSITION = [
        'FC Mobile' => 'Koordinator FC Mobile',
        'MLBB' => 'Koordinator MLBB',
        'PUBG' => 'Koordinator Johen PUBG',
        'Free Fire' => 'Koordinator Free Fire',
        'E-football' => 'Koordinator E-football',
        'Valorant' => 'Koordinator Valorant',
        'Roblox' => 'Koordinator Roblox',
        'Monkey PUBG' => 'Koordinator Monkey PUBG',
    ];

    public const HOST_POSITION = [
        'PUBG' => ['Host Johen PUBG', 'Host PUBG'],
        'Free Fire' => ['Host Free Fire', 'Host FF'],
        'MLBB' => ['Host MLBB', 'Host Mobile Legend', 'Host Mobile Legends'],
        'FC Mobile' => ['Host FC Mobile', 'Host FCMobile'],
        'E-football' => ['Host E-football', 'Host E-Football', 'Host Efootball'],
        'Valorant' => ['Host Valorant'],
        'Roblox' => ['Host Roblox'],
        'Monkey PUBG' => ['Host Monkey PUBG', 'Host MonkeyPUBG'],
    ];

    public static function canonical(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') return '';

        return self::PARAM_MAP[$value] ?? $value;
    }

    public static function isKnown(?string $value): bool
    {
        return in_array(self::canonical($value), self::NAMES, true);
    }

    public static function division(string $divisi): ?Division
    {
        $name = self::DIVISION_NAME[$divisi] ?? null;
        if (!$name) return null;

        return Division::whereRaw('LOWER(nama) = ?', [mb_strtolower($name)])->first();
    }

    public static function rootPosition(string $divisi): ?Position
    {
        $rootName = self::ROOT_POSITION[$divisi] ?? null;

        if ($rootName) {
            $root = Position::where('nama', $rootName)->first();
            if ($root) return $root;
        }

        $division = self::division($divisi);
        if (!$division) return null;

        return Position::where('division_id', $division->id)
            ->where(function ($q) {
                $q->where('nama', 'like', 'Koordinator%')
                  ->orWhere('nama', 'like', 'Coordinator%');
            })
            ->orderBy('id')
            ->first();
    }

    public static function descendantPositionIds(Position $position): array
    {
        $ids = [];

        foreach (Position::where('parent_id', $position->id)->get() as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, self::descendantPositionIds($child));
        }

        return $ids;
    }

    public static function hostPositionIds(string $divisi): array
    {
        $division = self::division($divisi);
        $ids = $division
            ? Position::where('division_id', $division->id)->where('nama', 'like', 'Host%')->pluck('id')->all()
            : [];

        $root = self::rootPosition($divisi);
        if ($root) {
            $ids = array_merge($ids, Position::whereIn('id', self::descendantPositionIds($root))
                ->where('nama', 'like', 'Host%')
                ->pluck('id')
                ->all());
        }

        return array_values(array_unique($ids));
    }

    public static function employeeIds(string $divisi): array
    {
        $root = self::rootPosition($divisi);
        if (!$root) return [];

        $positionIds = self::descendantPositionIds($root);
        $positionIds[] = $root->id;

        return Employee::whereHas('positions', function ($q) use ($positionIds) {
            $q->whereIn('position_id', $positionIds);
        })->pluck('id')->toArray();
    }

    public static function isKoordinator(string $divisi, ?Employee $employee): bool
    {
        $root = self::rootPosition($divisi);
        if (!$root || !$employee) return false;

        return $employee->positions()->where('position_id', $root->id)->exists();
    }

    public static function isStaffHost(string $divisi, ?Employee $employee): bool
    {
        if (!$employee) return false;

        $hostPositionNames = self::HOST_POSITION[$divisi] ?? [];

        if ($hostPositionNames !== [] && $employee->positions()->where(function ($q) use ($hostPositionNames) {
            foreach ($hostPositionNames as $name) {
                $q->orWhere('nama', 'like', $name . '%');
            }
        })->exists()) {
            return true;
        }

        $hostPositionIds = self::hostPositionIds($divisi);
        if ($hostPositionIds === []) return false;

        return $employee->positions()->whereIn('position_id', $hostPositionIds)->exists();
    }

    public static function pendingQuery(string $divisi): Builder
    {
        return BonusPubg::where('bonus_pubgs.divisi', $divisi)
            ->where('bonus_pubgs.status', 'pending');
    }

    public static function pendingApprovals(string $divisi, ?Employee $employee): Collection
    {
        if (!self::isKoordinator($divisi, $employee)) return new Collection();

        $employeeIds = self::employeeIds($divisi);
        if ($employeeIds === []) return new Collection();

        return self::pendingQuery($divisi)
            ->whereIn('bonus_pubgs.employee_id', $employeeIds)
            ->with('employee:id,nama')
            ->orderBy('bonus_pubgs.tanggal')
            ->orderBy('bonus_pubgs.id')
            ->get([
                'bonus_pubgs.id',
                'bonus_pubgs.employee_id',
                'bonus_pubgs.nik',
                'bonus_pubgs.nama',
                'bonus_pubgs.divisi',
                'bonus_pubgs.sesi',
                'bonus_pubgs.tanggal',
            ]);
    }

    public static function pendingApprovalCount(string $divisi, ?Employee $employee): int
    {
        if (!self::isKoordinator($divisi, $employee)) return 0;

        $employeeIds = self::employeeIds($divisi);
        if ($employeeIds === []) return 0;

        return self::pendingQuery($divisi)
            ->whereIn('bonus_pubgs.employee_id', $employeeIds)
            ->count();
    }
}
