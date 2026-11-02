<?php

namespace Tests\Feature;

use App\Livewire\UserTable;
use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OfficeUserAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_can_only_access_assigned_offices(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STAFF]);
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();
        $streamer = Office::where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();

        $user->offices()->attach($baleendah, ['is_default' => true]);

        $this->assertTrue($user->canAccessOffice($baleendah));
        $this->assertFalse($user->canAccessOffice($streamer));
        $this->assertTrue($user->defaultOffice()->is($baleendah));
    }

    public function test_super_admin_and_gm_have_global_office_access_without_memberships(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $gm = User::factory()->create(['role' => User::ROLE_GM_CEO]);
        $streamer = Office::where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();

        $this->assertTrue($superAdmin->canAccessOffice($streamer));
        $this->assertTrue($gm->canAccessOffice($streamer));
    }

    public function test_super_admin_can_create_an_account_with_office_assignments(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();
        $streamer = Office::where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();

        $this->actingAs($superAdmin);

        Livewire::test(UserTable::class)
            ->set('name', 'Staff Multi Kantor')
            ->set('username', 'staff.multi')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('role', User::ROLE_STAFF)
            ->set('officeIds', [$baleendah->id, $streamer->id])
            ->call('save')
            ->assertHasNoErrors();

        $user = User::where('username', 'staff.multi')->firstOrFail();

        $this->assertEqualsCanonicalizing(
            [$baleendah->id, $streamer->id],
            $user->offices()->pluck('offices.id')->all(),
        );
        $this->assertSame($baleendah->id, $user->defaultOffice()?->id);
    }

    public function test_regular_account_requires_at_least_one_office(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->actingAs($superAdmin);

        Livewire::test(UserTable::class)
            ->set('name', 'Tanpa Kantor')
            ->set('username', 'tanpa.kantor')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('role', User::ROLE_STAFF)
            ->set('officeIds', [])
            ->call('save')
            ->assertHasErrors(['officeIds']);

        $this->assertDatabaseMissing('users', ['username' => 'tanpa.kantor']);
    }
}
