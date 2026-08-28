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

    private function isUnderCoordinatorPosition(Employee $employee): bool
    {
        $positions = $employee->positions;
        foreach ($positions as $position) {
            $current = $position;
            while ($current && $current->parent_id) {
                $current = Position::find($current->parent_id);
                if ($current && str_contains(strtolower($current->nama), 'koordinator')) {
                    return true;
                }
            }
        }
        return false;
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
                $allEmployees = Employee::with('users', 'positions')
                    ->whereIn('id', $subordinateIds)
                    ->get();

                $filtered = $allEmployees->filter(function ($e) {
                    return $this->isUnderCoordinatorPosition($e);
                })->values();

                if ($user->isHeadOfStore1()) {
                    $hos1Coordinators = $filtered;
                } elseif ($user->isHeadOfStore2()) {
                    $hos2Coordinators = $filtered;
                } else {
                    $generalCoordinators = $filtered;
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
