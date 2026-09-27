<?php

namespace App\Livewire;

use App\Models\ActivityCompetitor;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class ActivityCompetitorCoordinatorList extends Component
{
    #[On('report-feedback-updated')]
    public function refresh(): void
    {
        //
    }

    private function descendantPositionIds(int $positionId): array
    {
        $ids = [$positionId];
        foreach (Position::where('parent_id', $positionId)->pluck('id') as $childId) {
            $ids = array_merge($ids, $this->descendantPositionIds($childId));
        }

        return $ids;
    }

    private function headOfStoreGroup(Employee $employee): ?string
    {
        $position = $employee->positions->first(fn (Position $position) => (bool) $position->pivot?->is_main);

        while ($position) {
            $name = strtolower($position->nama);
            if ($name === 'head of store 1') return 'hos1';
            if ($name === 'head of store 2') return 'hos2';
            $position = $position->parent_id ? Position::find($position->parent_id) : null;
        }

        return null;
    }

    public function render()
    {
        $user = auth()->user();
        $employee = $user->employee;
        $groups = ['hos1' => collect(), 'hos2' => collect(), 'general' => collect()];

        if ($employee && $user->isManager()) {
            $position = $employee->mainPosition();
            if ($position) {
                $descendantIds = array_diff($this->descendantPositionIds($position->id), [$position->id]);
                $employeeIds = Employee::whereIn('id', function ($query) use ($descendantIds) {
                    $query->select('employee_id')->from('employee_position')->whereIn('position_id', $descendantIds);
                })->pluck('id');

                if (str_contains(strtolower($position->nama), 'head of store 2')) {
                    $efootball = Position::where('nama', 'Koordinator E-football')->first();
                    if ($efootball) {
                        $efootballIds = Employee::whereHas('positions', fn ($query) => $query->whereIn(
                            'position_id',
                            $this->descendantPositionIds($efootball->id)
                        ))->pluck('id');
                        $employeeIds = $employeeIds->diff($efootballIds);
                    }
                }

                $coordinators = Employee::with(['positions', 'users'])
                    ->whereIn('id', $employeeIds)
                    ->get()
                    ->filter(function (Employee $candidate) {
                        return $candidate->positions->contains(fn (Position $position) =>
                            (bool) $position->pivot?->is_main && str_contains(strtolower($position->nama), 'koordinator')
                        ) || $candidate->users->contains(fn (User $candidateUser) => $candidateUser->isKoordinatorCreative());
                    });

                foreach ($coordinators as $coordinator) {
                    $group = $this->headOfStoreGroup($coordinator);
                    if ($user->isHeadOfStore1() && $group !== 'hos1') continue;
                    if ($user->isHeadOfStore2() && $group !== 'hos2') continue;
                    $groups[$group ?? 'general']->push($coordinator);
                }
            }
        }

        $coordinatorIds = collect($groups)->flatten()->pluck('id');

        $counts = ActivityCompetitor::selectRaw('employee_id, COUNT(*) as total')
            ->whereIn('employee_id', $coordinatorIds)
            ->groupBy('employee_id')
            ->pluck('total', 'employee_id');

        $pendingCounts = ActivityCompetitor::selectRaw('employee_id, COUNT(*) as total')
            ->whereIn('employee_id', $coordinatorIds)
            ->where(function ($query) {
                $query->whereNull('feedback_atasan')
                    ->orWhere('feedback_atasan', '');
            })
            ->groupBy('employee_id')
            ->pluck('total', 'employee_id');

        return view('livewire.activity-competitor-coordinator-list', [
            'hos1Coordinators' => $groups['hos1'],
            'hos2Coordinators' => $groups['hos2'],
            'generalCoordinators' => $groups['general'],
            'activityCounts' => $counts,
            'pendingCounts' => $pendingCounts,
        ]);
    }
}
