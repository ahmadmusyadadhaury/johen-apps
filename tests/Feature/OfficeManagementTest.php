<?php

namespace Tests\Feature;

use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_office_management_page(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->get(route('offices.index'))
            ->assertOk()
            ->assertSee('Master Kantor')
            ->assertSee('Kantor Summarecon Bandung')
            ->assertSee('Kantor Baleendah')
            ->assertSee('Kantor Johen Streamer');
    }

    public function test_non_super_admin_cannot_open_office_management_page(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->actingAs($manager)
            ->get(route('offices.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_update_an_office(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $office = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();

        $this->actingAs($superAdmin)
            ->patch(route('offices.update', $office), [
                'code' => $office->code,
                'name' => 'Kantor Baleendah Utama',
                'slug' => $office->slug,
                'address' => 'Baleendah, Kabupaten Bandung',
                'attendance_method' => Office::ATTENDANCE_WEB_FACE,
                'attendance_latitude' => -7.0075000,
                'attendance_longitude' => 107.6259000,
                'attendance_radius_meters' => 75,
                'is_active' => true,
                'sort_order' => 2,
            ])
            ->assertRedirect(route('offices.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('offices', [
            'id' => $office->id,
            'name' => 'Kantor Baleendah Utama',
            'address' => 'Baleendah, Kabupaten Bandung',
            'attendance_method' => Office::ATTENDANCE_WEB_FACE,
            'attendance_latitude' => -7.0075000,
            'attendance_longitude' => 107.6259000,
            'attendance_radius_meters' => 75,
        ]);
    }

    public function test_office_update_rejects_duplicate_code_and_invalid_attendance_method(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();
        $summarecon = Office::where('code', Office::CODE_SUMMARECON_BANDUNG)->firstOrFail();

        $this->actingAs($superAdmin)
            ->from(route('offices.index'))
            ->patch(route('offices.update', $baleendah), [
                'code' => $summarecon->code,
                'name' => $baleendah->name,
                'slug' => $baleendah->slug,
                'address' => null,
                'attendance_method' => 'manual',
                'attendance_latitude' => null,
                'attendance_longitude' => null,
                'attendance_radius_meters' => 100,
                'is_active' => true,
                'sort_order' => 2,
            ])
            ->assertRedirect(route('offices.index'))
            ->assertSessionHasErrors(['code', 'attendance_method']);
    }
}
