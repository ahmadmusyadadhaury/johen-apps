<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use App\Models\UserFaceTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class DailyFaceAttendanceController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $employee = $this->eligibleEmployee($request);
        $template = UserFaceTemplate::query()->where('user_id', $request->user()->id)->first();
        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('date', $employee->resolveWorkDate())
            ->first();

        return response()->json([
            'registered' => $template && $template->model_version !== 'human-faceres-pending',
            'action' => $this->nextAction($attendance),
            'time_in' => $attendance?->time_in,
            'time_out' => $attendance?->time_out,
            'office' => $employee->office?->name,
            'location_configured' => $employee->office?->hasAttendanceLocation() ?? false,
            'radius_meters' => $employee->office?->attendance_radius_meters,
        ]);
    }

    public function register(Request $request, FaceAttendanceController $faces): JsonResponse
    {
        $this->eligibleEmployee($request);

        return $faces->register($request);
    }

    public function attend(Request $request): JsonResponse
    {
        $data = $request->validate([
            'embeddings' => ['required', 'array', 'min:1', 'max:4'],
            'embeddings.*' => ['required', 'array', 'size:1024'],
            'embeddings.*.*' => ['required', 'numeric', 'between:-10,10'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['required', 'numeric', 'min:0', 'max:200'],
        ]);

        $user = $request->user();
        $employee = $this->eligibleEmployee($request);
        $office = $employee->office;
        abort_unless($office?->hasAttendanceLocation(), 422, 'Titik koordinat presensi kantor belum diatur oleh Super Admin.');
        abort_if((float) $data['accuracy'] > 100, 422, 'Akurasi lokasi terlalu rendah. Aktifkan GPS presisi tinggi lalu coba kembali.');

        $distance = $this->distanceMeters(
            (float) $office->attendance_latitude,
            (float) $office->attendance_longitude,
            (float) $data['latitude'],
            (float) $data['longitude'],
        );
        if ($distance > $office->attendance_radius_meters) {
            return response()->json([
                'message' => 'Lokasi Anda berada di luar radius kantor (jarak '.round($distance).' meter).',
                'distance_meters' => round($distance, 2),
                'allowed_radius_meters' => $office->attendance_radius_meters,
            ], 403);
        }
        $template = UserFaceTemplate::query()->where('user_id', $user->id)->first();

        abort_unless($template && $template->model_version !== 'human-faceres-pending', 422, 'Daftarkan wajah pada akun Anda terlebih dahulu.');

        $known = json_decode(Crypt::decryptString($template->encrypted_template), true, flags: JSON_THROW_ON_ERROR);
        $knownEmbeddings = isset($known['poses'])
            ? array_values($known['poses'])
            : (isset($known[0]) && is_numeric($known[0]) ? [$known] : $known);
        $candidateEmbeddings = array_map(
            fn (array $embedding) => array_map('floatval', $embedding),
            $data['embeddings'],
        );
        $similarity = $this->bestSimilarity($knownEmbeddings, $candidateEmbeddings);

        abort_if($similarity < 0.5, 403, 'Wajah belum cukup cocok (skor '.round($similarity * 100).'%). Pastikan pencahayaan cukup dan lihat ke kamera.');

        $now = now();
        $result = DB::transaction(function () use ($employee, $now, $request, $data, $distance): array {
            $workDate = $employee->resolveWorkDate();
            $attendance = Attendance::query()
                ->where('employee_id', $employee->id)
                ->whereDate('date', $workDate)
                ->lockForUpdate()
                ->first();

            $action = $this->nextAction($attendance);
            abort_if($action === 'completed', 409, 'Presensi masuk dan pulang hari ini sudah lengkap.');

            if (! $attendance) {
                $attendance = Attendance::query()->create([
                    'employee_id' => $employee->id,
                    'date' => $workDate,
                    'time_in' => $now->format('H:i:s'),
                    'status' => 'hadir',
                    'method' => 'face',
                ]);
            } elseif (! $attendance->time_in) {
                $attendance->update([
                    'time_in' => $now->format('H:i:s'),
                    'method' => 'face',
                ]);
            } else {
                $attendance->update([
                    'time_out' => $now->format('H:i:s'),
                    'method' => 'face',
                ]);
            }

            AttendancePunch::query()->create([
                'machine_user_id' => 'web-face-'.$employee->id,
                'employee_id' => $employee->id,
                'punch_at' => $now,
                'method' => 'face',
                'machine_serial' => 'WEB-FACE',
                'raw_data' => [
                    'source' => 'operational_attendance',
                    'ip' => $request->ip(),
                    'latitude' => (float) $data['latitude'],
                    'longitude' => (float) $data['longitude'],
                    'accuracy' => (float) $data['accuracy'],
                    'distance_meters' => round($distance, 2),
                ],
            ]);

            return [
                'action' => $action,
                'attendance' => $attendance->fresh(),
            ];
        });

        $label = $result['action'] === 'check_in' ? 'Absen masuk' : 'Absen pulang';

        return response()->json([
            'success' => true,
            'message' => $label.' berhasil pada '.$now->format('H:i:s').'.',
            'action' => $result['action'],
            'time_in' => $result['attendance']->time_in,
            'time_out' => $result['attendance']->time_out,
            'distance_meters' => round($distance, 2),
        ]);
    }

    private function eligibleEmployee(Request $request): Employee
    {
        $employee = $request->user()->employee;

        abort_unless($employee, 422, 'Akun Anda belum terhubung ke data karyawan.');
        abort_unless($employee->office?->usesWebFaceAttendance(), 403, 'Kantor Anda tidak menggunakan presensi Face ID website.');

        return $employee;
    }

    private function nextAction(?Attendance $attendance): string
    {
        if (! $attendance || ! $attendance->time_in) {
            return 'check_in';
        }

        return $attendance->time_out ? 'completed' : 'check_out';
    }

    private function bestSimilarity(array $knownEmbeddings, array $candidateEmbeddings): float
    {
        $best = 0.0;

        foreach ($knownEmbeddings as $known) {
            if (! is_array($known) || count($known) !== 1024) {
                continue;
            }

            foreach ($candidateEmbeddings as $candidate) {
                if (count($candidate) !== 1024) {
                    continue;
                }

                $sum = 0.0;
                foreach ($known as $index => $value) {
                    $sum += ((float) $value - (float) $candidate[$index]) ** 2;
                }

                if ($sum === 0.0) {
                    return 1.0;
                }

                $distance = round(2500 * $sum, 2);
                $normalized = (1 - (sqrt($distance) / 100) - 0.2) / 0.6;
                $best = max($best, round(max(0.0, min(1.0, $normalized)), 2));
            }
        }

        return $best;
    }

    private function distanceMeters(float $fromLatitude, float $fromLongitude, float $toLatitude, float $toLongitude): float
    {
        $earthRadius = 6371000;
        $latitudeDelta = deg2rad($toLatitude - $fromLatitude);
        $longitudeDelta = deg2rad($toLongitude - $fromLongitude);
        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($fromLatitude)) * cos(deg2rad($toLatitude)) * sin($longitudeDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
