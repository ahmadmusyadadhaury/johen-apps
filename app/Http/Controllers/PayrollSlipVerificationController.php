<?php

namespace App\Http\Controllers;

use App\Models\PayrollDetail;
use App\Models\UserFaceTemplate;
use App\Notifications\PayrollSlipVerificationCode;
use App\Services\PayrollSlipAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class PayrollSlipVerificationController extends Controller
{
    public function show(Request $request, PayrollDetail $detail, PayrollSlipAccess $access): RedirectResponse
    {
        $user = $request->user();
        $access->authorize($user, $detail);
        $action = $request->query('action', 'download') === 'view' ? 'view' : 'download';

        if ($access->isVerified($user, $detail)) {
            return $this->continueToSlip($detail, $action);
        }

        session()->flash('open_payroll_verification', ['detail_id' => $detail->id, 'action' => $action]);
        return redirect()->route('hris.informasi-saya');
    }

    public function faceChallenge(Request $request, PayrollDetail $detail, PayrollSlipAccess $access): JsonResponse
    {
        $user = $request->user();
        $access->authorize($user, $detail);
        $template = UserFaceTemplate::where('user_id', $user->id)->first();
        abort_unless($template && $template->model_version !== 'human-faceres-pending', 422, 'Face ID belum terdaftar pada akun ini.');
        $challenge = bin2hex(random_bytes(32));
        session()->put($this->challengeKey($detail), $challenge);
        return response()->json(['challenge' => $challenge]);
    }

    public function sendEmailCode(Request $request, PayrollDetail $detail, PayrollSlipAccess $access): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $access->authorize($user, $detail);
        $action = $request->input('action') === 'view' ? 'view' : 'download';

        if (! $user->email || ! $user->hasVerifiedEmail()) {
            if ($request->expectsJson()) return response()->json(['message' => 'Tambahkan dan verifikasi email melalui menu Profil terlebih dahulu.'], 422);
            return back()->withErrors(['email' => 'Tambahkan dan verifikasi email melalui menu Profil terlebih dahulu.']);
        }

        $key = $this->emailCodeKey($user->id, $detail->id);
        $limiterKey = 'payroll-slip-email:'.$user->id;
        if (RateLimiter::tooManyAttempts($limiterKey, 3)) {
            if ($request->expectsJson()) return response()->json(['message' => 'Terlalu banyak permintaan kode. Coba lagi setelah '.RateLimiter::availableIn($limiterKey).' detik.'], 429);
            return back()->withErrors(['email' => 'Terlalu banyak permintaan kode. Coba lagi setelah '.RateLimiter::availableIn($limiterKey).' detik.']);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($key, ['hash' => Hash::make($code), 'attempts' => 0], now()->addMinutes(5));

        try {
            $user->notify(new PayrollSlipVerificationCode($code, $detail->payrollImport->periode));
        } catch (\Throwable $exception) {
            Cache::forget($key);
            report($exception);

            if ($request->expectsJson()) return response()->json(['message' => 'Email verifikasi belum dapat dikirim. Periksa pengaturan email atau gunakan Face ID.'], 500);
            return back()->withErrors(['email' => 'Email verifikasi belum dapat dikirim. Periksa pengaturan email atau gunakan Face ID.']);
        }

        RateLimiter::hit($limiterKey, 600);

        $message = 'Kode verifikasi sudah dikirim ke '.$this->maskEmail($user->email).'.';
        if ($request->expectsJson()) return response()->json(['message' => $message]);
        return redirect()->route('payroll.slip.verify', ['detail' => $detail, 'action' => $action])->with('email_code_sent', $message);
    }

    public function verifyEmailCode(Request $request, PayrollDetail $detail, PayrollSlipAccess $access): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $access->authorize($user, $detail);
        $data = $request->validate(['code' => ['required', 'digits:6'], 'action' => ['required', 'in:view,download']]);
        $key = $this->emailCodeKey($user->id, $detail->id);
        $codeData = Cache::get($key);

        if (! $codeData || ($codeData['attempts'] ?? 0) >= 5) {
            Cache::forget($key);

            if ($request->expectsJson()) return response()->json(['message' => 'Kode kedaluwarsa atau percobaan sudah habis. Minta kode baru.'], 422);

            return back()->withErrors(['code' => 'Kode kedaluwarsa atau percobaan sudah habis. Minta kode baru.']);
        }

        if (! Hash::check($data['code'], $codeData['hash'])) {
            $codeData['attempts']++;
            Cache::put($key, $codeData, now()->addMinutes(5));

            if ($request->expectsJson()) return response()->json(['message' => 'Kode verifikasi tidak sesuai.'], 422);
            return back()->withErrors(['code' => 'Kode verifikasi tidak sesuai.']);
        }

        Cache::forget($key);
        $access->markVerified($user, $detail);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Email berhasil diverifikasi.', 'redirect_url' => route($data['action'] === 'view' ? 'payroll.view-pdf' : 'payroll.download-pdf', $detail)]);
        }
        return $this->continueToSlip($detail, $data['action']);
    }

    public function verifyFace(Request $request, PayrollDetail $detail, PayrollSlipAccess $access): JsonResponse
    {
        $user = $request->user();
        $access->authorize($user, $detail);
        $data = $request->validate([
            'challenge' => ['required', 'string', 'size:64'],
            'embeddings' => ['required', 'array', 'min:1', 'max:4'],
            'embeddings.*' => ['required', 'array', 'size:1024'],
            'embeddings.*.*' => ['required', 'numeric', 'between:-10,10'],
            'action' => ['required', 'in:view,download'],
        ]);

        $challengeKey = $this->challengeKey($detail);
        $expectedChallenge = session()->pull($challengeKey);
        abort_unless(is_string($expectedChallenge) && hash_equals($expectedChallenge, $data['challenge']), 419, 'Sesi verifikasi kedaluwarsa. Muat ulang halaman dan coba lagi.');

        $template = UserFaceTemplate::where('user_id', $user->id)->first();
        abort_unless($template && $template->model_version !== 'human-faceres-pending', 422, 'Face ID belum terdaftar pada akun ini.');

        $known = json_decode(Crypt::decryptString($template->encrypted_template), true, flags: JSON_THROW_ON_ERROR);
        $knownEmbeddings = isset($known['poses'])
            ? array_values($known['poses'])
            : (isset($known[0]) && is_numeric($known[0]) ? [$known] : $known);
        $candidates = array_map(fn (array $embedding) => array_map('floatval', $embedding), $data['embeddings']);

        abort_if($this->bestSimilarity($knownEmbeddings, $candidates) < 0.5, 403, 'Wajah belum cocok. Hadap kamera dengan pencahayaan cukup, lalu coba lagi.');

        $access->markVerified($user, $detail);

        return response()->json([
            'message' => 'Face ID berhasil diverifikasi.',
            'redirect_url' => route($data['action'] === 'view' ? 'payroll.view-pdf' : 'payroll.download-pdf', $detail),
        ]);
    }

    private function continueToSlip(PayrollDetail $detail, string $action): RedirectResponse
    {
        return redirect()->route($action === 'view' ? 'payroll.view-pdf' : 'payroll.download-pdf', $detail);
    }

    private function bestSimilarity(array $knownEmbeddings, array $candidateEmbeddings): float
    {
        $best = 0.0;
        foreach ($knownEmbeddings as $known) {
            if (! is_array($known) || count($known) !== 1024) continue;
            foreach ($candidateEmbeddings as $candidate) {
                if (! is_array($candidate) || count($candidate) !== 1024) continue;
                $sum = 0.0;
                foreach ($known as $index => $value) $sum += ((float) $value - (float) $candidate[$index]) ** 2;
                // Human 3.3.6 calculates distance as round(100 * 25 * sum, 2).
                // The previous factor of 25 made distances 10x too small, so
                // unrelated faces could receive an inflated match score.
                $distance = round(2500 * $sum, 2);
                $similarity = $sum === 0.0 ? 1.0 : max(0.0, min(1.0, (1 - (sqrt($distance) / 100) - 0.2) / 0.6));
                $best = max($best, round($similarity, 2));
            }
        }

        return $best;
    }

    private function challengeKey(PayrollDetail $detail): string
    {
        return 'payroll_slip_face_challenge.'.$detail->id;
    }

    private function emailCodeKey(int $userId, int $detailId): string
    {
        return 'payroll_slip_email_code.'.$userId.'.'.$detailId;
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($name, 0, 1);

        return $visible.str_repeat('*', max(mb_strlen($name) - 1, 2)).'@'.$domain;
    }
}
