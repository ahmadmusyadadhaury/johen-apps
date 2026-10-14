<?php

namespace App\Http\Controllers;

use App\Models\WeeklyMeeting;
use App\Models\WeeklyMeetingAttendance;
use App\Models\Employee;
use App\Events\WeeklyMeetingAttendanceCreated;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\PasskeyService;
use Illuminate\Support\Facades\DB;

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

    public function attendWithPasskey(Request $request, PasskeyService $passkeys)
    {
        $data = $request->validate([
            'credential' => 'required|array',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:0|max:100',
        ]);
        $user = $request->user();
        $employee = $user?->employee;
        abort_unless($employee, 403, 'Akun Anda tidak terhubung ke data karyawan.');

        $passkeys->verify($user, $data['credential']);

        $now = Carbon::now();
        $meeting = WeeklyMeeting::where('is_active', true)
            ->whereDate('meeting_date', $now->toDateString())
            ->where(function ($q) use ($now) {
                $q->whereNull('start_time')->orWhereTime('start_time', '<=', $now->toTimeString());
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_time')->orWhereTime('end_time', '>', $now->toTimeString());
            })
            ->orderBy('start_time')->first();
        abort_unless($meeting, 404, 'Tidak ada weekly meeting aktif saat ini.');
        abort_unless($meeting->latitude !== null && $meeting->longitude !== null && $meeting->radius_meters > 0, 422, 'Lokasi dan radius rapat belum diatur.');

        $distance = $this->distanceMeters((float) $meeting->latitude, (float) $meeting->longitude, (float) $data['latitude'], (float) $data['longitude']);
        abort_if($distance > $meeting->radius_meters, 403, 'Lokasi Anda berada di luar radius rapat ('.round($distance).' m).');

        $attendance = DB::transaction(function () use ($meeting, $employee, $data, $now) {
            $lockedMeeting = WeeklyMeeting::whereKey($meeting->id)->lockForUpdate()->firstOrFail();
            abort_if(WeeklyMeetingAttendance::where('weekly_meeting_id', $lockedMeeting->id)->where('employee_id', $employee->id)->exists(), 409, 'Anda sudah absen pada rapat ini.');
            return WeeklyMeetingAttendance::create([
                'weekly_meeting_id' => $lockedMeeting->id,
                'employee_id' => $employee->id,
                'attended_at' => $now,
                'method' => 'passkey_location',
                'device_location' => round($this->distanceMeters((float) $lockedMeeting->latitude, (float) $lockedMeeting->longitude, (float) $data['latitude'], (float) $data['longitude'])).' m dari titik rapat (akurasi ±'.round((float) $data['accuracy']).' m)',
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]);
        });

        try { WeeklyMeetingAttendanceCreated::dispatch($attendance); } catch (\Throwable $e) { report($e); }
        return response()->json(['success' => true, 'message' => 'Absen berhasil! Selamat datang, '.$employee->nama, 'attended_at' => $attendance->attended_at->format('H:i:s')]);
    }

    private function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earth = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $earth * 2 * asin(sqrt($a));
    }
}
