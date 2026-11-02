<?php

namespace Tests\Feature;

use App\Livewire\EmployeeTable;
use App\Models\Office;
use App\Models\User;
use App\Support\OfficeContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EmployeeWorkLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_johen_streamer_is_available_and_selected_for_streamer_context(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $streamer = Office::where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();

        $this->actingAs($superAdmin)
            ->withSession([OfficeContext::SESSION_KEY => (string) $streamer->id]);

        Livewire::test(EmployeeTable::class)
            ->call('openCreateModal')
            ->assertSet('lokasi_kerja', 'Johen Streamer')
            ->set('step', 2)
            ->assertSeeHtml('<option value="Johen Streamer">Johen Streamer</option>');
    }
}
