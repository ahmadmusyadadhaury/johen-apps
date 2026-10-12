<?php

namespace Tests\Feature;

use App\Livewire\ScriptVideoTable;
use App\Livewire\SidebarScriptVideoBadge;
use App\Models\ScriptVideoTarget;
use App\Models\ScriptVideoUpload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ScriptVideoSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function koordinator(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Koor Creative',
            'username' => 'koor_creative',
            'email' => 'koor@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_KOORDINATOR_CREATIVE,
        ], $overrides));
    }

    private function superAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'username' => 'super',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }

    private function videoAnimator(): User
    {
        return User::create([
            'name' => 'Video Animator',
            'username' => 'video_animator',
            'email' => 'video@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STAFF_CREATIVE,
        ]);
    }

    public function test_weekly_quota_is_dynamic(): void
    {
        $this->assertSame(19, ScriptVideoTarget::weeklyQuotaFor(75, 0, 1));
        $this->assertSame(22, ScriptVideoTarget::weeklyQuotaFor(75, 10, 2));
        $this->assertSame(75, ScriptVideoTarget::weeklyQuotaFor(75, 0, 4));
    }

    public function test_koordinator_renders_upload_mode(): void
    {
        $this->actingAs($this->koordinator());

        Livewire::test(ScriptVideoTable::class)
            ->assertViewHas('isRekap', false)
            ->assertViewHas('target', 75)
            ->assertViewHas('jumlah', 0);
    }

    public function test_koordinator_can_upload_script(): void
    {
        Storage::fake('public');

        $user = $this->koordinator();
        $this->actingAs($user);

        Livewire::test(ScriptVideoTable::class)
            ->set('judul', 'Script Video Ep 1')
            ->set('kategoriGame', 'MLBB')
            ->set('bulan', 'Oktober')
            ->set('tahun', 2026)
            ->set('file', UploadedFile::fake()->create('script.pdf', 10, 'application/pdf'))
            ->call('save');

        $this->assertDatabaseHas('script_video_uploads', [
            'user_id' => $user->id,
            'bulan' => 'Oktober',
            'tahun' => 2026,
            'judul' => 'Script Video Ep 1',
            'kategori_game' => 'MLBB',
            'minggu' => ScriptVideoUpload::mingguUntukTanggal(now()),
        ]);

        Storage::disk('public')->assertExists(ScriptVideoUpload::first()->file_path);
    }

    public function test_target_can_be_upserted_by_super_admin(): void
    {
        $this->actingAs($this->superAdmin());

        Livewire::test(ScriptVideoTable::class)
            ->set('bulan', 'Oktober')
            ->set('tahun', 2026)
            ->set('targetInput', 100)
            ->call('saveTarget');

        $this->assertSame(100, ScriptVideoTarget::targetFor('Oktober', 2026));
    }

    public function test_super_admin_sees_rekap(): void
    {
        $this->koordinator();
        $this->actingAs($this->superAdmin());

        Livewire::test(ScriptVideoTable::class)
            ->assertViewHas('isRekap', true)
            ->assertViewHas('rekapStats', function ($stats) {
                return $stats['total'] === 1 && $stats['belum'] === 1;
            })
            ->assertViewHas('rekap', function ($rekap) {
                $row = $rekap->first();

                return $row->weeklySummary[1]['quota'] === 19 && $row->weeklySummary[2]['quota'] === 25;
            });
    }

    public function test_koordinator_badge_shows_weekly_remaining(): void
    {
        $user = $this->koordinator();
        $this->actingAs($user);

        $now = now();
        $bulan = ScriptVideoUpload::currentBulanName();
        $tahun = ScriptVideoUpload::currentTahun();
        $minggu = ScriptVideoUpload::mingguUntukTanggal($now);

        foreach (range(1, 3) as $i) {
            ScriptVideoUpload::create([
                'user_id' => $user->id,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'judul' => "Script {$i}",
                'file_path' => "script-{$i}.png",
                'file_name' => 'script.png',
                'created_at' => $now,
            ]);
        }

        $target = ScriptVideoTarget::targetFor($bulan, $tahun);
        $uploadedSebelum = ScriptVideoUpload::forPeriod($bulan, $tahun)
            ->where('user_id', $user->id)
            ->where('minggu', '<', $minggu)
            ->count();
        $quota = ScriptVideoTarget::weeklyQuotaFor($target, $uploadedSebelum, $minggu);

        Livewire::test(SidebarScriptVideoBadge::class)
            ->assertViewHas('badge', max(0, $quota - 3));
    }

    public function test_super_admin_badge_counts_unmet_kordinators(): void
    {
        $this->koordinator();
        $this->actingAs($this->superAdmin());

        Livewire::test(SidebarScriptVideoBadge::class)
            ->assertViewHas('badge', 1);
    }

    public function test_video_animator_sees_all_scripts(): void
    {
        $bulan = ScriptVideoUpload::currentBulanName();
        $tahun = ScriptVideoUpload::currentTahun();

        $k1 = $this->koordinator(['name' => 'Koor A', 'username' => 'koor_a', 'email' => 'a@example.com']);
        $k2 = $this->koordinator(['name' => 'Koor B', 'username' => 'koor_b', 'email' => 'b@example.com']);

        foreach (range(1, 3) as $i) {
            ScriptVideoUpload::create([
                'user_id' => $k1->id, 'bulan' => $bulan, 'tahun' => $tahun,
                'judul' => "KoorA {$i}", 'file_path' => "a-{$i}.png", 'file_name' => 'script.png', 'created_at' => now(),
            ]);
            ScriptVideoUpload::create([
                'user_id' => $k2->id, 'bulan' => $bulan, 'tahun' => $tahun,
                'judul' => "KoorB {$i}", 'file_path' => "b-{$i}.png", 'file_name' => 'script.png', 'created_at' => now(),
            ]);
        }

        $this->actingAs($this->videoAnimator());

        Livewire::test(ScriptVideoTable::class)
            ->assertViewHas('isVideoAnimator', true)
            ->assertViewHas('isRekap', false)
            ->assertViewHas('contributorCount', 2)
            ->assertViewHas('uploads', fn ($uploads) => $uploads->total() === 6)
            ->set('search', 'Koor A')
            ->assertViewHas('uploads', fn ($uploads) => $uploads->total() === 3);
    }

    public function test_video_animator_can_toggle_konten_dibuat(): void
    {
        $upload = ScriptVideoUpload::create([
            'user_id' => $this->koordinator()->id,
            'bulan' => ScriptVideoUpload::currentBulanName(),
            'tahun' => ScriptVideoUpload::currentTahun(),
            'judul' => 'Script Toggle', 'file_path' => 'toggle.png', 'file_name' => 'toggle.png', 'created_at' => now(),
        ]);

        $this->actingAs($this->videoAnimator());

        Livewire::test(ScriptVideoTable::class)
            ->call('toggleKontenDibuat', $upload->id);

        $this->assertDatabaseHas('script_video_uploads', ['id' => $upload->id, 'konten_dibuat' => true]);
    }

    public function test_non_video_animator_cannot_toggle_konten_dibuat(): void
    {
        $koor = $this->koordinator();
        $upload = ScriptVideoUpload::create([
            'user_id' => $koor->id,
            'bulan' => ScriptVideoUpload::currentBulanName(),
            'tahun' => ScriptVideoUpload::currentTahun(),
            'judul' => 'Script Toggle', 'file_path' => 'toggle.png', 'file_name' => 'toggle.png', 'created_at' => now(),
        ]);

        $this->actingAs($koor);

        Livewire::test(ScriptVideoTable::class)
            ->call('toggleKontenDibuat', $upload->id);

        $this->assertDatabaseHas('script_video_uploads', ['id' => $upload->id, 'konten_dibuat' => false]);
    }

    public function test_video_animator_can_filter_by_game_category(): void
    {
        $bulan = ScriptVideoUpload::currentBulanName();
        $tahun = ScriptVideoUpload::currentTahun();
        $k = $this->koordinator();

        ScriptVideoUpload::create(['user_id' => $k->id, 'bulan' => $bulan, 'tahun' => $tahun, 'judul' => 'MLBB Konten', 'kategori_game' => 'MLBB', 'file_path' => 'mlbb.png', 'file_name' => 'mlbb.png', 'created_at' => now()]);
        ScriptVideoUpload::create(['user_id' => $k->id, 'bulan' => $bulan, 'tahun' => $tahun, 'judul' => 'Valorant Konten', 'kategori_game' => 'Valorant', 'file_path' => 'val.png', 'file_name' => 'val.png', 'created_at' => now()]);

        $this->actingAs($this->videoAnimator());

        Livewire::test(ScriptVideoTable::class)
            ->set('filterGame', 'MLBB')
            ->assertViewHas('uploads', fn ($uploads) => $uploads->total() === 1);
    }

    public function test_video_animator_badge_counts_all_scripts_this_month(): void
    {
        $k1 = $this->koordinator();

        foreach (range(1, 4) as $i) {
            ScriptVideoUpload::create([
                'user_id' => $k1->id,
                'bulan' => ScriptVideoUpload::currentBulanName(),
                'tahun' => ScriptVideoUpload::currentTahun(),
                'judul' => "Script {$i}", 'file_path' => "s-{$i}.png", 'file_name' => 'script.png', 'created_at' => now(),
            ]);
        }

        $this->actingAs($this->videoAnimator());

        Livewire::test(SidebarScriptVideoBadge::class)
            ->assertViewHas('badge', 4);
    }
}