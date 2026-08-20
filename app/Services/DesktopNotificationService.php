<?php

namespace App\Services;

use App\Models\DesktopNotification;
use App\Models\Position;

class DesktopNotificationService
{
    /**
     * Kirim notifikasi desktop ke semua karyawan yang memegang posisi tertentu.
     * Dipakai saat evaluasi/situasi disimpan untuk sebuah jabatan.
     *
     * @return int jumlah notifikasi baru yang dibuat
     */
    public function pushToPosition(Position $position, string $title, string $body, array $data = []): int
    {
        $employeeIds = $position->employees->pluck('id');

        if ($employeeIds->isEmpty()) {
            return 0;
        }

        $noteId = $data['position_note_id'] ?? null;
        $existing = collect();
        if ($noteId) {
            $existing = DesktopNotification::whereIn('employee_id', $employeeIds)
                ->where('data->position_note_id', $noteId)
                ->pluck('employee_id');
        }

        $created = 0;
        foreach ($employeeIds as $employeeId) {
            if ($existing->contains($employeeId)) {
                continue;
            }

            DesktopNotification::create([
                'employee_id' => $employeeId,
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);
            $created++;
        }

        return $created;
    }
}