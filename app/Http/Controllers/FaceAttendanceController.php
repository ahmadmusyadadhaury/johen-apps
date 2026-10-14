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
        $template = UserFaceTemplate::where('user_id', $request->user()->id)->first();
        return response()->json(['registered' => $template !== null && $template->model_version !== 'human-faceres-pending']);
    }

    public function preflight(Request $request): JsonResponse
    {
        $eligibility = $this->attendanceEligibility($request->user(), Carbon::now());

        return response()->json([
            'can_attend' => $eligibility['message'] === null,
            'message' => $eligibility['message'] ?? 'Presensi dapat dilanjutkan. Silakan verifikasi wajah dan lokasi.',
            'meeting' => $eligibility['meeting'] ? [
                'title' => $eligibility['meeting']->title,
                'meeting_date' => $eligibility['meeting']->meeting_date->toDateString(),
                'starts_at' => $eligibility['meeting']->start_time?->format('H:i'),
                'ends_at' => $eligibility['meeting']->end_time?->format('H:i'),
            ] : null,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pose' => 'sometimes|required|in:front,right,left,up',
            'complete' => 'sometimes|boolean',
            'embeddings' => 'sometimes|required|array|min:1|max:5',
            'embeddings.*' => 'required|array|size:1024',
            'embeddings.*.*' => 'required|numeric|between:-10,10',
            'embedding' => 'sometimes|required|array|size:1024',
            'embedding.*' => 'required|numeric|between:-10,10',
        ]);
        if (isset($data['pose'])) {
            $userId = $request->user()->id;
            $result = DB::transaction(function () use ($userId, $data) {
                $template = UserFaceTemplate::where('user_id', $userId)->lockForUpdate()->first();
                if ($template && $template->model_version !== 'human-faceres-pending') {
                    return ['conflict' => true];
                }

                $poses = [];
                if ($data['pose'] !== 'front') {
                    if (!$template) {
                        return ['missing_front' => true];
                    }
                    $payload = json_decode(Crypt::decryptString($template->encrypted_template), true, flags: JSON_THROW_ON_ERROR);
                    $poses = $payload['poses'] ?? [];
                }
                $poses[$data['pose']] = array_map('floatval', $data['embedding']);
                $requiredPoses = ['front', 'right', 'left', 'up'];
                $complete = (bool) ($data['complete'] ?? false);
                if ($complete && array_diff($requiredPoses, array_keys($poses)) !== []) {
                    throw ValidationException::withMessages(['pose' => 'Semua sudut wajah harus tersimpan sebelum pendaftaran selesai.']);
                }
                $templateData = [
                    'encrypted_template' => Crypt::encryptString(json_encode(['format' => 'human-faceres-poses-v1', 'poses' => $poses], JSON_THROW_ON_ERROR)),
                    'model_version' => $complete ? 'human-faceres-poses-v1' : 'human-faceres-pending',
                ];
                if ($template) {
                    $template->update($templateData);
                } else {
                    UserFaceTemplate::create(['user_id' => $userId] + $templateData);
                }

                return ['complete' => $complete, 'count' => count($poses)];
            });

            if (!empty($result['conflict'])) {
                return response()->json(['message' => 'Wajah sudah terdaftar pada akun ini.'], 409);
            }
            if (!empty($result['missing_front'])) {
                throw ValidationException::withMessages(['pose' => 'Pendaftaran harus dimulai dari posisi menghadap kamera.']);
            }

            return response()->json([
                'success' => true,
                'registered' => $result['complete'],
                'message' => $result['complete']
                    ? 'Semua sudut tersimpan. Face ID berhasil didaftarkan.'
                    : 'Sudut tersimpan ('.$result['count'].'/4). Silakan lanjutkan ke sudut berikutnya.',
            ]);
        }

        $templates = $data['embeddings'] ?? (isset($data['embedding']) ? [$data['embedding']] : []);
        if ($templates === []) {
            throw ValidationException::withMessages(['embeddings' => 'Data wajah tidak ditemukan. Silakan ulangi pendaftaran.']);
        }
        $templates = array_map(fn (array $embedding) => array_map('floatval', $embedding), $templates);
        try {
            UserFaceTemplate::create([
                'user_id' => $request->user()->id,
                'encrypted_template' => Crypt::encryptString(json_encode($templates, JSON_THROW_ON_ERROR)),
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
            'embeddings' => 'sometimes|required|array|min:1|max:4',
            'embeddings.*' => 'required|array|size:1024',
            'embeddings.*.*' => 'required|numeric|between:-10,10',
            'embedding' => 'sometimes|required|array|size:1024',
            'embedding.*' => 'required|numeric|between:-10,10',
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
        // Existing rows contain one flat 1024-value embedding. New rows contain
        // a list of embeddings so the best valid camera sample can be matched.
        $knownEmbeddings = isset($known['poses'])
            ? array_values($known['poses'])
            : (isset($known[0]) && is_numeric($known[0]) ? [$known] : $known);
        $candidateEmbeddings = $data['embeddings'] ?? (isset($data['embedding']) ? [$data['embedding']] : []);
        if ($candidateEmbeddings === []) {
            throw ValidationException::withMessages(['embeddings' => 'Data wajah tidak ditemukan. Silakan ulangi verifikasi.']);
        }
        $candidateEmbeddings = array_map(fn (array $embedding) => array_map('floatval', $embedding), $candidateEmbeddings);
        $similarity = $this->bestSimilarity($knownEmbeddings, $candidateEmbeddings);
        abort_if($similarity < 0.5, 403, 'Wajah belum cukup cocok (skor '.round($similarity * 100).'%). Hadap kamera, pastikan pencahayaan cukup, atau minta Super Admin mereset pendaftaran wajah.');
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

        // Match Human 3.3.6 normalization: multiply squared Euclidean distance
        // before taking its root, then normalize to the documented [0.2, 0.8]
        // similarity range. Multiplying sqrt(sum) directly by 25 incorrectly
        // clamps almost every real comparison to 20%.
        if ($sum === 0.0) {
            return 1.0;
        }

        $distance = round(25 * $sum, 2);
        $normalized = (1 - (sqrt($distance) / 100) - 0.2) / (0.8 - 0.2);

        return round(max(0.0, min(1.0, $normalized)), 2);
    }

    private function bestSimilarity(array $knownEmbeddings, array $candidateEmbeddings): float
    {
        $best = 0.0;
        foreach ($knownEmbeddings as $known) {
            if (!is_array($known) || count($known) !== 1024) {
                continue;
            }
            foreach ($candidateEmbeddings as $candidate) {
                if (!is_array($candidate) || count($candidate) !== 1024) {
                    continue;
                }
                $best = max($best, $this->similarity($known, $candidate));
            }
        }

        return $best;
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

        $faceTemplate = UserFaceTemplate::where('user_id', $user->id)->first();
        if (!$faceTemplate || $faceTemplate->model_version === 'human-faceres-pending') {
            return ['meeting' => $meeting, 'employee' => $employee, 'message' => 'Presensi belum bisa dilakukan. Daftarkan wajah pada akun Anda terlebih dahulu.'];
        }

        return ['meeting' => $meeting, 'employee' => $employee, 'message' => null];
    }
}
