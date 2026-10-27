<?php

namespace App\Http\Controllers;

use App\Facades\FileVault;
use App\Models\TrainingSession;
use Illuminate\Http\Response;

class TrainingThumbnailController extends Controller
{
    public function show(TrainingSession $trainingSession): Response
    {
        abort_unless($trainingSession->is_published || auth()->user()?->canManageTraining() || (int) $trainingSession->dibuat_oleh === (int) auth()->id(), 404);
        abort_unless($trainingSession->thumbnail, 404);

        $content = FileVault::get('public', $trainingSession->thumbnail);
        abort_if($content === null, 404);

        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($content);
        abort_unless(is_string($mimeType) && str_starts_with($mimeType, 'image/'), 404);

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.basename($trainingSession->thumbnail).'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
