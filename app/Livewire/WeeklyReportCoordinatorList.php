<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Position;
use Livewire\Component;

class WeeklyReportCoordinatorList extends Component
{
    private function getDescendantPositionIds(int $positionId): array
    {
        $ids = [$positionId];
        $children = Position::where('parent_id', $positionId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getDescendantPositionIds($childId));
        }
        return $ids;
    }

    private function getSubordinateIds(Employee $employee): array
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

    private function isCoordinatorEmployee(Employee $employee): bool
    {
        return $employee->positions->contains(function (Position $position) {
            return (bool) $position->pivot?->is_main
                && str_contains(strtolower($position->nama), 'koordinator');
        });
    }

    private function getHeadOfStoreGroup(Employee $employee): ?string
    {
        $position = $employee->positions->first(fn (Position $position) => (bool) $position->pivot?->is_main);

        while ($position) {
            $name = strtolower($position->nama);
            if ($name === 'head of store 1') {
                return 'hos1';
            }
            if ($name === 'head of store 2') {
                return 'hos2';
            }

            $position = $position->parent_id ? Position::find($position->parent_id) : null;
        }

        return null;
    }

    public function render()
    {
        $user = auth()->user();
        $employee = $user->employee;
        $hos1Coordinators = collect();
        $hos2Coordinators = collect();
        $generalCoordinators = collect();

        if ($employee && $user->isManager()) {
            $subordinateIds = $this->getSubordinateIds($employee);

            if (!empty($subordinateIds)) {
                $allEmployees = Employee::with('positions')
                    ->whereIn('id', $subordinateIds)
                    ->get();

                $filtered = $allEmployees->filter(function (Employee $employee) {
                    return $this->isCoordinatorEmployee($employee);
                })->values();

                foreach ($filtered as $coordinator) {
                    $group = $this->getHeadOfStoreGroup($coordinator);

                    if ($user->isHeadOfStore1() && $group !== 'hos1') {
                        continue;
                    }
                    if ($user->isHeadOfStore2() && $group !== 'hos2') {
                        continue;
                    }

                    match ($group) {
                        'hos1' => $hos1Coordinators->push($coordinator),
                        'hos2' => $hos2Coordinators->push($coordinator),
                        default => $generalCoordinators->push($coordinator),
                    };
                }
            }
        }

        return view('livewire.weekly-report-coordinator-list', [
            'hos1Coordinators' => $hos1Coordinators,
            'hos2Coordinators' => $hos2Coordinators,
            'generalCoordinators' => $generalCoordinators,
        ]);
    }
}
