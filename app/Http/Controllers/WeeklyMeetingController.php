<?php

namespace App\Http\Controllers;

use App\Models\WeeklyMeeting;
use App\Models\WeeklyMeetingAttendance;
use App\Models\Employee;
use App\Events\WeeklyMeetingAttendanceCreated;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WeeklyMeetingController extends Controller
{
    public function generateQr(WeeklyMeeting $weeklyMeeting)
    {
        $qrCode = $weeklyMeeting->generateQrCode();
        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'message' => 'QR Code berhasil digenerate.',
        ]);
    }

    public function attend(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $today = Carbon::today();
        $meeting = WeeklyMeeting::where('meeting_date', $today)
            ->where('is_active', true)
            ->first();

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada rapat mingguan aktif hari ini.',
            ], 404);
        }

        if ($request->qr_code !== $meeting->qr_code) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid untuk rapat ini.',
            ], 400);
        }

        $employee = auth()->user()->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung ke data karyawan.',
            ], 400);
        }

        $existing = WeeklyMeetingAttendance::where('weekly_meeting_id', $meeting->id)
            ->where('employee_id', $employee->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pada rapat ini.',
                'attended_at' => $existing->attended_at->format('H:i:s'),
            ], 400);
        }

        $attendance = WeeklyMeetingAttendance::create([
            'weekly_meeting_id' => $meeting->id,
            'employee_id' => $employee->id,
            'attended_at' => Carbon::now(),
            'method' => 'qr_scan',
        ]);

        // Broadcast real-time update to admin master
        // ShouldBroadcastNow -> synchronous delivery to Reverb; try/catch so an
        // outage never breaks the attend response after the record is already saved.
        try {
            WeeklyMeetingAttendanceCreated::dispatch($attendance);
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'message' => 'Absen berhasil! Selamat datang, ' . $employee->nama,
            'attended_at' => $attendance->attended_at->format('H:i:s'),
        ]);
    }
}