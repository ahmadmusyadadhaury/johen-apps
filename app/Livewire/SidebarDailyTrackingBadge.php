<?php

namespace App\Livewire;

use App\Models\BonusPubg;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarDailyTrackingBadge extends Component
{
    private const DIVISION_POSITION_MAP = [
        'PUBG' => 'koordinator johen pubg',
        'Free Fire' => 'koordinator free fire',
        'MLBB' => 'koordinator mlbb',
        'E-football' => 'koordinator e-football',
        'Valorant' => 'koordinator valorant',
        'Roblox' => 'koordinator roblox',
        'Monkey PUBG' => 'koordinator monkey pubg',
        'FC Mobile' => 'koordinator fc mobile',
        'Admin' => 'koordinator admin',
    ];

    public function render()
    {
        $user = auth()->user();
        $count = 0;

        if (!$user) {
            return view('livewire.sidebar-daily-tracking-badge', ['count' => 0]);
        }

        if ($user->isKoordinatorGame()) {
            $employee = $user->employee;
            if ($employee) {
                $teamIds = $this->getKoordinatorTeamIds($employee, $user);
                if (!empty($teamIds)) {
                    $count = BonusPubg::whereIn('employee_id', $teamIds)
                        ->where('status', 'pending')
                        ->count();
                }
            }
        } elseif ($user->isManager()) {
            $employee = $user->employee;
            if ($employee) {
                $subordinateIds = $this->getManagerSubordinateIds($employee);
                if (!empty($subordinateIds)) {
                    $count = BonusPubg::whereIn('employee_id', $subordinateIds)
                        ->where('status', 'disetujui')
                        ->whereNotNull('approved_by')
                        ->whereIn('divisi', $this->getManagerDivisionNames($employee))
                        ->where(function ($q) {
                            $q->whereNull('feedback_atasan')
                              ->orWhere('feedback_atasan', '');
                        })
                        ->count();
                }
            }
        }

        return view('livewire.sidebar-daily-tracking-badge', ['count' => $count]);
    }

    #[On('daily-tracking-updated')]
    public function refresh(): void
    {
        //
    }

    private function getKoordinatorTeamIds(Employee $employee, User $user): array
    {
        $ids = [];

        $mainPosition = $employee->mainPosition();
        if ($mainPosition) {
            $descendantIds = $this->getDescendantPositionIds($mainPosition->id);
            $descendantIds = array_diff($descendantIds, [$mainPosition->id]);
            if (!empty($descendantIds)) {
                $fromPositions = Employee::whereIn('id', function ($q) use ($descendantIds) {
                    $q->select('employee_id')
                      ->from('employee_position')
                      ->whereIn('position_id', $descendantIds)
                      ->where('is_main', true);
                })->pluck('id')->toArray();
                $ids = array_merge($ids, $fromPositions);
            }
        }

        $roleMap = [
            'isKoordinatorPubg' => User::ROLE_STAFF_HOST_PUBG,
            'isKoordinatorFf' => User::ROLE_STAFF_HOST_FF,
            'isKoordinatorMlbb' => User::ROLE_STAFF_HOST_MLBB,
            'isKoordinatorEfootball' => User::ROLE_STAFF_HOST_EFOOTBALL,
            'isKoordinatorValorant' => User::ROLE_STAFF_HOST_VALORANT,
            'isKoordinatorRoblox' => User::ROLE_STAFF_HOST_ROBLOX,
            'isKoordinatorMonkeyPubg' => User::ROLE_STAFF_HOST_MONKEY_PUBG,
        ];

        foreach ($roleMap as $method => $staffRole) {
            if ($user->$method()) {
                $roleIds = Employee::whereHas('users', function ($q) use ($staffRole) {
                    $q->where('role', $staffRole);
                })->pluck('id')->toArray();
                $ids = array_merge($ids, $roleIds);
                break;
            }
        }

        return array_values(array_unique($ids));
    }

    private function getManagerDivisionNames(Employee $employee): array
    {
        $position = $employee->mainPosition();
        if (!$position) return [];

        $descendantIds = $this->getDescendantPositionIds($position->id);
        $names = Position::whereIn('id', $descendantIds)->pluck('nama')
            ->map(fn ($n) => strtolower($n))->toArray();

        $divisions = [];
        foreach (self::DIVISION_POSITION_MAP as $divisi => $posName) {
            foreach ($names as $name) {
                if (str_contains($name, $posName)) {
                    $divisions[] = $divisi;
                    break;
                }
            }
        }

        return array_values(array_unique($divisions));
    }

    private function getManagerSubordinateIds(Employee $employee): array
    {
        $position = $employee->mainPosition();
        if (!$position) return [];

        $descendantIds = $this->getDescendantPositionIds($position->id);
        $descendantIds = array_diff($descendantIds, [$position->id]);

        if (empty($descendantIds)) return [];

        $ids = Employee::whereIn('id', function ($q) use ($descendantIds) {
            $q->select('employee_id')
              ->from('employee_position')
              ->whereIn('position_id', $descendantIds);
        })->pluck('id')->toArray();

        if (str_contains(strtolower($position->nama), 'head of store 2')) {
            $efootball = Position::where('nama', 'Koordinator E-football')->first();
            if ($efootball) {
                $efootballPositionIds = $this->getDescendantPositionIds($efootball->id);
                $efootballIds = Employee::whereHas('positions', function ($q) use ($efootballPositionIds) {
                    $q->whereIn('position_id', $efootballPositionIds);
                })->pluck('id')->toArray();
                $ids = array_diff($ids, $efootballIds);
            }
        }

        return array_values($ids);
    }

    private function getDescendantPositionIds(int $positionId): array
    {
        $ids = [$positionId];
        $children = Position::where('parent_id', $positionId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getDescendantPositionIds($childId));
        }
        return $ids;
    }
}
