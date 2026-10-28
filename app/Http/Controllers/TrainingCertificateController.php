<?php

namespace App\Http\Controllers;

use App\Models\TrainingRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrainingCertificateController extends Controller
{
    public function show(Request $request, TrainingRegistration $registration): StreamedResponse
    {
        $user = $request->user();
        $registration->loadMissing('session');

        $canView = (int) $registration->employee_id === (int) $user->employee?->id
            || $user->canManageTraining()
            || (int) $registration->session?->dibuat_oleh === (int) $user->id;

        abort_unless($canView && $registration->status === 'attended', 404);
        abort_unless($registration->certificate_path && Storage::disk('local')->exists($registration->certificate_path), 404);

        $name = basename((string) $registration->certificate_name) ?: 'sertifikat';
        $mime = in_array($registration->certificate_mime, ['application/pdf', 'image/jpeg', 'image/png'], true)
            ? $registration->certificate_mime
            : 'application/octet-stream';
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return Storage::disk('local')->response($registration->certificate_path, $name, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ], $disposition);
    }
}
