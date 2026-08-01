<?php

namespace App\Livewire;

use App\Models\ActivityCompetitor;
use App\Models\Employee;
use App\Models\Position;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityCompetitorTable extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $showFeedbackModal = false;
    public bool $showDetail = false;
    public bool $showSuccess = false;
    public ?int $editId = null;
    public ?int $feedbackId = null;
    public ?int $detailId = null;

    public string $jenis = '';
    public string $tanggal_analysis = '';
    public string $nama_competitor = '';
    public string $platform = '';
    public string $kategori_produk = '';
    public string $link = '';
    public string $strength = '';
    public string $weakness = '';
    public string $opportunity = '';
    public string $threat = '';
    public string $kesimpulan = '';
    public string $feedback_atasan = '';

    public function openSwot(): void
    {
        $this->resetInput();
        $this->jenis = 'swot';
        $this->showModal = true;
    }

    public function openMonitoring(): void
    {
        $this->resetInput();
        $this->jenis = 'monitoring';
        $this->showModal = true;
    }

    public function openFeedback(int $id): void
    {
        $item = ActivityCompetitor::findOrFail($id);
        $this->feedbackId = $item->id;
        $this->feedback_atasan = $item->feedback_atasan ?? '';
        $this->showFeedbackModal = true;
    }

    public function saveFeedback(): void
    {
        $this->validate(['feedback_atasan' => 'required']);

        ActivityCompetitor::where('id', $this->feedbackId)->update([
            'feedback_atasan' => $this->feedback_atasan,
        ]);

        $this->resetInput();
        $this->showFeedbackModal = false;
        $this->dispatch('report-feedback-updated');
        $this->showSuccess = true;
    }

    public function save(): void
    {
        $rules = [
            'jenis' => 'required|in:swot,monitoring',
        ];

        if ($this->jenis === 'swot') {
            $rules['tanggal_analysis'] = 'required|date';
            $rules['nama_competitor'] = 'required|string|max:255';
            $rules['platform'] = 'required|in:instagram,tiktok';
            $rules['kategori_produk'] = 'required|in:top_up_game,jual_beli_akun_game';
            $rules['strength'] = 'required|string';
            $rules['weakness'] = 'required|string';
            $rules['opportunity'] = 'required|string';
            $rules['threat'] = 'required|string';
            $rules['kesimpulan'] = 'required|string';
        }

        $this->validate($rules);

        $employee = auth()->user()->employee;
        if (!$employee) return;

        $data = [
            'employee_id' => $employee->id,
            'jenis' => $this->jenis,
        ];

        if ($this->jenis === 'swot') {
            $data['tanggal_analysis'] = $this->tanggal_analysis;
            $data['nama_competitor'] = $this->nama_competitor;
            $data['platform'] = $this->platform;
            $data['kategori_produk'] = $this->kategori_produk;
            $data['link'] = $this->link ?: null;
            $data['strength'] = $this->strength;
            $data['weakness'] = $this->weakness;
            $data['opportunity'] = $this->opportunity;
            $data['threat'] = $this->threat;
            $data['kesimpulan'] = $this->kesimpulan;
        }

        ActivityCompetitor::create($data);

        $this->resetInput();
        $this->showModal = false;
        $this->dispatch('report-feedback-updated');
        session()->flash('message', 'Activity Competitor berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        $item = ActivityCompetitor::findOrFail($id);
        $employee = auth()->user()->employee;
        if ($employee && $item->employee_id === $employee->id) {
            $item->delete();
            session()->flash('message', 'Data berhasil dihapus.');
        }
    }

    public function viewDetail(int $id): void
    {
        $this->detailId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->detailId = null;
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->showFeedbackModal = false;
        $this->showSuccess = false;
        $this->resetInput();
    }

    private function resetInput(): void
    {
        $this->editId = null;
        $this->feedbackId = null;
        $this->jenis = '';
        $this->tanggal_analysis = '';
        $this->nama_competitor = '';
        $this->platform = '';
        $this->kategori_produk = '';
        $this->link = '';
        $this->strength = '';
        $this->weakness = '';
        $this->opportunity = '';
        $this->threat = '';
        $this->kesimpulan = '';
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
        $items = collect();
        $canGiveFeedbackMap = [];

        if ($employee) {
            $query = ActivityCompetitor::with('employee');
            $currentEmployeeId = $employee->id;
            $visibleIds = [$currentEmployeeId];

            if ($user->isManager() || $user->isKoordinatorCreative() || $user->isKoordinatorIt() || $user->isKoordinatorAdmin() || $user->isKoordinatorPubg() || $user->isKoordinatorFf()) {
                $subordinateIds = $this->getSubordinateIds($employee);
                $visibleIds = array_merge($visibleIds, $subordinateIds);
            }

            $query->whereIn('employee_id', $visibleIds);
            $items = $query->latest()->paginate(10);

            foreach ($items as $item) {
                $re = $item->employee;
                if ($re && $re->id !== $currentEmployeeId) {
                    $atasan = $this->getAtasan($re);
                    if ($atasan && $atasan->id === $currentEmployeeId) {
                        $canGiveFeedbackMap[$re->id] = true;
                    }
                }
            }
        }

        return view('livewire.activity-competitor-table', [
            'items' => $items,
            'canGiveFeedbackMap' => $canGiveFeedbackMap,
        ]);
    }
}
