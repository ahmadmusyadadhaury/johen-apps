<?php

namespace Tests\Feature;

use App\Models\ItTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketRevertScenariosTest extends TestCase
{
    use RefreshDatabase;

    private function makeTicket(User $requester, string $status, ?int $assigneeId = null, array $extra = []): ItTicket
    {
        return ItTicket::create(array_merge([
            'kode' => 'IT-20260814-' . str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT),
            'requester_id' => $requester->id,
            'assignee_id' => $assigneeId,
            'judul' => 'Test',
            'deskripsi' => 'Deskripsi',
            'kategori' => 'perangkat',
            'prioritas' => 'sedang',
            'status' => $status,
        ], $extra));
    }

    public function test_koordinator_cannot_revert_own_assigned_ticket_to_menunggu(): void
    {
        $koord = User::factory()->create(['role' => 'koordinator_it']);
        $requester = User::factory()->create();
        $t = $this->makeTicket($requester, 'diproses', $koord->id, ['proses_mulai_at' => now()]);

        $this->actingAs($koord)->patch('/it/tickets/' . $t->id, [
            'assignee_id' => $koord->id,
            'status' => 'menunggu',
        ])->assertForbidden();

        $this->assertSame('diproses', $t->fresh()->status);
    }

    public function test_koordinator_cannot_change_status_of_ticket_assigned_to_other(): void
    {
        $koord = User::factory()->create(['role' => 'koordinator_it']);
        $staff = User::factory()->create(['role' => 'staff_it']);
        $requester = User::factory()->create();
        $t = $this->makeTicket($requester, 'diproses', $staff->id, ['proses_mulai_at' => now()]);

        $this->actingAs($koord)->patch('/it/tickets/' . $t->id, [
            'assignee_id' => $staff->id,
            'status' => 'menunggu',
        ])->assertForbidden();

        $this->assertSame('diproses', $t->fresh()->status);
    }

    public function test_selesai_ticket_cannot_revert_to_menunggu(): void
    {
        $koord = User::factory()->create(['role' => 'koordinator_it']);
        $requester = User::factory()->create();
        $t = $this->makeTicket($requester, 'selesai', $koord->id, ['selesai_at' => now()]);

        $this->actingAs($koord)->patch('/it/tickets/' . $t->id, [
            'assignee_id' => $koord->id,
            'status' => 'menunggu',
        ])->assertForbidden();

        $this->assertSame('selesai', $t->fresh()->status);
    }
}