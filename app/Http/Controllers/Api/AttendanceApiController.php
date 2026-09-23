<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttendanceSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    public function __construct(private readonly AttendanceSyncService $sync) {}

    public function push(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'punches' => 'required|array|max:500',
            'punches.*.machine_user_id' => 'required|string|max:20',
            'punches.*.punch_at' => 'required|date_format:Y-m-d H:i:s',
            'punches.*.method' => 'sometimes|string|max:20',
            'punches.*.machine_serial' => 'sometimes|nullable|string|max:50',
        ]);

        $processed = ['new' => 0, 'duplicate' => 0, 'unmatched' => 0];

        foreach ($validated['punches'] as $punch) {
            $result = $this->sync->recordPunch(
                $punch['machine_user_id'],
                $punch['punch_at'],
                $punch['method'] ?? 'finger',
                $punch['machine_serial'] ?? null,
            );

            match ($result['status']) {
                'ok' => $processed['new']++,
                'duplicate' => $processed['duplicate']++,
                'unmatched' => $processed['unmatched']++,
                default => null,
            };
        }

        $response = [
            'success' => true,
            'processed' => $processed,
        ];

        if ($processed['unmatched'] > 0) {
            $response['warning'] = 'Ada user mesin yang belum dipetakan ke karyawan. Jalankan `attendance:sync-users` untuk melihat daftarnya.';
        }

        return response()->json($response);
    }
}
