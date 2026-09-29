<?php

namespace Tests\Feature;

use App\Models\AttendancePunch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AttendancePushTest extends TestCase
{
    use RefreshDatabase;

    protected function enablePush(array $overrides = []): void
    {
        config([
            'services.attendance_cloud' => array_merge([
                'url' => 'https://cloud.test/api/attendance/push',
                'token' => 'test-token',
                'enabled' => true,
                'verify_ssl' => true,
            ], $overrides),
        ]);
    }

    private function punch(string $machineUserId, string $punchAt): AttendancePunch
    {
        return AttendancePunch::create([
            'machine_user_id' => $machineUserId,
            'punch_at' => $punchAt,
            'method' => 'mesin',
            'machine_serial' => 'SN-1',
        ]);
    }

    public function test_it_does_nothing_when_cloud_sync_is_disabled(): void
    {
        config(['services.attendance_cloud' => ['url' => null, 'token' => null, 'enabled' => false]]);
        Http::fake();

        $punch = $this->punch('58', '2026-09-01 07:00:00');

        $this->artisan('attendance:push')->assertSuccessful();

        Http::assertNothingSent();
        $this->assertNull($punch->fresh()->pushed_at);
    }

    public function test_it_sends_unsynced_punches_and_marks_them_pushed(): void
    {
        $this->enablePush();
        Http::fake(['*' => Http::response([
            'success' => true,
            'processed' => ['new' => 1, 'duplicate' => 0, 'unmatched' => 0],
        ])]);

        $punch = $this->punch('58', '2026-09-01 07:00:00');

        $this->artisan('attendance:push')->assertSuccessful();

        Http::assertSent(function (Request $request) {
            $body = $request->data();

            return $request->url() === 'https://cloud.test/api/attendance/push'
                && $request->hasHeader('Authorization', 'Bearer test-token')
                && count($body['punches']) === 1
                && $body['punches'][0]['machine_user_id'] === '58'
                && $body['punches'][0]['punch_at'] === '2026-09-01 07:00:00'
                && $body['punches'][0]['method'] === 'mesin'
                && $body['punches'][0]['machine_serial'] === 'SN-1';
        });

        $this->assertNotNull($punch->fresh()->pushed_at);
    }

    public function test_it_skips_punches_that_were_already_pushed(): void
    {
        $this->enablePush();
        Http::fake(['*' => Http::response([
            'success' => true,
            'processed' => ['new' => 0, 'duplicate' => 0, 'unmatched' => 0],
        ])]);

        // pushed_at tidak ada di $fillable, jadi harus lewat query builder.
        $alreadyPushed = $this->punch('58', '2026-09-01 07:00:00');
        AttendancePunch::whereKey($alreadyPushed->id)->update(['pushed_at' => now()->subDay()]);

        $this->artisan('attendance:push')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_dry_run_does_not_send_or_mark_anything(): void
    {
        $this->enablePush();
        Http::fake();

        $punch = $this->punch('58', '2026-09-01 07:00:00');

        $this->artisan('attendance:push --dry-run')->assertSuccessful();

        Http::assertNothingSent();
        $this->assertNull($punch->fresh()->pushed_at);
    }

    public function test_permanent_http_failure_does_not_mark_punches_and_reports_failure(): void
    {
        Log::spy();

        $this->enablePush();
        Http::fake(['*' => Http::response('Unauthenticated.', 401)]);

        $punch = $this->punch('58', '2026-09-01 07:00:00');

        $this->artisan('attendance:push')->assertFailed();

        $this->assertNull($punch->fresh()->pushed_at, 'Punch gagal push tidak boleh ditandai pushed_at agar dicoba lagi.');
        Log::shouldHaveReceived('error');
    }

    public function test_missing_endpoint_is_reported_as_failure_and_punches_stay_pending(): void
    {
        Log::spy();

        $this->enablePush();
        Http::fake(['*' => Http::response('Not Found', 404)]);

        $punch = $this->punch('58', '2026-09-01 07:00:00');

        $this->artisan('attendance:push')->assertFailed();

        $this->assertNull($punch->fresh()->pushed_at);
        Log::shouldHaveReceived('error');
    }

    public function test_transient_server_error_leaves_punches_pending_for_retry(): void
    {
        $this->enablePush();
        Http::fake(['*' => Http::response('Server Error', 500)]);

        $punch = $this->punch('58', '2026-09-01 07:00:00');

        $this->artisan('attendance:push')->assertFailed();

        $this->assertNull($punch->fresh()->pushed_at);
    }

    public function test_since_option_filters_by_punch_date(): void
    {
        $this->enablePush();
        Http::fake(['*' => Http::response([
            'success' => true,
            'processed' => ['new' => 1, 'duplicate' => 0, 'unmatched' => 0],
        ])]);

        $old = $this->punch('1', '2026-08-01 07:00:00');
        $recent = $this->punch('2', '2026-09-20 07:00:00');

        $this->artisan('attendance:push --since=2026-09-01')->assertSuccessful();

        Http::assertSent(fn (Request $request) => collect($request->data()['punches'])
            ->pluck('machine_user_id')
            ->all() === ['2']);

        $this->assertNull($old->fresh()->pushed_at);
        $this->assertNotNull($recent->fresh()->pushed_at);
    }
}
