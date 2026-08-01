<?php

namespace App\Livewire;

use App\Models\ActivityCompetitor;
use App\Models\Employee;
use App\Models\Position;
use App\Models\WeeklyPlanReport;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarFeedbackBadge extends Component
{
    public string $type = 'weekly';

    public function render()
    {
        $user = auth()->user();
        $count = 0;

        if ($user && $user->isManager() && $user->employee) {
            $subordinateIds = $this->getManagerSubordinateIds($user->employee);
            if (!empty($subordinateIds)) {
                $query = $this->type === 'activity'
                    ? ActivityCompetitor::query()
                    : WeeklyPlanReport::query();

                $count = $query->whereIn('employee_id', $subordinateIds)
                    ->where(function ($q) {
                        $q->whereNull('feedback_atasan')
                          ->orWhere('feedback_atasan', '');
                    })
                    ->count();
            }
        }

        return view('livewire.sidebar-feedback-badge', ['count' => $count]);
    }

    #[On('report-feedback-updated')]
    public function refresh(): void
    {
        //
    }

    private function getManagerSubordinateIds(Employee $employee): array
    {
        $position = $employee->mainPosition();
        if (!$position) return [];

        $descendantIds = $this->getDescendantPositionIds($position->id);
        $descendantIds = array_diff($descendantIds, [$position->id]);

        if (empty($descendantIds)) return [];

        return Employee::whereIn('id', function ($q) use ($descendantIds) {
            $q->select('employee_id')
              ->from('employee_position')
              ->whereIn('position_id', $descendantIds);
        })->pluck('id')->toArray();
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
