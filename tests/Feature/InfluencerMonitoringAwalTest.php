<?php

namespace Tests\Feature;

use App\Livewire\InfluencerPengajuanTable;
use App\Livewire\InfluencerTable;
use App\Livewire\SidebarInfluencerMonitoringBadge;
use App\Models\Employee;
use App\Models\InfluencerMonitoring;
use App\Models\InfluencerPengajuan;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InfluencerMonitoringAwalTest extends TestCase
{
    use RefreshDatabase;

    private function employeeWithPosition(string $positionName, string $role): User
    {
        $position = Position::firstOrCreate(['nama' => $positionName], ['is_active' => true]);
        $employee = Employee::create([
            'nik' => 'NIK-'.uniqid(),
            'nama' => $positionName.'-'.uniqid(),
            'status' => 'aktif',
            'posisi' => $positionName,
        ]);
        $employee->positions()->attach($position->id, ['is_main' => true]);

        return User::factory()->create([
            'role' => $role,
            'employee_id' => $employee->id,
        ]);
    }

    private function kol(string $positionName = 'Admin KOL 1'): User
    {
        return $this->employeeWithPosition($positionName, User::ROLE_STAFF_CREATIVE);
    }

    private function coordinator(): User
    {
        return User::factory()->create(['role' => User::ROLE_KOORDINATOR_CREATIVE]);
    }

    private function hos(): User
    {
        return $this->employeeWithPosition('Head of Store 1', User::ROLE_STAFF);
    }

    private function gm(): User
    {
        return User::factory()->create(['role' => User::ROLE_GM_CEO]);
    }

    private function ajukan(User $kol, array $values = []): InfluencerPengajuan
    {
        Livewire::actingAs($kol)
            ->test(InfluencerTable::class)
            ->set('nama', $values['nama'] ?? 'Influencer Monitoring Awal')
            ->set('divisi', $values['divisi'] ?? 'Johen PUBG')
            ->set('rekomendasiLamaKontrak', $values['rekomendasiLamaKontrak'] ?? 6)
            ->set('biaya', 1500000)
            ->set('initialMonitoringMonth', $values['month'] ?? '')
            ->set('initialMonitoringFollowers', $values['followers'] ?? '')
            ->set('initialMonitoringViewers', $values['viewers'] ?? '')
            ->set('initialMonitoringDuration', $values['duration'] ?? '')
            ->set('initialMonitoringTargetDuration', $values['target'] ?? '130')
            ->set('initialMonitoringNotes', $values['notes'] ?? '')
            ->set('initialMonitoringBenefits', $values['benefits'] ?? '')
            ->call('save')
            ->assertHasNoErrors();

        return InfluencerPengajuan::query()->latest('id')->firstOrFail();
    }

    /**
     * Menjalankan seluruh tangga persetujuan sampai Influencer terbentuk:
     * pending_creative -> pending_hos1 -> pending_gm -> approved.
     */
    private function approveThrough(InfluencerPengajuan $pengajuan): InfluencerPengajuan
    {
        Livewire::actingAs($this->coordinator())->test(InfluencerPengajuanTable::class)->call('approve', $pengajuan->id);
        Livewire::actingAs($this->hos())->test(InfluencerPengajuanTable::class)->call('approve', $pengajuan->id);
        Livewire::actingAs($this->gm())->test(InfluencerPengajuanTable::class)->call('approve', $pengajuan->id);

        return $pengajuan->fresh();
    }

    public function test_pengajuan_tanpa_monitoring_awal_tidak_membuat_baris_monitoring(): void
    {
        $kol = $this->kol();
        $this->hos();

        $pengajuan = $this->approveThrough($this->ajukan($kol));

        $this->assertSame('approved', $pengajuan->status);
        $this->assertNotNull($pengajuan->influencer_id);
        $this->assertDatabaseCount('influencer_monitorings', 0);
    }

    public function test_monitoring_awal_disalin_ke_influencer_monitoring_saat_gm_menyetujui(): void
    {
        $kol = $this->kol();
        $this->hos();
        $month = now()->subMonth()->format('Y-m');

        $pengajuan = $this->approveThrough($this->ajukan($kol, [
            'month' => $month,
            'followers' => 1700000,
            'viewers' => 1400000,
            'duration' => 162,
            'target' => 130,
            'notes' => 'Catatan monitoring awal.',
            'benefits' => 'Bonus konten dan penempatan logo di channel.',
        ]));

        $monitoring = InfluencerMonitoring::query()->sole();

        $this->assertSame($pengajuan->influencer_id, $monitoring->influencer_id);
        $this->assertSame($month.'-01', $monitoring->period_month->toDateString());
        $this->assertSame(1700000, $monitoring->followers);
        $this->assertSame(1400000, $monitoring->viewers_last_month);
        $this->assertSame(162.0, (float) $monitoring->duration_hours);
        $this->assertSame(130.0, (float) $monitoring->target_duration_hours);
        $this->assertSame('Catatan monitoring awal.', $monitoring->notes);
        $this->assertStringContainsString('Bonus konten', $monitoring->benefits);
    }

    public function test_approve_gm_dipanggil_dua_kali_tidak_menggandakan_data(): void
    {
        $kol = $this->kol();
        $this->hos();

        $pengajuan = $this->approveThrough($this->ajukan($kol, [
            'month' => now()->format('Y-m'),
            'followers' => 500,
            'viewers' => 400,
            'duration' => 100,
        ]));

        $monitoringId = InfluencerMonitoring::query()->sole()->id;

        // Approve kedua: status sudah approved sehingga guard status menahannya.
        Livewire::actingAs($this->gm())
            ->test(InfluencerPengajuanTable::class)
            ->call('approve', $pengajuan->id);

        $this->assertDatabaseCount('influencers', 1);
        $this->assertDatabaseCount('influencer_monitorings', 1);
        $this->assertDatabaseHas('influencer_monitorings', ['id' => $monitoringId, 'followers' => 500]);
    }

    public function test_blok_monitoring_awal_bersifat_opsional(): void
    {
        $kol = $this->kol();
        $this->hos();

        // Bulan terisi tapi angka kosong harus ditolak.
        Livewire::actingAs($kol)
            ->test(InfluencerTable::class)
            ->set('nama', 'Tanpa Angka')
            ->set('divisi', 'Johen PUBG')
            ->set('rekomendasiLamaKontrak', 6)
            ->set('initialMonitoringMonth', now()->format('Y-m'))
            ->call('save')
            ->assertHasErrors(['initialMonitoringFollowers']);

        $this->assertDatabaseCount('influencer_pengajuans', 0);

        // Seluruhnya kosong harus lolos.
        $this->ajukan($kol, [
            'nama' => 'Sepenuhnya Kosong',
            'month' => '',
            'target' => '',
        ]);

        $pengajuan = InfluencerPengajuan::query()->sole();

        $this->assertNull($pengajuan->monitoring_month);
        $this->assertNull($pengajuan->monitoring_followers);
    }

    public function test_bulan_monitoring_awal_tidak_boleh_di_masa_depan(): void
    {
        $kol = $this->kol();
        $this->hos();

        Livewire::actingAs($kol)
            ->test(InfluencerTable::class)
            ->set('nama', 'Bulan Depan')
            ->set('divisi', 'Johen PUBG')
            ->set('rekomendasiLamaKontrak', 6)
            ->set('initialMonitoringMonth', now()->addMonth()->format('Y-m'))
            ->set('initialMonitoringFollowers', 100)
            ->set('initialMonitoringViewers', 100)
            ->set('initialMonitoringDuration', 100)
            ->call('save')
            ->assertHasErrors(['initialMonitoringMonth']);

        $this->assertDatabaseCount('influencer_pengajuans', 0);
    }

    public function test_monitoring_awal_tidak_bisa_diubah_setelah_pengajuan_disetujui(): void
    {
        $kol = $this->kol();
        $this->hos();

        $pengajuan = $this->approveThrough($this->ajukan($kol, [
            'month' => now()->format('Y-m'),
            'followers' => 500,
            'viewers' => 400,
            'duration' => 100,
        ]));

        Livewire::actingAs($kol)
            ->test(InfluencerPengajuanTable::class)
            ->call('openEdit', $pengajuan->id)
            ->set('nama', 'Nama Direvisi')
            ->set('monitoringMonth', now()->subMonth()->format('Y-m'))
            ->set('monitoringFollowers', 999999)
            ->call('save')
            ->assertHasNoErrors();

        $pengajuan->refresh();

        $this->assertSame('Nama Direvisi', $pengajuan->nama);
        $this->assertSame(500, (int) $pengajuan->monitoring_followers);
        $this->assertSame(1, InfluencerMonitoring::query()->count());
    }

    public function test_badge_menghitung_influencer_milik_sendiri_yang_belum_diisi_bulan_ini(): void
    {
        $kolA = $this->kol('Admin KOL 1');
        $kolB = $this->kol('Admin KOL 2');
        $this->hos();

        $terisi = $this->approveThrough($this->ajukan($kolA, [
            'nama' => 'Sudah Diisi',
            'month' => now()->format('Y-m'),
            'followers' => 500,
            'viewers' => 400,
            'duration' => 100,
        ]));
        $kosong = $this->approveThrough($this->ajukan($kolA, ['nama' => 'Belum Diisi']));
        $milikOrangLain = $this->approveThrough($this->ajukan($kolB, [
            'nama' => 'Milik KOL B',
            'month' => now()->format('Y-m'),
            'followers' => 700,
            'viewers' => 600,
            'duration' => 90,
        ]));

        $this->assertDatabaseCount('influencers', 3);
        $this->assertDatabaseCount('influencer_monitorings', 2);
        $this->assertNotNull($terisi->influencer_id);
        $this->assertNotNull($kosong->influencer_id);
        $this->assertNotNull($milikOrangLain->influencer_id);

        // KOL A punya 2 influencer, baru 1 yang terisi bulan ini.
        Livewire::actingAs($kolA)
            ->test(SidebarInfluencerMonitoringBadge::class)
            ->assertViewHas('count', 1);

        // KOL B punya 1 influencer dan sudah terisi.
        Livewire::actingAs($kolB)
            ->test(SidebarInfluencerMonitoringBadge::class)
            ->assertViewHas('count', 0);
    }

    public function test_badge_mengembalikan_nol_untuk_role_bukan_admin_kol(): void
    {
        Livewire::actingAs($this->gm())
            ->test(SidebarInfluencerMonitoringBadge::class)
            ->assertViewHas('count', 0);
    }

    public function test_monitoring_bulan_lain_tidak_memenuhi_badge_bulan_ini(): void
    {
        $kol = $this->kol();
        $this->hos();

        $this->approveThrough($this->ajukan($kol, [
            'month' => now()->subMonth()->format('Y-m'),
            'followers' => 500,
            'viewers' => 400,
            'duration' => 100,
        ]));

        Livewire::actingAs($kol)
            ->test(SidebarInfluencerMonitoringBadge::class)
            ->assertViewHas('count', 1);
    }

    public function test_perpanjangan_tidak_membuat_monitoring_awal(): void
    {
        $kol = $this->kol();
        $this->hos();

        $pengajuan = $this->approveThrough($this->ajukan($kol));

        Livewire::actingAs($kol)
            ->test(InfluencerTable::class)
            ->call('openExtensionRequest', $pengajuan->influencer_id)
            ->set('extensionDuration', 6)
            ->set('extensionCost', 1500000)
            ->call('submitExtensionRequest')
            ->assertHasNoErrors();

        $perpanjangan = InfluencerPengajuan::query()->where('is_perpanjangan', true)->sole();

        $this->assertNull($perpanjangan->monitoring_month);

        Livewire::actingAs($this->coordinator())->test(InfluencerPengajuanTable::class)->call('approve', $perpanjangan->id);
        Livewire::actingAs($this->hos())->test(InfluencerPengajuanTable::class)->call('approve', $perpanjangan->id);
        Livewire::actingAs($this->gm())->test(InfluencerPengajuanTable::class)->call('approve', $perpanjangan->id);

        $this->assertDatabaseCount('influencers', 1);
        $this->assertDatabaseCount('influencer_monitorings', 0);
    }
}
