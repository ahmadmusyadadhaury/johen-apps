<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Storage;

class LeaveMedicalCertificateController extends Controller
{
    public function show(LeaveRequest $leaveRequest)
    {
        abort_unless($leaveRequest->jenis === 'izin' && $leaveRequest->perihal === 'Sakit' && $leaveRequest->persetujuan_hr === 'disetujui', 404);
        abort_unless($leaveRequest->surat_dokter_path && Storage::disk('local')->exists($leaveRequest->surat_dokter_path), 404);

        $user = auth()->user();
        $employeeId = $user->employee?->id;
        $isHr = $user->isStaffHr() || $user->employee?->positions()
            ->whereIn('nama', ['Human Resource Generalist', 'Admin HR', 'Admin GA', 'Office Boy'])
            ->exists();

        abort_unless(
            $employeeId === $leaveRequest->employee_id
                || $employeeId === $leaveRequest->atasan_id
                || $employeeId === $leaveRequest->atasan2_id
                || $isHr,
            403
        );

        return Storage::disk('local')->response($leaveRequest->surat_dokter_path, null, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ], 'inline');
    }
}
