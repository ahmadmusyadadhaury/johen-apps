<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Office;
use App\Models\Position;
use App\Models\Scopes\OfficeScope;
use App\Services\OfficeLeadershipStructureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OfficeLeadershipStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_leaders_are_shared_in_every_office_structure_without_copying_employees(): void
    {
        $offices = Office::query()->orderBy('id')->get();

        $leaders = collect([
            ['nik' => 'CEO-1', 'nama' => 'CEO Test', 'position' => 'CEO'],
            ['nik' => 'GM-1', 'nama' => 'GM Test', 'position' => 'General Manager'],
            ['nik' => 'HSM-1', 'nama' => 'HSM 1 Test', 'position' => 'Head of Store 1'],
            ['nik' => 'HSM-2', 'nama' => 'HSM 2 Test', 'position' => 'Head of Store 2'],
            ['nik' => 'ASMEN-1', 'nama' => 'Asmen Test', 'position' => 'Asistant Manager'],
        ])->map(fn (array $leader) => Employee::create($leader + [
            'office_id' => $offices->first()->id,
            'tipe' => Employee::TIPE_KARYAWAN_AKTIF,
        ]));

        app(OfficeLeadershipStructureService::class)->sync();

        $this->assertSame(5, Employee::withoutGlobalScope(OfficeScope::class)->count());

        foreach ($offices as $office) {
            $positions = Position::withoutGlobalScope(OfficeScope::class)
                ->where('office_id', $office->id)
                ->whereIn('nama', ['CEO', 'General Manager', 'Head of Store 1', 'Head of Store 2', 'Asistant Manager'])
                ->with('employees')
                ->get()
                ->keyBy('nama');

            $this->assertSame(['CEO', 'General Manager', 'Head of Store 1', 'Head of Store 2', 'Asistant Manager'], $positions->keys()->all());
            $this->assertNull($positions['CEO']->parent_id);
            $this->assertSame($positions['CEO']->id, $positions['General Manager']->parent_id);
            $this->assertSame($positions['General Manager']->id, $positions['Head of Store 1']->parent_id);
            $this->assertSame($positions['General Manager']->id, $positions['Head of Store 2']->parent_id);
            $this->assertSame($positions['General Manager']->id, $positions['Asistant Manager']->parent_id);

            foreach ($positions as $position) {
                $this->assertCount(1, $position->employees);
            }
        }

        $this->assertSame(15, DB::table('employee_position')->count());
    }
}
