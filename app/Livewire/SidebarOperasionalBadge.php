<?php

namespace App\Livewire;

use App\Models\ActivityCompetitor;
use App\Models\BonusPubg;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\User;
use App\Models\WeeklyPlanReport;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarOperasionalBadge extends Component
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
        $total = 0;

        if (!$user) {
            return view('livewire.sidebar-operasional-badge', ['total' => 0]);
        }

        $total += $this->countLeaveRequests($user);
        $total += $this->countReportsAwaitingFeedback($user);
        $total += $this->countDailyTrackingPending($user);

        return view('livewire.sidebar-operasional-badge', ['total' => $total]);
    }

    #[On('leave-request-updated')]
    #[On('report-feedback-updated')]
    #[On('daily-tracking-updated')]
    public function refresh(): void
    {
        //
    }

    private function countLeaveRequests(User $user): int
    {
        if ($user->isSuperAdmin() || $user->isGmCeo()) {
            return LeaveRequest::where('persetujuan_hr', 'menunggu')->count();
        }

        if ($user->employee) {
            $employeeId = $user->employee->id;
            return LeaveRequest::where(function ($q) use ($employeeId) {
                $q->where('atasan_id', $employeeId)
                  ->where('persetujuan_koor', 'menunggu');
            })->orWhere(function ($q) use ($employeeId) {
                $q->where('atasan2_id', $employeeId)
                  ->where('persetujuan_atasan2', 'menunggu');
            })->count();
        }

        return 0;
    }

    private function countReportsAwaitingFeedback(User $user): int
    {
        if (!$user->isManager() || !$user->employee) {
            return 0;
        }

        $subordinateIds = $this->getManagerSubordinateIds($user->employee);
        if (empty($subordinateIds)) {
            return 0;
        }

        $feedbackQuery = function ($q) {
            $q->whereNull('feedback_atasan')
              ->orWhere('feedback_atasan', '');
        };

        return WeeklyPlanReport::whereIn('employee_id', $subordinateIds)
                ->where($feedbackQuery)
                ->count()
            + ActivityCompetitor::whereIn('employee_id', $subordinateIds)
                ->where($feedbackQuery)
                ->count();
    }

    private function countDailyTrackingPending(User $user): int
    {
        if (!$user->isManager() || !$user->employee) {
            return 0;
        }

        $subordinateIds = $this->getManagerSubordinateIds($user->employee);
        if (empty($subordinateIds)) {
            return 0;
        }

        return BonusPubg::whereIn('employee_id', $subordinateIds)
            ->where('status', 'disetujui')
            ->whereNotNull('approved_by')
            ->whereIn('divisi', $this->getManagerDivisionNames($user->employee))
            ->where(function ($q) {
                $q->whereNull('feedback_atasan')
                  ->orWhere('feedback_atasan', '');
            })
            ->count();
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
