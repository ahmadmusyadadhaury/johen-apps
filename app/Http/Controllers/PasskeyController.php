<?php

namespace App\Http\Controllers;

use App\Services\PasskeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasskeyController extends Controller
{
    public function registrationOptions(Request $request, PasskeyService $passkeys): JsonResponse
    {
        abort_unless($request->user()->employee, 403, 'Akun Anda belum terhubung ke data karyawan.');
        return response()->json($passkeys->registrationOptions($request->user()));
    }

    public function register(Request $request, PasskeyService $passkeys): JsonResponse
    {
        $credential = $passkeys->finishRegistration($request->user(), $request->all());
        return response()->json(['success' => true, 'message' => 'Passkey berhasil didaftarkan.']);
    }

    public function authenticationOptions(Request $request, PasskeyService $passkeys): JsonResponse
    {
        abort_unless($request->user()->employee, 403, 'Akun Anda belum terhubung ke data karyawan.');
        return response()->json($passkeys->authenticationOptions($request->user()));
    }
}
