<?php

namespace App\Http\Controllers;

use App\Events\WeeklyMeetingAttendanceCreated;
use App\Models\UserFaceTemplate;
use App\Models\WeeklyMeeting;
use App\Models\WeeklyMeetingAttendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FaceAttendanceController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        return response()->json(['registered' => UserFaceTemplate::where('user_id', $request->user()->id)->exists()]);
    }

    public function preflight(Request $request): JsonResponse
    {
        $eligibility = $this->attendanceEligibility($request->user(), Carbon::now());

        return response()->json([
            'can_attend' => $eligibility['message'] === null,
            'message' => $eligibility['message'] ?? 'Presensi dapat dilanjutkan. Silakan verifikasi wajah dan lokasi.',
            'meeting' => $eligibility['meeting'] ? [
                'title' => $eligibility['meeting']->title,
                'starts_at' => $eligibility['meeting']->start_time?->format('H:i'),
                'ends_at' => $eligibility['meeting']->end_time?->format('H:i'),
            ] : null,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate(['embedding' => 'required|array|size:1024', 'embedding.*' => 'required|numeric|between:-10,10']);
        $template = array_map('floatval', $data['embedding']);
        try {
            UserFaceTemplate::create([
                'user_id' => $request->user()->id,
                'encrypted_template' => Crypt::encryptString(json_encode($template, JSON_THROW_ON_ERROR)),
                'model_version' => 'human-faceres',
            ]);
        } catch (\Illuminate\Database\QueryException $exception) {
            if (UserFaceTemplate::where('user_id', $request->user()->id)->exists()) {
                return response()->json(['message' => 'Wajah sudah terdaftar pada akun ini.'], 409);
            }
            throw $exception;
        }
        return response()->json(['success' => true, 'message' => 'Wajah berhasil didaftarkan. Foto kamera tidak disimpan.']);
    }

    public function attend(Request $request, WeeklyMeetingController $meetings): JsonResponse
    {
        $data = $request->validate([
            'embedding' => 'required|array|size:1024', 'embedding.*' => 'required|numeric|between:-10,10',
            'latitude' => 'required|numeric|between:-90,90', 'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:0|max:100',
        ]);
        $user = $request->user();
        $now = Carbon::now();
        $eligibility = $this->attendanceEligibility($user, $now);
        abort_if($eligibility['message'] !== null, 422, $eligibility['message']);
        $employee = $eligibility['employee'];
        $meeting = $eligibility['meeting'];
        $stored = UserFaceTemplate::where('user_id', $user->id)->first();
        abort_unless($stored, 422, 'Daftarkan wajah pada akun Anda terlebih dahulu.');
        $known = json_decode(Crypt::decryptString($stored->encrypted_template), true, flags: JSON_THROW_ON_ERROR);
        $similarity = $this->similarity($known, array_map('floatval', $data['embedding']));
        abort_if($similarity < 0.5, 403, 'Wajah tidak cocok dengan data akun Anda. Coba lagi dengan pencahayaan yang cukup.');
        $distance = $meetings->distanceBetween((float) $meeting->latitude, (float) $meeting->longitude, (float) $data['latitude'], (float) $data['longitude']);
        abort_if($distance > $meeting->radius_meters, 403, 'Lokasi Anda berada di luar radius rapat ('.round($distance).' m).');

        $attendance = DB::transaction(function () use ($meeting, $employee, $data, $now, $distance) {
            $locked = WeeklyMeeting::whereKey($meeting->id)->lockForUpdate()->firstOrFail();
            abort_if(WeeklyMeetingAttendance::where('weekly_meeting_id', $locked->id)->where('employee_id', $employee->id)->exists(), 409, 'Anda sudah absen pada rapat ini.');
            return WeeklyMeetingAttendance::create([
                'weekly_meeting_id' => $locked->id, 'employee_id' => $employee->id, 'attended_at' => $now,
                'method' => 'face_location', 'device_location' => round($distance).' m dari titik rapat (akurasi ±'.round((float) $data['accuracy']).' m)',
                'latitude' => $data['latitude'], 'longitude' => $data['longitude'],
            ]);
        });
        try { WeeklyMeetingAttendanceCreated::dispatch($attendance); } catch (\Throwable $exception) { report($exception); }
        return response()->json(['success' => true, 'message' => 'Absen berhasil! Selamat datang, '.$employee->nama, 'attended_at' => $attendance->attended_at->format('H:i:s')]);
    }

    private function similarity(array $left, array $right): float
    {
        if (count($left) !== 1024 || count($right) !== 1024) throw ValidationException::withMessages(['embedding' => 'Data wajah tidak valid.']);
        $sum = 0.0;
        foreach ($left as $index => $value) $sum += ((float) $value - (float) $right[$index]) ** 2;
        // Human faceres maps Euclidean distance using multiplier 25 and caps similarity to [0.2, 0.8].
        return max(0.2, min(0.8, 1 - sqrt($sum) * 25));
    }

    private function attendanceEligibility(\App\Models\User $user, Carbon $now): array
    {
        $meeting = WeeklyMeeting::where('is_active', true)
            ->whereDate('meeting_date', $now->toDateString())
            ->where(fn ($query) => $query->whereNull('start_time')->orWhereTime('start_time', '<=', $now->toTimeString()))
            ->where(fn ($query) => $query->whereNull('end_time')->orWhereTime('end_time', '>', $now->toTimeString()))
            ->orderBy('start_time')->first();

        if (!$meeting) {
            return ['meeting' => null, 'employee' => $user->employee, 'message' => 'Belum bisa melakukan presensi saat ini. Tidak ada weekly meeting yang sedang berlangsung.'];
        }

        $employee = $user->employee;
        if (!$employee) {
            return ['meeting' => $meeting, 'employee' => null, 'message' => 'Akun Anda belum terhubung ke data karyawan.'];
        }

        $attendance = WeeklyMeetingAttendance::where('weekly_meeting_id', $meeting->id)
            ->where('employee_id', $employee->id)->first();
        if ($attendance) {
            return ['meeting' => $meeting, 'employee' => $employee, 'message' => 'Anda sudah absen pada rapat ini pada '.$attendance->attended_at->format('H:i:s').'.'];
        }

        if ($meeting->latitude === null || $meeting->longitude === null || !$meeting->radius_meters) {
            return ['meeting' => $meeting, 'employee' => $employee, 'message' => 'Presensi belum bisa dilakukan. Admin belum mengatur titik lokasi dan radius rapat.'];
        }

        if (!UserFaceTemplate::where('user_id', $user->id)->exists()) {
            return ['meeting' => $meeting, 'employee' => $employee, 'message' => 'Presensi belum bisa dilakukan. Daftarkan wajah pada akun Anda terlebih dahulu.'];
        }

        return ['meeting' => $meeting, 'employee' => $employee, 'message' => null];
    }
}
