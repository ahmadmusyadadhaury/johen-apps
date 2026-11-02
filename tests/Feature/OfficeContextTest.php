<?php

namespace Tests\Feature;

use App\Models\Office;
use App\Models\User;
use App\Support\OfficeContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficeContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_starts_in_all_offices_context(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->get(route('offices.index'))
            ->assertOk()
            ->assertSee('Kantor aktif: Semua Kantor', false)
            ->assertSessionHas(OfficeContext::SESSION_KEY, OfficeContext::ALL_OFFICES);
    }

    public function test_gm_can_switch_to_a_specific_office_and_back_to_all_offices(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GM_CEO]);
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();

        $this->actingAs($gm)
            ->post(route('office-context.update'), ['office_id' => (string) $baleendah->id])
            ->assertRedirect()
            ->assertSessionHas(OfficeContext::SESSION_KEY, (string) $baleendah->id);

        $this->post(route('office-context.update'), ['office_id' => OfficeContext::ALL_OFFICES])
            ->assertRedirect()
            ->assertSessionHas(OfficeContext::SESSION_KEY, OfficeContext::ALL_OFFICES);
    }

    public function test_regular_user_cannot_select_all_or_an_unassigned_office(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STAFF]);
        $summarecon = Office::where('code', Office::CODE_SUMMARECON_BANDUNG)->firstOrFail();
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();
        $user->offices()->attach($summarecon, ['is_default' => true]);

        $this->actingAs($user)
            ->post(route('office-context.update'), ['office_id' => OfficeContext::ALL_OFFICES])
            ->assertForbidden();

        $this->post(route('office-context.update'), ['office_id' => (string) $baleendah->id])
            ->assertForbidden();
    }

    public function test_regular_user_context_falls_back_to_their_default_office(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STAFF]);
        $streamer = Office::where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();
        $user->offices()->attach($streamer, ['is_default' => true]);

        $this->actingAs($user)
            ->withSession([OfficeContext::SESSION_KEY => '999999'])
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSessionHas(OfficeContext::SESSION_KEY, (string) $streamer->id);
    }

    public function test_weekly_meeting_menu_is_hidden_for_johen_streamer_office(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_STREAMER]);
        $streamer = Office::where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();
        $user->offices()->attach($streamer, ['is_default' => true]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertDontSee('Weekly Meeting');
    }
}
