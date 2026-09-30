<?php

namespace Tests\Feature;

use App\Livewire\InfluencerPengajuanTable;
use App\Livewire\InfluencerTable;
use App\Models\Employee;
use App\Models\Influencer;
use App\Models\InfluencerMonitoring;
use App\Models\InfluencerPembayaran;
use App\Models\InfluencerPengajuan;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InfluencerDeletionTest extends TestCase
{
    use RefreshDatabase;

    private function kol(string $nama): User
    {
        $position = Position::create(['nama' => $nama, 'is_active' => true]);
        $employee = Employee::create([
            'nik' => 'NIK-'.uniqid(),
            'nama' => $nama,
            'status' => 'aktif',
            'posisi' => $nama,
        ]);
        $employee->positions()->attach($position->id, ['is_main' => true]);

        return User::factory()->create([
            'role' => 'staff_creative',
            'employee_id' => $employee->id,
        ]);
    }

    private function influencer(string $nama = 'Influencer Test', string $divisi = 'Johen PUBG'): Influencer
    {
        return Influencer::create([
            'nama' => $nama,
            'divisi' => $divisi,
            'mulai_kontrak' => now()->startOfMonth()->toDateString(),
            'habis_kontrak' => now()->startOfMonth()->addMonths(5)->toDateString(),
            'biaya' => 1000000,
        ]);
    }

    private function pengajuanApproved(User $kol, Influencer $influencer, ?int $influencerId = null): InfluencerPengajuan
    {
        return InfluencerPengajuan::create([
            'no_kontrak' => 'K-'.uniqid(),
            'nama' => $influencer->nama,
            'divisi' => $influencer->divisi,
            'status' => 'approved',
            'pengaju_id' => $kol->id,
            'influencer_id' => $influencerId ?? $influencer->id,
            'mulai_kontrak' => $influencer->mulai_kontrak,
            'habis_kontrak' => $influencer->habis_kontrak,
        ]);
    }

    private function monitoring(Influencer $influencer, Carbon $month): InfluencerMonitoring
    {
        return InfluencerMonitoring::create([
            'influencer_id' => $influencer->id,
            'period_month' => $month->startOfMonth()->toDateString(),
            'followers' => 100,
            'viewers_last_month' => 50,
            'duration_hours' => 10,
            'target_duration_hours' => 130,
            'notes' => 'Catatan monitoring.',
        ]);
    }

    private function payment(Influencer $influencer, int $bulanKe, Carbon $jatuhTempo): InfluencerPembayaran
    {
        return InfluencerPembayaran::create([
            'influencer_id' => $influencer->id,
            'bulan_ke' => $bulanKe,
            'tanggal_jatuh_tempo' => $jatuhTempo->toDateString(),
            'jumlah' => 1000000,
            'status' => 'pending',
        ]);
    }

    public function test_menghapus_pengajuan_approved_menghapus_influencer_monitoring_dan_pembayaran(): void
    {
        $kol = $this->kol('Admin KOL 1');
        $influencer = $this->influencer();
        $monitoring = $this->monitoring($influencer, now()->subMonth());
        $payment = $this->payment($influencer, 1, now()->addDays(3));
        $pengajuan = $this->pengajuanApproved($kol, $influencer);

        Livewire::actingAs($kol)
            ->test(InfluencerPengajuanTable::class)
            ->call('confirmDelete', $pengajuan->id)
            ->call('deletePengajuan')
            ->assertOk();

        $this->assertDatabaseMissing('influencers', ['id' => $influencer->id]);
        $this->assertDatabaseMissing('influencer_monitorings', ['id' => $monitoring->id]);
        $this->assertDatabaseMissing('influencer_pembayarans', ['id' => $payment->id]);
        $this->assertDatabaseMissing('influencer_pengajuans', ['id' => $pengajuan->id]);
    }

    public function test_menghapus_pengajuan_approved_dengan_influencer_id_null_tetap_menghapus_influencer(): void
    {
        $kol = $this->kol('Admin KOL 1');
        $influencer = $this->influencer();
        $pengajuan = $this->pengajuanApproved($kol, $influencer, null);

        Livewire::actingAs($kol)
            ->test(InfluencerPengajuanTable::class)
            ->call('confirmDelete', $pengajuan->id)
            ->call('deletePengajuan')
            ->assertOk();

        $this->assertDatabaseMissing('influencers', ['id' => $influencer->id]);
        $this->assertDatabaseMissing('influencer_pengajuans', ['id' => $pengajuan->id]);
    }

    public function test_menghapus_influencer_langsung_juga_menghapus_pengajuan_tertaut(): void
    {
        $kol = $this->kol('Admin KOL 1');
        $influencer = $this->influencer();
        $monitoring = $this->monitoring($influencer, now()->subMonth());
        $pengajuan = $this->pengajuanApproved($kol, $influencer);

        Livewire::actingAs($kol)
            ->test(InfluencerTable::class)
            ->call('delete', $influencer->id)
            ->call('deleteConfirmed')
            ->assertOk();

        $this->assertDatabaseMissing('influencers', ['id' => $influencer->id]);
        $this->assertDatabaseMissing('influencer_monitorings', ['id' => $monitoring->id]);
        $this->assertDatabaseMissing('influencer_pengajuans', ['id' => $pengajuan->id]);
    }

    public function test_stats_card_admin_kol_hanya_menghitung_data_milik_sendiri(): void
    {
        $kolA = $this->kol('Admin KOL 1');
        $kolB = $this->kol('Admin KOL 2');

        $influencerMilikA = $this->influencer('Influencer A', 'Johen MLBB');
        $this->pengajuanApproved($kolA, $influencerMilikA);

        $influencerMilikB = $this->influencer('Influencer B', 'Johen PUBG');
        $this->pengajuanApproved($kolB, $influencerMilikB);

        Livewire::actingAs($kolA)
            ->test(InfluencerTable::class)
            ->assertViewHas('aktifCount', 1)
            ->assertViewHas('tidakAktifCount', 0)
            ->assertDontSee('Influencer B')
            ->assertSee('Hapus influencer Influencer A', false)
            ->assertSee('Influencer A');
    }

    public function test_menghapus_influencer_menurunkan_angka_stats_card(): void
    {
        $kol = $this->kol('Admin KOL 1');
        $influencer = $this->influencer();
        $pengajuan = $this->pengajuanApproved($kol, $influencer);

        Livewire::actingAs($kol)
            ->test(InfluencerTable::class)
            ->assertViewHas('aktifCount', 1);

        Livewire::actingAs($kol)
            ->test(InfluencerPengajuanTable::class)
            ->call('confirmDelete', $pengajuan->id)
            ->call('deletePengajuan')
            ->assertOk();

        Livewire::actingAs($kol)
            ->test(InfluencerTable::class)
            ->assertViewHas('aktifCount', 0)
            ->assertViewHas('tidakAktifCount', 0);
    }
}