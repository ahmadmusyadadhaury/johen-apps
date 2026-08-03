<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Position;
use App\Models\WeeklyPlanReport;
use Livewire\Component;
use Livewire\WithPagination;

class WeeklyPlanReportTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $showW1Modal = false;
    public bool $showFeedbackModal = false;
    public bool $showSuccess = false;
    public ?int $editId = null;
    public ?int $w1Id = null;
    public ?int $feedbackId = null;

    public string $tanggal = '';
    public string $kategori = '';
    public string $plan_activity = '';
    public string $objective = '';
    public string $keterangan = '';
    public string $action_plan = '';
    public string $feedback_atasan = '';

    public function openNew(): void
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function openW1(int $id): void
    {
        $report = WeeklyPlanReport::findOrFail($id);
        $this->w1Id = $report->id;
        $this->tanggal = $report->tanggal->format('Y-m-d');
        $this->kategori = $report->kategori;
        $this->plan_activity = $report->plan_activity;
        $this->objective = $report->objective;
        $this->keterangan = $report->keterangan ?? '';
        $this->action_plan = $report->action_plan ?? '';
        $this->showW1Modal = true;
    }

    public function openFeedback(int $id): void
    {
        $report = WeeklyPlanReport::findOrFail($id);
        $this->feedbackId = $report->id;
        $this->feedback_atasan = $report->feedback_atasan ?? '';
        $this->showFeedbackModal = true;
    }

    public function saveW1(): void
    {
        $this->validate([
            'keterangan' => 'required',
            'action_plan' => 'required',
        ]);

        WeeklyPlanReport::where('id', $this->w1Id)->update([
            'keterangan' => $this->keterangan,
            'action_plan' => $this->action_plan,
        ]);

        $this->resetInput();
        $this->showW1Modal = false;
        session()->flash('message', 'W+1 berhasil diisi.');
    }

    public function saveFeedback(): void
    {
        $this->validate([
            'feedback_atasan' => 'required',
        ]);

        WeeklyPlanReport::where('id', $this->feedbackId)->update([
            'feedback_atasan' => $this->feedback_atasan,
        ]);

        $this->resetInput();
        $this->showFeedbackModal = false;
        $this->dispatch('report-feedback-updated');
        $this->showSuccess = true;
    }

    public function save(): void
    {
        $this->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required',
            'plan_activity' => 'required',
            'objective' => 'required',
        ]);

        $employee = auth()->user()->employee;
        if (!$employee) return;

        WeeklyPlanReport::create([
            'employee_id' => $employee->id,
            'tanggal' => $this->tanggal,
            'kategori' => $this->kategori,
            'plan_activity' => $this->plan_activity,
            'objective' => $this->objective,
        ]);

        $this->resetInput();
        $this->showModal = false;
        $this->dispatch('report-feedback-updated');
        session()->flash('message', 'WPR berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        $report = WeeklyPlanReport::findOrFail($id);
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) return;

        $isOwn = $report->employee_id === $employee->id;
        $isKoordinator = $user->isKoordinator();

        if ($isOwn || $isKoordinator) {
            $report->delete();
            session()->flash('message', 'WPR berhasil dihapus.');
        }
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->showW1Modal = false;
        $this->showFeedbackModal = false;
        $this->showSuccess = false;
        $this->resetInput();
    }

    private function resetInput(): void
    {
        $this->editId = null;
        $this->w1Id = null;
        $this->feedbackId = null;
        $this->tanggal = '';
        $this->kategori = '';
        $this->plan_activity = '';
        $this->objective = '';
        $this->keterangan = '';
        $this->action_plan = '';
        $this->feedback_atasan = '';
    }

    private function getAtasan(Employee $employee): ?Employee
    {
        $position = $employee->mainPosition();
        if (!$position || !$position->parent_id) return null;

        $current = $position->parent;
        while ($current) {
            $atasan = $current->employees()->first();
            if ($atasan) return $atasan;
            $current = $current->parent;
        }

        return null;
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

    private function getSubordinateIds(Employee $employee): array
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

    public function render()
    {
        $user = auth()->user();
        $employee = $user->employee;
        $reports = collect();
        $userEmployee = $employee;
        $canGiveFeedbackMap = [];
        $hideCreateButton = false;

        if ($employee) {
            $query = WeeklyPlanReport::with('employee');
            $currentEmployeeId = $employee->id;
            $visibleIds = [$currentEmployeeId];

            if ($user->isKoordinator()) {
                $divisionIds = $employee->divisions()->pluck('divisions.id')->toArray();
                $teamIds = Employee::whereHas('divisions', fn($q) => $q->whereIn('divisions.id', $divisionIds))
                    ->where('id', '!=', $employee->id)
                    ->pluck('id')
                    ->toArray();
                $visibleIds = array_merge($visibleIds, $teamIds);
            } elseif ($user->isManager()) {
                $subordinateIds = $this->getSubordinateIds($employee);
                $visibleIds = array_merge($visibleIds, $subordinateIds);
                $hideCreateButton = true;
            } elseif ($user->isKoordinatorCreative() || $user->isKoordinatorIt() || $user->isKoordinatorAdmin() || $user->isKoordinatorPubg() || $user->isKoordinatorFf() || $user->isKoordinatorRoblox() || $user->isKoordinatorMonkeyPubg()) {
                $subordinateIds = $this->getSubordinateIds($employee);
                $visibleIds = array_merge($visibleIds, $subordinateIds);
            }

            $query->whereIn('employee_id', $visibleIds);
            $reports = $query->latest('tanggal')->paginate(10);

            foreach ($reports as $r) {
                $re = $r->employee;
                if ($re && $re->id !== $currentEmployeeId) {
                    $atasan = $this->getAtasan($re);
                    if ($atasan && $atasan->id === $currentEmployeeId) {
                        $canGiveFeedbackMap[$re->id] = true;
                    }
                }
            }
        }

        return view('livewire.weekly-plan-report-table', [
            'reports' => $reports,
            'canGiveFeedbackMap' => $canGiveFeedbackMap,
            'userEmployee' => $userEmployee,
            'hideCreateButton' => $hideCreateButton,
        ]);
    }
}
