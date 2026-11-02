<?php

namespace Tests\Feature;

use App\Livewire\UserTable;
use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StreamerRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_streamer_is_an_operational_staff_role(): void
    {
        $streamer = User::factory()->make(['role' => User::ROLE_STREAMER]);

        $this->assertTrue($streamer->isStreamer());
        $this->assertTrue($streamer->isStaff());
        $this->assertSame(1, $streamer->roleLevel());
        $this->assertSame('Streamer', $streamer->getRoleDisplayName());
        $this->assertFalse($streamer->canCreateData());
        $this->assertFalse($streamer->hasGlobalOfficeAccess());
    }

    public function test_super_admin_can_create_streamer_for_johen_streamer_office(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $office = Office::query()->where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();

        Livewire::actingAs($superAdmin)
            ->test(UserTable::class)
            ->set('name', 'Streamer Test')
            ->set('username', 'streamer.test')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('role', User::ROLE_STREAMER)
            ->set('officeIds', [$office->id])
            ->call('save')
            ->assertHasNoErrors();

        $streamer = User::query()->where('username', 'streamer.test')->firstOrFail();

        $this->assertSame(User::ROLE_STREAMER, $streamer->role);
        $this->assertTrue($streamer->canAccessOffice($office));
        $this->assertSame([$office->id], $streamer->offices()->pluck('offices.id')->all());
    }
}
