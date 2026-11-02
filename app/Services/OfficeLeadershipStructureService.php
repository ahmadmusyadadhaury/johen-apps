<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Office;
use App\Models\Position;
use App\Models\Scopes\OfficeScope;
use Illuminate\Support\Facades\DB;

class OfficeLeadershipStructureService
{
    /**
     * Memasang data pimpinan yang sama pada struktur setiap kantor tanpa
     * menduplikasi data induk pegawai.
     */
    public function sync(): int
    {
        $leaders = Employee::query()
            ->withoutGlobalScope(OfficeScope::class)
            ->whereIn('position', [
                'CEO',
                'General Manager',
                'Head of Store 1',
                'Head of Store 2',
                'Asistant Manager',
                'Assistant Manager',
            ])
            ->get()
            ->keyBy('position');

        if ($leaders->isEmpty()) {
            return 0;
        }

        $synced = 0;

        DB::transaction(function () use ($leaders, &$synced): void {
            foreach (Office::query()->where('is_active', true)->get() as $office) {
                $ceo = $this->position($office, 'CEO');
                $gm = $this->position($office, 'General Manager', $ceo->id);
                $headOfStore1 = $this->position($office, 'Head of Store 1', $gm->id);
                $headOfStore2 = $this->position($office, 'Head of Store 2', $gm->id);
                $assistantManager = $this->position($office, 'Asistant Manager', $gm->id);

                $this->attach($ceo, $leaders->get('CEO'));
                $this->attach($gm, $leaders->get('General Manager'));
                $this->attach($headOfStore1, $leaders->get('Head of Store 1'));
                $this->attach($headOfStore2, $leaders->get('Head of Store 2'));
                $this->attach(
                    $assistantManager,
                    $leaders->get('Asistant Manager') ?? $leaders->get('Assistant Manager'),
                );

                $synced++;
            }
        });

        return $synced;
    }

    private function position(Office $office, string $name, ?int $parentId = null): Position
    {
        $position = Position::query()
            ->withoutGlobalScope(OfficeScope::class)
            ->firstOrNew([
                'office_id' => $office->id,
                'nama' => $name,
            ]);

        $position->fill([
            'parent_id' => $parentId,
            'is_active' => true,
        ]);
        $position->save();

        return $position;
    }

    private function attach(Position $position, ?Employee $employee): void
    {
        if (! $employee) {
            return;
        }

        $position->employees()->syncWithoutDetaching([
            $employee->id => ['is_main' => true],
        ]);
    }
}
