<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\Position;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SidebarKontrakBadge extends Component
{
    public function render()
    {
        $user = auth()->user();
        $count = 0;

        if ($user) {
            $count = $this->countPendingEvaluations($user);
        }

        return view('livewire.sidebar-kontrak-badge', ['count' => $count]);
    }

    private function countPendingEvaluations($user): int
    {
        $base = EmployeeContract::query()
            ->where('status', 'berlaku')
            ->where('tanggal_berakhir', '>=', now())
            ->where('tanggal_berakhir', '<=', now()->addDays(14));

        if ($user->isAnyKoordinator() || $user->isManager()) {
            $teamEmployeeIds = $this->getScopedTeamEmployeeIds($user);
            if (empty($teamEmployeeIds)) {
                return 0;
            }
            $base->whereIn('employee_id', $teamEmployeeIds);
        } elseif ($user->isSuperAdmin() || $user->isGmCeo()) {
            // super admin & GM CEO sees all
        } else {
            return 0;
        }

        $contractIds = $base->pluck('id');

        if ($contractIds->isEmpty()) {
            return 0;
        }

        $submittedContractIds = DB::table('contract_evaluations')
            ->whereIn('contract_id', $contractIds)
            ->where('evaluator_id', $user->id)
            ->whereNotNull('submitted_at')
            ->pluck('contract_id');

        return $contractIds->diff($submittedContractIds)->count();
    }

    private function getScopedTeamEmployeeIds($user): array
    {
        $positionName = $this->getScopedPositionName($user);
        if (!$positionName) {
            return [];
        }

        $position = Position::where('nama', $positionName)->first();
        if (!$position) {
            return [];
        }

        $descendantIds = $this->getAllDescendantIds($position);
        $descendantIds[] = $position->id;

        $ids = DB::table('employee_position')
            ->whereIn('position_id', $descendantIds)
            ->distinct()
            ->pluck('employee_id')
            ->all();

        // Jangan tampilkan/ hitung kontrak milik user yang sedang login.
        $ownEmployeeId = $user->employee_id;
        if ($ownEmployeeId !== null) {
            $ids = array_values(array_diff($ids, [$ownEmployeeId]));
        }

        return $ids;
    }

    private function getScopedPositionName($user): ?string
    {
        if ($user->isManager()) {
            return $user->employee?->mainPosition()?->nama;
        }

        $mapped = match (true) {
            $user->isKoordinatorPubg() => 'Koordinator Johen PUBG',
            $user->isKoordinatorFf() => 'Koordinator Free Fire',
            $user->isKoordinatorMlbb() => 'Koordinator MLBB',
            $user->isKoordinatorEfootball() => 'Koordinator E-football',
            $user->isKoordinatorValorant() => 'Koordinator Valorant',
            $user->isKoordinatorRoblox() => 'Koordinator Roblox',
            $user->isKoordinatorMonkeyPubg() => 'Koordinator Monkey PUBG',
            $user->isKoordinatorIt() => 'Koordinator IT',
            $user->isKoordinatorCreative() => 'Koordinator Creative',
            $user->isKoordinatorAdmin() => 'Koordinator Admin',
            $user->isKoordinatorStock() => 'Koordinator Stock',
            $user->isKoordinatorFcMobile() => 'Koordinator FC Mobile',
            default => null,
        };

        if ($mapped) {
            return $mapped;
        }

        if ($user->isKoordinator()) {
            $position = $user->employee?->mainPosition()?->nama;
            if ($position && str_starts_with(strtolower($position), 'koordinator')) {
                return $position;
            }
        }

        return null;
    }

    private function getAllDescendantIds(Position $position): array
    {
        $ids = [];
        foreach ($position->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllDescendantIds($child));
        }
        return $ids;
    }
}
