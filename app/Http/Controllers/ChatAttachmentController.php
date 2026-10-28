<?php

namespace App\Http\Controllers;

use App\Models\DirectMessage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatAttachmentController extends Controller
{
    public function show(DirectMessage $directMessage): StreamedResponse
    {
        abort_unless(
            (int) $directMessage->sender_id === (int) auth()->id()
                || (int) $directMessage->recipient_id === (int) auth()->id(),
            404,
        );
        abort_unless($directMessage->attachment_path && Storage::disk('local')->exists($directMessage->attachment_path), 404);

        $mime = (string) $directMessage->attachment_mime;
        $name = basename((string) $directMessage->attachment_name) ?: 'lampiran';

        if (str_starts_with($mime, 'image/')) {
            return Storage::disk('local')->response($directMessage->attachment_path, $name, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $name).'"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ]);
        }

        return Storage::disk('local')->download($directMessage->attachment_path, $name, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
