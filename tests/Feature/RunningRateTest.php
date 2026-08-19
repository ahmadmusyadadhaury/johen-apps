<?php

namespace Tests\Feature;

use App\Livewire\RunningRateDashboard;
use App\Models\Employee;
use App\Models\Position;
use App\Models\RunningRateDailySold;
use App\Models\RunningRateHostTarget;
use App\Models\RunningRatePeriod;
use App\Models\User;
use App\Services\RunningRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class RunningRateTest extends TestCase
{
    use RefreshDatabase;

    private function koordinatorFf(): User
    {
        return User::factory()->create(['role' => User::ROLE_KOORDINATOR_FF]);
    }

    private function staffHostFf(): User
    {
        return User::factory()->create(['role' => User::ROLE_STAFF_HOST_FF]);
    }

    private function employee(string $nik, string $nama): Employee
    {
        return Employee::create([
            'nik' => $nik,
            'nama' => $nama,
            'status' => 'aktif',
        ]);
    }

    private function period(string $mulai, string $selesai): RunningRatePeriod
    {
        return RunningRatePeriod::create([
            'divisi' => 'Free Fire',
            'nama' => 'Agustus 2026',
            'tanggal_mulai' => Carbon::parse($mulai),
            'tanggal_selesai' => Carbon::parse($selesai),
            'is_active' => true,
        ]);
    }

    public function test_summary_metrics_match_expected_prompt_numbers(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');

        $maul = $this->employee('26030035', 'Fiqri Mauludin');
        $selvi = $this->employee('26030042', 'Selvianti Amalia');
        $tiwi = $this->employee('26030064', 'Pratiwi Audina Wijaya');
        $rian = $this->employee('26030070', 'Rian Ardianysah');

        $targets = [
            $maul->id => 15,
            $selvi->id => 18,
            $tiwi->id => 7,
            $rian->id => 7,
        ];
        foreach ($targets as $hostId => $target) {
            $period->targets()->create(['host_id' => $hostId, 'target' => $target]);
        }

        $solds = [
            $maul->id => [['2026-08-10', 2], ['2026-08-11', 3], ['2026-08-12', 3]],
            $selvi->id => [['2026-08-10', 5], ['2026-08-11', 6], ['2026-08-12', 5]],
            $tiwi->id => [['2026-08-10', 1], ['2026-08-11', 2], ['2026-08-12', 2]],
            $rian->id => [['2026-08-11', 1], ['2026-08-12', 2]],
        ];
        foreach ($solds as $hostId => $rows) {
            foreach ($rows as [$tanggal, $sold]) {
                $period->dailySolds()->create(['host_id' => $hostId, 'tanggal' => $tanggal, 'sold' => $sold]);
            }
        }

        $asOf = Carbon::parse('2026-08-12');
        $summary = app(RunningRateService::class)->summary($period, asOf: $asOf);

        $this->assertSame(20, $summary['remaining_working_days']);
        $this->assertEqualsWithDelta(47, $summary['total_target'], 0.01);
        $this->assertEqualsWithDelta(32, $summary['total_sold'], 0.01);
        $this->assertEqualsWithDelta(68.09, $summary['achievement'], 0.01);
        $this->assertEqualsWithDelta(15, $summary['remaining'], 0.01);
        $this->assertEqualsWithDelta(0.75, $summary['rr_daily'], 0.01);
        $this->assertEqualsWithDelta(3.75, $summary['rr_weekly'], 0.01);
    }

    public function test_host_metrics_match_expected_prompt_numbers(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');
        $maul = $this->employee('26030035', 'Fiqri Mauludin');
        $period->targets()->create(['host_id' => $maul->id, 'target' => 15]);
        foreach ([['2026-08-10', 2], ['2026-08-11', 3], ['2026-08-12', 3]] as [$tanggal, $sold]) {
            $period->dailySolds()->create(['host_id' => $maul->id, 'tanggal' => $tanggal, 'sold' => $sold]);
        }

        $metrics = app(RunningRateService::class)->hostMetrics($period, $maul->id, Carbon::parse('2026-08-12'));

        $this->assertSame(20, $metrics['remaining_working_days']);
        $this->assertEqualsWithDelta(15, $metrics['target'], 0.01);
        $this->assertEqualsWithDelta(8, $metrics['sold'], 0.01);
        $this->assertEqualsWithDelta(53.33, $metrics['achievement'], 0.01);
        $this->assertEqualsWithDelta(7, $metrics['remaining'], 0.01);
        $this->assertEqualsWithDelta(0.35, $metrics['rr_daily'], 0.01);
        $this->assertEqualsWithDelta(1.75, $metrics['rr_weekly'], 0.01);
    }

    public function test_remaining_days_counts_all_calendar_days(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');
        $service = app(RunningRateService::class);

        $this->assertSame(31, $service->totalWorkingDays($period));
        $this->assertSame(13, $service->remainingWorkingDays($period, Carbon::parse('2026-08-19')));
    }

    public function test_koordinator_can_input_sold(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');
        $maul = $this->employee('26030035', 'Fiqri Mauludin');
        $period->targets()->create(['host_id' => $maul->id, 'target' => 15]);

        Livewire::actingAs($this->koordinatorFf())
            ->test(RunningRateDashboard::class)
            ->assertOk()
            ->call('openSoldModal')
            ->set('soldTanggal', '2026-08-19')
            ->set('soldHost', (string) $maul->id)
            ->set('soldValue', '2')
            ->call('saveSold')
            ->assertHasNoErrors();

        $record = RunningRateDailySold::where('period_id', $period->id)->where('host_id', $maul->id)->first();
        $this->assertNotNull($record);
        $this->assertSame('2026-08-19', $record->tanggal->toDateString());
        $this->assertEqualsWithDelta(2, (float) $record->sold, 0.01);
    }

    public function test_duplicate_sold_for_same_host_and_date_updates_record(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');
        $maul = $this->employee('26030035', 'Fiqri Mauludin');
        $period->targets()->create(['host_id' => $maul->id, 'target' => 15]);
        $period->dailySolds()->create(['host_id' => $maul->id, 'tanggal' => '2026-08-19', 'sold' => 2]);

        Livewire::actingAs($this->koordinatorFf())
            ->test(RunningRateDashboard::class)
            ->call('openSoldModal')
            ->set('soldTanggal', '2026-08-19')
            ->set('soldHost', (string) $maul->id)
            ->set('soldValue', '3')
            ->call('saveSold')
            ->assertHasNoErrors();

        $this->assertSame(1, RunningRateDailySold::count());

        $record = RunningRateDailySold::first();
        $this->assertSame('2026-08-19', $record->tanggal->toDateString());
        $this->assertEqualsWithDelta(3, (float) $record->sold, 0.01);
    }

    public function test_koordinator_role_division_takes_priority_over_auxiliary_host_position(): void
    {
        $rootFf = Position::create(['nama' => 'Koordinator Free Fire']);
        $hostFf = Position::create(['nama' => 'Host Free Fire (Pagi)', 'parent_id' => $rootFf->id]);
        $rootPubg = Position::create(['nama' => 'Koordinator Johen PUBG']);
        $hostPubg = Position::create(['nama' => 'Host Johen PUBG (Siang)', 'parent_id' => $rootPubg->id]);
        $hostPubgSubuh = Position::create(['nama' => 'Host Johen PUBG (Subuh)', 'parent_id' => $rootPubg->id]);

        $rafly = $this->employee('26030014', 'Muhamad Rafly Firdaus');
        $rafly->positions()->attach([$rootFf->id, $hostPubg->id]);

        $host = $this->employee('26030035', 'Fiqri Mauludin');
        $host->positions()->attach($hostFf->id);

        $pubgHost = $this->employee('26030041', 'Eben Haizer');
        $pubgHost->positions()->attach($hostPubgSubuh->id);

        $user = User::factory()->create([
            'role' => User::ROLE_KOORDINATOR_FF,
            'employee_id' => $rafly->id,
        ]);

        $this->assertSame('Free Fire', $user->getRoleDivisionName());

        $ffHosts = RunningRateService::hostsForDivision('Free Fire');
        $this->assertTrue($ffHosts->pluck('id')->doesntContain($rafly->id));
        $this->assertTrue($ffHosts->pluck('id')->contains($host->id));

        $period = $this->period('2026-08-01', '2026-08-31');
        $period->targets()->create(['host_id' => $host->id, 'target' => 15]);

        Livewire::actingAs($user)
            ->test(RunningRateDashboard::class)
            ->assertOk()
            ->assertSee('Fiqri Mauludin')
            ->assertDontSee('Eben Haizer')
            ->assertDontSee('Muhamad Rafly Firdaus');
    }

    public function test_koordinator_can_edit_host_target_and_sold_for_selected_date(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');
        $maul = $this->employee('26030035', 'Fiqri Mauludin');
        $period->targets()->create(['host_id' => $maul->id, 'target' => 15]);

        Livewire::actingAs($this->koordinatorFf())
            ->test(RunningRateDashboard::class)
            ->assertOk()
            ->set('tanggalFilter', '2026-08-20')
            ->call('openEditTargetModal', $maul->id)
            ->set('targetValue', '20')
            ->set('soldValue', '4')
            ->call('saveTarget')
            ->assertHasNoErrors();

        $this->assertEqualsWithDelta(20, (float) $period->targets()->where('host_id', $maul->id)->value('target'), 0.01);

        $record = RunningRateDailySold::where('period_id', $period->id)
            ->where('host_id', $maul->id)
            ->whereDate('tanggal', '2026-08-20')
            ->first();
        $this->assertNotNull($record);
        $this->assertEqualsWithDelta(4, (float) $record->sold, 0.01);
    }

    public function test_koordinator_can_edit_host_target_and_sold(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');
        $maul = $this->employee('26030035', 'Fiqri Mauludin');
        $period->targets()->create(['host_id' => $maul->id, 'target' => 15]);

        Livewire::actingAs($this->koordinatorFf())
            ->test(RunningRateDashboard::class)
            ->call('openEditTargetModal', $maul->id)
            ->set('targetValue', '20')
            ->set('soldValue', '4')
            ->call('saveTarget')
            ->assertHasNoErrors();

        $this->assertEqualsWithDelta(20, (float) $period->targets()->where('host_id', $maul->id)->value('target'), 0.01);

        $record = RunningRateDailySold::where('period_id', $period->id)
            ->where('host_id', $maul->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();
        $this->assertNotNull($record);
        $this->assertEqualsWithDelta(4, (float) $record->sold, 0.01);
    }

    public function test_koordinator_can_delete_host_data(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');
        $maul = $this->employee('26030035', 'Fiqri Mauludin');
        $period->targets()->create(['host_id' => $maul->id, 'target' => 15]);
        $period->dailySolds()->create(['host_id' => $maul->id, 'tanggal' => '2026-08-19', 'sold' => 2]);

        Livewire::actingAs($this->koordinatorFf())
            ->test(RunningRateDashboard::class)
            ->call('confirmDeleteHost', $maul->id)
            ->call('executeDeleteHost');

        $this->assertSame(0, RunningRateHostTarget::count());
        $this->assertSame(0, RunningRateDailySold::count());
    }

    public function test_staff_host_cannot_manage_sold(): void
    {
        $period = $this->period('2026-08-01', '2026-08-31');
        $maul = $this->employee('26030035', 'Fiqri Mauludin');
        $period->targets()->create(['host_id' => $maul->id, 'target' => 15]);

        Livewire::actingAs($this->staffHostFf())
            ->test(RunningRateDashboard::class)
            ->assertOk()
            ->call('openSoldModal')
            ->assertForbidden();

        $this->assertSame(0, RunningRateDailySold::count());
    }

    public function test_koordinator_other_games_scope_to_their_division(): void
    {
        $cases = [
            User::ROLE_KOORDINATOR_PUBG => 'PUBG',
            User::ROLE_KOORDINATOR_MLBB => 'MLBB',
            User::ROLE_KOORDINATOR_EFOOTBALL => 'E-football',
            User::ROLE_KOORDINATOR_VALORANT => 'Valorant',
            User::ROLE_KOORDINATOR_ROBLOX => 'Roblox',
            User::ROLE_KOORDINATOR_MONKEY_PUBG => 'Monkey PUBG',
        ];

        $nik = 26039901;
        foreach ($cases as $role => $division) {
            $user = User::factory()->create(['role' => $role]);

            $host = $this->employee((string) $nik++, 'Host ' . $division);
            $period = RunningRatePeriod::create([
                'divisi' => $division,
                'nama' => 'Agustus 2026',
                'tanggal_mulai' => Carbon::parse('2026-08-01'),
                'tanggal_selesai' => Carbon::parse('2026-08-31'),
                'is_active' => true,
            ]);
            $period->targets()->create(['host_id' => $host->id, 'target' => 10]);

            Livewire::actingAs($user)
                ->test(RunningRateDashboard::class)
                ->assertOk()
                ->assertSee('Host ' . $division);
        }
    }

    public function test_koordinator_cannot_view_other_division_via_query(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_KOORDINATOR_EFOOTBALL]);

        Livewire::actingAs($user)
            ->withQueryParams(['divisi' => 'Free Fire'])
            ->test(RunningRateDashboard::class)
            ->assertForbidden();
    }

    public function test_fc_mobile_koordinator_position_holder_can_manage_fc_mobile(): void
    {
        $rootFc = Position::create(['nama' => 'Koordinator FC Mobile']);
        $hostFc = Position::create(['nama' => 'Host FC Mobile (Pagi)', 'parent_id' => $rootFc->id]);

        $dhika = $this->employee('26039910', 'Dhika FC Mobile');
        $dhika->positions()->attach($rootFc->id);

        $host = $this->employee('26039911', 'Host FC Mobile A');
        $host->positions()->attach($hostFc->id);

        $user = User::factory()->create([
            'role' => User::ROLE_STAFF_HOST_MLBB,
            'employee_id' => $dhika->id,
        ]);

        $period = RunningRatePeriod::create([
            'divisi' => 'FC Mobile',
            'nama' => 'Agustus 2026',
            'tanggal_mulai' => Carbon::parse('2026-08-01'),
            'tanggal_selesai' => Carbon::parse('2026-08-31'),
            'is_active' => true,
        ]);
        $period->targets()->create(['host_id' => $host->id, 'target' => 10]);

        Livewire::actingAs($user)
            ->withQueryParams(['divisi' => 'FC Mobile'])
            ->test(RunningRateDashboard::class)
            ->assertOk()
            ->assertSee('Host FC Mobile A')
            ->call('openSoldModal')
            ->assertOk();
    }
}
