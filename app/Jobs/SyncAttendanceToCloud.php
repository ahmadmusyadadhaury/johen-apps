<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncAttendanceToCloud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 120;

    public function handle(): void
    {
        $lock = Cache::lock('attendance:push:job', 60);

        if (! $lock->get()) {
            return;
        }

        try {
            $exitCode = Artisan::call('attendance:push');

            if ($exitCode !== 0) {
                Log::warning('SyncAttendanceToCloud: attendance:push mengembalikan exit code non-zero.', [
                    'exit_code' => $exitCode,
                    'output' => str(Artisan::output())->limit(500)->value(),
                ]);
            }
        } finally {
            optional($lock)->release();
        }
    }
}
