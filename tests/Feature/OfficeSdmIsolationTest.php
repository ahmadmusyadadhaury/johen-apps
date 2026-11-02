<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Position;
use App\Models\User;
use App\Support\OfficeContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficeSdmIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_summarecon_data_is_hidden_when_baleendah_is_active(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $summarecon = Office::where('code', Office::CODE_SUMMARECON_BANDUNG)->firstOrFail();
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();

        Employee::withoutGlobalScopes()->create([
            'office_id' => $summarecon->id,
            'nik' => 'SUM-001',
            'nama' => 'Pegawai Summarecon',
            'tipe' => Employee::TIPE_KARYAWAN_AKTIF,
        ]);
        Division::withoutGlobalScopes()->create([
            'office_id' => $summarecon->id,
            'nama' => 'Divisi Summarecon',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->withSession([OfficeContext::SESSION_KEY => (string) $baleendah->id]);

        $this->assertSame(0, Employee::count());
        $this->assertSame(0, Division::count());
        $this->assertSame(0, Position::count());
    }

    public function test_all_offices_context_can_see_summarecon_data(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $summarecon = Office::where('code', Office::CODE_SUMMARECON_BANDUNG)->firstOrFail();

        Division::withoutGlobalScopes()->create([
            'office_id' => $summarecon->id,
            'nama' => 'Divisi Summarecon',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->withSession([OfficeContext::SESSION_KEY => OfficeContext::ALL_OFFICES]);

        $this->assertSame(1, Division::count());
    }

    public function test_new_division_is_owned_by_the_active_office(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $streamer = Office::where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();

        $this->actingAs($superAdmin)
            ->withSession([OfficeContext::SESSION_KEY => (string) $streamer->id]);

        $division = Division::create([
            'nama' => 'Operasional Streamer',
            'is_active' => true,
        ]);

        $this->assertSame($streamer->id, $division->office_id);
    }

    public function test_empty_office_dashboard_does_not_render_summarecon_statistics(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();

        $this->actingAs($superAdmin)
            ->withSession([OfficeContext::SESSION_KEY => (string) $baleendah->id])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kantor Baleendah masih kosong')
            ->assertSee('Data Kantor Summarecon Bandung tidak ditampilkan')
            ->assertDontSee('Ringkasan seluruh data dan aktivitas');
    }

    public function test_summarecon_employee_and_division_are_not_rendered_on_baleendah_sdm_pages(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $summarecon = Office::where('code', Office::CODE_SUMMARECON_BANDUNG)->firstOrFail();
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();

        Employee::withoutGlobalScopes()->create([
            'office_id' => $summarecon->id,
            'nik' => 'SUM-UI-001',
            'nama' => 'Nama Khusus Summarecon',
            'tipe' => Employee::TIPE_KARYAWAN_AKTIF,
        ]);
        Division::withoutGlobalScopes()->create([
            'office_id' => $summarecon->id,
            'nama' => 'Divisi Khusus Summarecon',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->withSession([OfficeContext::SESSION_KEY => (string) $baleendah->id])
            ->get(route('hris.employees.index'))
            ->assertOk()
            ->assertDontSee('Nama Khusus Summarecon');

        $this->get(route('hris.divisions.index'))
            ->assertOk()
            ->assertDontSee('Divisi Khusus Summarecon');
    }
}
