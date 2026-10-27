<?php

namespace App\Http\Controllers;

use App\Facades\FileVault;
use App\Models\ManualBook;
use Illuminate\Http\Response;

class ManualBookFileController extends Controller
{
    public function show(ManualBook $manualBook, string $type): Response
    {
        abort_unless(in_array($type, ['thumbnail', 'pdf'], true), 404);

        $path = $type === 'pdf' ? $manualBook->file_pdf : $manualBook->thumbnail;
        abort_if(! $path, 404);

        $content = FileVault::get('public', $path);
        abort_if($content === null, 404);

        $mimeType = $type === 'pdf'
            ? 'application/pdf'
            : (new \finfo(FILEINFO_MIME_TYPE))->buffer($content);

        if ($type === 'thumbnail') {
            abort_unless(is_string($mimeType) && str_starts_with($mimeType, 'image/'), 404);
        }

        $filename = $type === 'pdf'
            ? ($manualBook->nama ?: 'manual-book').' - '.($manualBook->versi ?: 'v1.0').'.pdf'
            : basename($path);

        return response($content, 200, [
            'Content-Type' => $mimeType ?: 'application/octet-stream',
            'Content-Disposition' => "inline; filename*=UTF-8''".rawurlencode($filename),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
