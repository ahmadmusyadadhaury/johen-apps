<?php

namespace Tests\Feature;

use App\Models\Office;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficeMasterTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_offices_are_available_with_their_attendance_methods(): void
    {
        $this->assertDatabaseCount('offices', 3);

        $summarecon = Office::where('code', Office::CODE_SUMMARECON_BANDUNG)->firstOrFail();
        $baleendah = Office::where('code', Office::CODE_BALEENDAH)->firstOrFail();
        $streamer = Office::where('code', Office::CODE_JOHEN_STREAMER)->firstOrFail();

        $this->assertTrue($summarecon->usesAttendanceMachine());
        $this->assertTrue($baleendah->usesWebFaceAttendance());
        $this->assertTrue($streamer->usesWebFaceAttendance());
    }

    public function test_active_offices_are_ordered_for_the_future_switcher(): void
    {
        $offices = Office::active()->orderBy('sort_order')->pluck('slug')->all();

        $this->assertSame([
            'summarecon-bandung',
            'baleendah',
            'johen-streamer',
        ], $offices);
    }
}
