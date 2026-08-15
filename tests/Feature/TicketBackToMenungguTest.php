<?php

namespace Tests\Feature;

use App\Models\ItTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketBackToMenungguTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_cannot_go_back_to_menunggu_after_diproses(): void
    {
        $staff = User::factory()->create(['role' => 'staff_it']);
        $requester = User::factory()->create();

        $t = ItTicket::create([
            'kode' => 'IT-20260814-001',
            'requester_id' => $requester->id,
            'assignee_id' => $staff->id,
            'judul' => 'Printer rusak',
            'deskripsi' => 'Tidak bisa mencetak',
            'kategori' => 'perangkat',
            'prioritas' => 'sedang',
            'status' => 'diproses',
            'mulai_ditangani_at' => now(),
            'proses_mulai_at' => now(),
        ]);

        $this->actingAs($staff)->patch('/it/tickets/' . $t->id, [
            'assignee_id' => $staff->id,
            'status' => 'menunggu',
        ])->assertForbidden();

        $this->assertSame('diproses', $t->fresh()->status);
    }

    public function test_koordinator_can_still_assign_pic_to_menunggu_ticket(): void
    {
        $koord = User::factory()->create(['role' => 'koordinator_it']);
        $staff = User::factory()->create(['role' => 'staff_it']);
        $requester = User::factory()->create();

        $t = ItTicket::create([
            'kode' => 'IT-20260814-002',
            'requester_id' => $requester->id,
            'judul' => 'Kabel putus',
            'deskripsi' => 'Kabel LAN putus',
            'kategori' => 'jaringan',
            'prioritas' => 'tinggi',
            'status' => 'menunggu',
        ]);

        $this->actingAs($koord)->patch('/it/tickets/' . $t->id, [
            'assignee_id' => $staff->id,
            'status' => 'menunggu',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame('menunggu', $t->fresh()->status);
        $this->assertSame($staff->id, (int) $t->fresh()->assignee_id);
    }

    public function test_forward_flow_diproses_to_dijeda_to_dilanjutkan_still_works(): void
    {
        $staff = User::factory()->create(['role' => 'staff_it']);
        $requester = User::factory()->create();

        $t = ItTicket::create([
            'kode' => 'IT-20260814-003',
            'requester_id' => $requester->id,
            'assignee_id' => $staff->id,
            'judul' => 'Mouse rusak',
            'deskripsi' => 'Scroll tidak berfungsi',
            'kategori' => 'perangkat',
            'prioritas' => 'rendah',
            'status' => 'diproses',
            'mulai_ditangani_at' => now(),
            'proses_mulai_at' => now(),
        ]);

        $this->actingAs($staff)->patch('/it/tickets/' . $t->id, [
            'assignee_id' => $staff->id,
            'status' => 'dijeda',
            'alasan_jeda' => 'Menunggu sparepart',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('dijeda', $t->fresh()->status);

        $this->actingAs($staff)->patch('/it/tickets/' . $t->id, [
            'assignee_id' => $staff->id,
            'status' => 'dilanjutkan',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('dilanjutkan', $t->fresh()->status);
    }
}