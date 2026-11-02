<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use App\Models\Office;
use App\Models\User;
use App\Models\UserFaceTemplate;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class DailyFaceAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private function streamer(): array
    {
        $office = Office::query()->where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();
        $office->update([
            'attendance_latitude' => -6.9175000,
            'attendance_longitude' => 107.6191000,
            'attendance_radius_meters' => 100,
        ]);
        $employee = Employee::query()->create([
            'office_id' => $office->id,
            'nik' => 'STREAM-001',
            'nama' => 'Streamer Test',
            'position' => 'Streamer',
            'tipe' => Employee::TIPE_KARYAWAN_AKTIF,
        ]);
        $user = User::factory()->create([
            'role' => User::ROLE_STREAMER,
            'employee_id' => $employee->id,
        ]);
        $user->offices()->attach($office->id, ['is_default' => true]);

        return [$user, $employee];
    }

    public function test_streamer_can_check_in_and_out_with_registered_face(): void
    {
        Carbon::setTestNow('2026-10-30 08:00:00');
        [$user, $employee] = $this->streamer();
        $embedding = array_fill(0, 1024, 0.01);
        UserFaceTemplate::query()->create([
            'user_id' => $user->id,
            'encrypted_template' => Crypt::encryptString(json_encode([$embedding], JSON_THROW_ON_ERROR)),
            'model_version' => 'human-faceres',
        ]);

        $this->actingAs($user)
            ->postJson(route('hris.absensi.face.attend'), [
                'embeddings' => [$embedding],
                'latitude' => -6.9175000,
                'longitude' => 107.6191000,
                'accuracy' => 10,
            ])
            ->assertOk()
            ->assertJsonPath('action', 'check_in');

        Carbon::setTestNow('2026-10-30 17:00:00');
        $this->actingAs($user)
            ->postJson(route('hris.absensi.face.attend'), [
                'embeddings' => [$embedding],
                'latitude' => -6.9175000,
                'longitude' => 107.6191000,
                'accuracy' => 10,
            ])
            ->assertOk()
            ->assertJsonPath('action', 'check_out');

        $attendance = Attendance::query()->where('employee_id', $employee->id)->firstOrFail();
        $this->assertSame('08:00:00', $attendance->time_in);
        $this->assertSame('17:00:00', $attendance->time_out);
        $this->assertSame('face', $attendance->method);
        $this->assertSame(2, AttendancePunch::query()->where('employee_id', $employee->id)->count());
    }

    public function test_face_attendance_card_is_shown_on_operational_attendance_page(): void
    {
        [$user] = $this->streamer();

        $this->actingAs($user)
            ->get(route('hris.absensi'))
            ->assertOk()
            ->assertSee('Presensi Harian Face ID')
            ->assertSee('Absen Masuk dengan Face ID');
    }

    public function test_machine_office_cannot_use_daily_face_attendance(): void
    {
        $office = Office::query()->where('code', Office::CODE_SUMMARECON_BANDUNG)->firstOrFail();
        $employee = Employee::query()->create([
            'office_id' => $office->id,
            'nik' => 'MACHINE-001',
            'nama' => 'Machine Employee',
            'tipe' => Employee::TIPE_KARYAWAN_AKTIF,
        ]);
        $user = User::factory()->create(['employee_id' => $employee->id]);

        $this->actingAs($user)
            ->getJson(route('hris.absensi.face.status'))
            ->assertForbidden();
    }

    public function test_face_attendance_is_rejected_outside_office_radius(): void
    {
        [$user] = $this->streamer();
        $embedding = array_fill(0, 1024, 0.01);
        UserFaceTemplate::query()->create([
            'user_id' => $user->id,
            'encrypted_template' => Crypt::encryptString(json_encode([$embedding], JSON_THROW_ON_ERROR)),
            'model_version' => 'human-faceres',
        ]);

        $this->actingAs($user)
            ->postJson(route('hris.absensi.face.attend'), [
                'embeddings' => [$embedding],
                'latitude' => -6.9275000,
                'longitude' => 107.6191000,
                'accuracy' => 10,
            ])
            ->assertForbidden()
            ->assertJsonFragment(['message' => 'Lokasi Anda berada di luar radius kantor (jarak 1112 meter).']);

        $this->assertDatabaseCount('attendances', 0);
    }
}
